<?php

use Zco\Bundle\Doctrine1Bundle\Migrations\AbstractMigration;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Migration des données de l'ancien table des uploads vers les nouvelles tables 
 * du module du gestionnaire de fichiers.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class Version20120331231734 extends AbstractMigration
{
	/**
	 * On vérifie d'abord que l'ancienne table des uploads existe bel et bien. 
	 * Si ce n'est pas le cas (installation locale récente), rien ne sert de 
	 * continuer !
	 *
	 * @param OutputInterface $output Pour afficher un retour à l'utilisateur
	 */
	public function preUp(OutputInterface $output)
	{
		$stmt = $this->dbh->prepare('SHOW TABLES');
		$stmt->execute();
		$exists = false;
		foreach ($stmt->fetchAll() as $row)
		{
			if ($row[0] === 'zcov2_uploads')
			{
				$exists = true;
				break;
			}
		}
		
		$this->skipIf(!$exists, 'No data about uploaded files to migrate');
	}
	
	public function up(OutputInterface $output)
	{
		$uploader   = $this->container->get('zco_file.uploader');
		$imagine    = $this->container->get('imagine');
		$filesystem = $this->container->get('gaufrette.uploads_filesystem');

		$stmt = $this->dbh->prepare('SELECT * FROM zcov2_uploads LEFT JOIN zcov2_categories ON upload_dossier = cat_id');
		$stmt->execute();
		$uploads = $stmt->fetchAll();
		$stmt->closeCursor();

		foreach ($uploads as $upload)
		{
			$folderName = $upload['cat_id'] == 20 ? 'racine' : rewrite($upload['cat_nom']);
			$relativePath = $upload['upload_dossier'] ? 
				'uploads/membres/'.$upload['upload_id_utilisateur'].'/'.$folderName.'/'.$upload['upload_chemin']
				: 'uploads/'.$upload['upload_chemin'];
			if (!is_file(BASEPATH.'/web/'.$relativePath))
			{
				echo $relativePath.' not found (#'.$upload['upload_id'].')'."\n";
				continue;
			}
			$uploadedFile = new File(BASEPATH.'/web/'.$relativePath);

			$mime = explode('/', $uploadedFile->getMimeType(), 2);

			$file = new \File();
			$file['user_id']	= $upload['upload_id_utilisateur'];
			$file['name']	   	= $upload['upload_nom'];
			$file['extension']  = substr($upload['upload_chemin'], strrpos($upload['upload_chemin'], '.') + 1) ?: 'bin';
			$file['major_mime'] = $mime[0];
			$file['minor_mime'] = $mime[1];
			$file['size']	   	= $uploadedFile->getSize();
			$file['type']	   	= 3;

			//Si le fichier est une image on remplit les paramètres spécifiques.
			if ($file->isImage())
			{
				try
				{
					$image = $imagine->open($uploadedFile->getPathname());
				}
				catch (\InvalidArgumentException $e)
				{
					echo 'Weird file '.$relativePath.' skipped (#'.$upload['upload_id'].')'."\n";
					continue;
				}

				$size  			= $image->getSize();
				$file['width']  = $size->getWidth();
				$file['height'] = $size->getHeight();
			}
			$file->save();

			//On redéfinit sa « vraie » date de création. La date de modification 
			//reste maintenant, cela importe peu.
			$file->date = $upload['upload_date'];
			$file->save();

			//On peut maintenir définir le chemin vers le fichier.
			$file['path'] = substr($relativePath, 8);

			//Si le fichier est une image, on lui crée une première miniature. Celle-ci 
			//sera utilisée dans les listes de fichiers, elle est donc systématiquement 
			//créée après l'envoi du fichier.
			if ($file->isImage())
			{
				$thumbnail 	= $image->thumbnail(new \Imagine\Image\Box(150, 80));
				$size		= $thumbnail->getSize();
				$path 		= sys_get_temp_dir().'/'.$file['id'].'-'.$size->getWidth().'x'.$size->getHeight().'.'.$file['extension'];

				$thumbnail->save($path);
				unset($thumbnail);

				$thumbnail = new \FileThumbnail();
				$thumbnail->File	 = $file;
				$thumbnail['width']  = $size->getWidth();
				$thumbnail['height'] = $size->getHeight();
				$thumbnail['size']   = filesize($path);
				$thumbnail['path']   = 'fichiers/min/'.$file->getSubdirectory().'/'.$file['id'].'.'.$file['extension'].'/'.$file['id'].'-'.$size->getWidth().'x'.$size->getHeight().'.'.$file['extension'];
				$thumbnail->save();

				//On associe l'image principale en retour au fichier.
				$file['thumbnail_id'] = $thumbnail['id'];

				//On écrit cette miniature sur le système de fichiers.
				$filesystem->write($thumbnail->getRelativePath(), file_get_contents($path));
				unlink($path);
				
				unset($thumbnail);
			}

			//Et on sauvegarde à nouveau le fichier !
			$file->save();

			//On mémorise maintenant l'association entre le fichier et les éléments (sujet, article, etc.).
			if ($upload['upload_element'])
			{
				$entityClass = null;
				if ($upload['upload_dossier'] == 74)
				{
					$entityClass = 'Blog';
				}
				elseif ($upload['upload_dossier'] == 79)
				{
					$entityClass = 'ForumSujet';
				}
				elseif ($upload['upload_dossier'] == 85)
				{
					$entityClass = 'TrackerTicket';
				}
				elseif ($upload['upload_dossier'] == 86)
				{
					$entityClass = 'Quiz';
				}

				if (!$entityClass || !\Doctrine_core::getTable($entityClass)->find($upload['upload_element']))
				{
					continue;
				}

				$usage = new \FileUsage();
				$usage['file_id'] = $file['id'];
				$usage['entity_class'] = $entityClass;
				$usage['entity_id']	= $upload['upload_element'];
				$usage->save();
				
				unset($usage);
			}
			
			unset($uploadedFile);
			unset($file);
		}
	}

	public function down(OutputInterface $output)
	{
		$this->addSql("TRUNCATE TABLE zcov2_file");
		$this->addSql("TRUNCATE TABLE zcov2_file_license");
		$this->addSql("TRUNCATE TABLE zcov2_file_thumbnail");
		$this->addSql("TRUNCATE TABLE zcov2_file_usage");
		$this->addSql("TRUNCATE TABLE zcov2_license");
	}
}