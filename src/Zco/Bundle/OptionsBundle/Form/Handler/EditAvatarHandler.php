<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012 Corrigraphie
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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Imagine\Image\Box;

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
	
	/**
	 * Constructeur.
	 *
	 * @param Request $this->request
	 */
	public function __construct(Request $request)
	{
		$this->request = $request;
	}
	
	/**
	 * Procède à la soumission du formulaire.
	 *
	 * @param  Utilisateur $user L'utiliser à modifier
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
	 * @param Utilisateur $user L'entité liée au formulaire
	 */
	protected function onSuccess(\Utilisateur $user)
	{
		if ($this->request->request->has('delete'))
		{
			unlink(BASEPATH.'/web/'.$user->getAvatar());
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

			//Si l'utilisateur a déjà un avatar local, on le supprime.
			if ($user->hasLocalAvatar())
			{
				unlink(BASEPATH.'/web/'.$user->getAvatar());
			}

			//Déplacement du fichier temporaire vers le dossier des avatars.
			$extension = $file->guessExtension();
			$path = array(BASEPATH.'/web/uploads/avatars', $user->getId().'.'.$extension);
			try
			{
				$file = $file->move($path[0], $path[1]);
			}
			catch (FileException $e)
			{
				return self::INTERNAL_ERROR;
			}

			//Redimensionnement de l'avatar si nécessaire afin de ne pas dépasser 100x100.
			$size = getimagesize($path[0].'/'.$path[1]);
			if ($size[0] > 100 || $size[1] > 100)
			{
				$this->get('imagine')
					->open($path[0].'/'.$path[1])
					->thumbnail(new Box(100, 100))
					->save($path[0].'/'.$path[1]);
			}
			
			//On termine en modifiant l'utilisateur pour lui lier son nouvel avatar.
			$user->setAvatar($path[1]);
			$user->save();

			return self::AVATAR_CHANGED;
		}

		return false;
	}
}