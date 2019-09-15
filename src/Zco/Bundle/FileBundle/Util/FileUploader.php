<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2019 Corrigraphie
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Zco\Bundle\FileBundle\Util;

use Gaufrette\FilesystemInterface;
use Imagine\Image\ImagineInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Zco\Bundle\FileBundle\Exception\UploadRejectedException;

/**
 * Classe découplée du reste facilitant l'envoi de fichiers. Elle est utilisée
 * dans le bundle actuel mais peut être réutilisée ailleurs pour stocker des
 * fichiers de différentes sources dans le gestionnaire de fichiers.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class FileUploader
{
    private $filesystem;
    private $imagine;

    private static $allowedMimeTypes = array(
        //.ogg
        'audio/ogg',
        'application/ogg',
        'audio/x-ogg',
        'application/x-ogg',

        //.mp3
        'audio/mpeg',
        'audio/x-mpeg',
        'audio/mp3',
        'audio/x-mp3',
        'audio/mpeg3',
        'audio/x-mpeg3',
        'audio/mpg',
        'audio/x-mpg',
        'audio/x-mpegaudio',

        //.pdf
        'application/pdf',
        'application/x-pdf',
        'application/acrobat',
        'applications/vnd.pdf',
        'text/pdf',
        'text/x-pdf',

        //.txt
        'text/plain',
        'application/txt',
        'text/anytext',
        'widetext/plain',
        'widetext/paragraph',

        //.rtf
        'application/rtf',
        'application/x-rtf',
        'text/rtf',
        'text/richtext',

        //.png
        'image/png',
        'application/png',
        'application/x-png',

        //.jpeg
        'image/pjg',
        'image/jpeg',

        //.gif
        'image/gif',
    );

    /**
     * Constructeur.
     *
     * @param FilesystemInterface $filesystem Le système de fichiers où stocker les fichiers
     * @param ImagineInterface $imagine
     */
    public function __construct(FilesystemInterface $filesystem, ImagineInterface $imagine)
    {
        $this->filesystem = $filesystem;
        $this->imagine = $imagine;
    }

    public function batchUpload(Request $request, array $options): BatchUploadResult
    {
        $result = new BatchUploadResult(count($request->files->get('file')));

        foreach ($request->files->get('file') as $uploadedFile) {
            /** @var UploadedFile $uploadedFile */
            //Si le fichier est invalide, il s'agit d'une erreur interne de PHP.
            if (!$uploadedFile->isValid()) {
                // On tente de déterminer de quelle erreur il s'agit pour faciliter
                // le rapport des erreurs et le débogage.
                switch ($uploadedFile->getError()) {
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        $message = 'fichier trop volumineux';
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $message = 'téléchargement échoué';
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        $message = 'aucun fichier trouvé';
                        break;
                    case UPLOAD_ERR_NO_TMP_DIR:
                    case UPLOAD_ERR_CANT_WRITE:
                    case UPLOAD_ERR_EXTENSION:
                        $message = 'erreur interne';
                        break;
                    default:
                        $message = null;
                }
                $result->addFailed($uploadedFile, $message);
                continue;
            }

            try {
                $file = $this->upload($uploadedFile, $options);
                $result->addSuccess($uploadedFile, $file['id']);
            } catch (UploadRejectedException $e) {
                $result->addFailed($uploadedFile, $e->getMessage());
            }
        }

        return $result;
    }

    /**
     * Enregistre un fichier dans le gestionnaire de fichiers.
     *
     * @param  UploadedFile $uploadedFile
     * @param  array $options
     * @return \File $file
     */
    public function upload(UploadedFile $uploadedFile, array $options)
    {
        //L'option spécifiant le créateur du fichier est obligatoire. Elle peut
        //éventuellement être modifiée lors de la propagation de l'événement mais
        //on assure à tous les observateurs qu'elle est déjà présente à la base.
        if (!array_key_exists('user_id', $options)) {
            throw new \InvalidArgumentException('You must specify the "user_id" option.');
        }

        // Vérification des quotas (données en octets).
        $usage = \Doctrine_Core::getTable('File')->getSpaceUsage($options['user_id']);
        $quota = verifier('fichiers_quota') * 1000 * 1000; // TODO: prendre en compte le vrai groupe
        if ($quota > -1 && $usage + $uploadedFile->getSize() > $quota) {
            throw new UploadRejectedException('dépassement de quota');
        }

        //Vérification des types MIME.
        if (!in_array($uploadedFile->getMimeType(), self::$allowedMimeTypes)) {
            throw new UploadRejectedException('fichier non reconnu');
        }

        //On crée en premier l'enregistrement représentant le fichier.
        $mime = explode('/', $uploadedFile->getMimeType(), 2);

        $file = new \File();
        $file['user_id'] = $options['user_id'];
        $file['name'] = substr($uploadedFile->getClientOriginalName(), 0, strrpos($uploadedFile->getClientOriginalName(), '.'));
        $file['extension'] = substr($uploadedFile->getClientOriginalName(), strrpos($uploadedFile->getClientOriginalName(), '.') + 1) ?: 'bin';
        $file['major_mime'] = $mime[0];
        $file['minor_mime'] = $mime[1];
        $file['size'] = $uploadedFile->getSize();
        $file['type'] = isset($options['type']) ? (int)$options['type'] : 0;

        //Une option permet de configurer si le fichier est décompté du quota de l'utilisateur.
        if (isset($options['quota_affected'])) {
            $file['quota_affected'] = (boolean)$options['quota_affected'];
        }

        //Si le fichier est une image on remplit les paramètres spécifiques.
        if ($file->isImage()) {
            $image = $this->imagine->open($uploadedFile->getPathname());
            $size = $image->getSize();
            $file['width'] = $size->getWidth();
            $file['height'] = $size->getHeight();
        }
        $file->save();

        //On peut maintenir définir le chemin vers le fichier.
        $file['path'] = 'fichiers/' . $file->getSubDirectory() . '/' . $file['id'] . '.' . $file['extension'];

        // Si le fichier est une image, on lui crée une première miniature. Celle-ci
        // sera utilisée dans les listes de fichiers, elle est donc systématiquement
        // créée après l'envoi du fichier.
        if ($file->isImage()) {
            $thumbnail = $image->thumbnail(new \Imagine\Image\Box(150, 80));
            $size = $thumbnail->getSize();
            $path = sys_get_temp_dir() . '/' . $file['id'] . '-' . $size->getWidth() . 'x' . $size->getHeight() . '.' . $file['extension'];

            $thumbnail->save($path);
            $thumbnail = null;

            $thumbnail = new \FileThumbnail();
            $thumbnail->File = $file;
            $thumbnail['width'] = $size->getWidth();
            $thumbnail['height'] = $size->getHeight();
            $thumbnail['size'] = filesize($path);
            $thumbnail['path'] = 'fichiers/min/' . $file->getSubdirectory() . '/' . $file['id'] . '.' . $file['extension'] . '/' . $file['id'] . '-' . $size->getWidth() . 'x' . $size->getHeight() . '.' . $file['extension'];
            $thumbnail->save();

            // On associe l'image principale en retour au fichier.
            $file['thumbnail_id'] = $thumbnail['id'];

            // On écrit cette miniature sur le système de fichiers.
            $this->filesystem->write($thumbnail->getRelativePath(), file_get_contents($path));
            unlink($path);
        }

        // Et on sauvegarde à nouveau le fichier !
        $file->save();

        // Et on écrit le fichier original sur le système de fichiers.
        $this->filesystem->write($file->getRelativePath(), file_get_contents($uploadedFile->getPathname()));
        unlink($uploadedFile->getPathname());

        return $file;
    }
}