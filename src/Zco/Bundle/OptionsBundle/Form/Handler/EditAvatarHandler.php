<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2020 Corrigraphie
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

namespace Zco\Bundle\OptionsBundle\Form\Handler;

use Gaufrette\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Imagine\Image\Box;
use Imagine\Image\ImagineInterface;

/**
 * Gère la soumission du formulaire de changement d'avatar.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class EditAvatarHandler
{
	const AVATAR_CHANGED = 1;
	const AVATAR_DELETED = 2;
	const INTERNAL_ERROR = 3;
	const WRONG_FORMAT = 4;

	protected $request;
	protected $imagine;
	protected $filesystem;

	/**
	 * Constructeur.
	 *
	 * @param Request $request
     * @param ImagineInterface $imagine
     * @param Filesystem $filesystem
	 */
	public function __construct(Request $request, ImagineInterface $imagine, Filesystem $filesystem)
	{
		$this->request = $request;
		$this->imagine = $imagine;
		$this->filesystem = $filesystem;
	}
	
	/**
	 * Procède à la soumission du formulaire.
	 *
	 * @param  \Utilisateur $user L'utiliser à modifier
	 * @return boolean Le formulaire a-t-il été traité correctement ?
	 */
	public function process(\Utilisateur $user)
	{
		if ($this->request->getMethod() === 'POST')
		{
			return $this->onSuccess($user);
		}

		return false;
	}

	/**
	 * Action à effectuer lorsque le formulaire est valide.
	 *
	 * @param \Utilisateur $user L'entité liée au formulaire
	 */
	protected function onSuccess(\Utilisateur $user)
	{
		if ($this->request->request->has('delete'))
		{
            if ($this->filesystem->has('avatars/' . $user->getAvatar())) {
                $this->filesystem->delete('avatars/' . $user->getAvatar());
            }
			$user->setAvatar('');
			$user->save();

			return self::AVATAR_DELETED;
		}

		//Upload depuis le disque dur
		if ($this->request->files->has('avatar') && $this->request->files->get('avatar'))
		{
			$file = $this->request->files->get('avatar');
			if (!$file->isValid())
			{
				return self::INTERNAL_ERROR;
			}

			//Vérification de l'extension et du type mime.
			$mimetypes = array('image/jpeg', 'image/png', 'image/gif');
			if (!in_array($file->getMimeType(), $mimetypes))
			{
				return self::WRONG_FORMAT;
			}

			//Si l'utilisateur a déjà un avatar, on le supprime.
			if ($user->hasAvatar()) {
			    $this->filesystem->delete('avatars/' . $user->getAvatar());
			}

            //Redimensionnement de l'avatar si nécessaire afin de ne pas dépasser 100x100.
            $contents = file_get_contents($file->getPathname());
            $size = getimagesize($file->getPathname());
            if ($size[0] > 100 || $size[1] > 100) {
                $contents = (string) $this->imagine->load($contents)->thumbnail(new Box(100, 100));
            }

            //Déplacement du fichier temporaire vers le dossier des avatars.
            $filename = $user->getId() . '.' . $file->guessExtension();
            $destination = 'avatars/' . $filename;
			try {
                $this->filesystem->write($destination, $contents);
			} catch (\Exception $e) {
				return self::INTERNAL_ERROR;
			}

			//On termine en modifiant l'utilisateur pour lui lier son nouvel avatar.
			$user->setAvatar($filename);
			$user->save();

			return self::AVATAR_CHANGED;
		}

		return false;
	}
}