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

namespace Zco\Bundle\ForumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class BaseController extends Controller
{
	public function __construct()
	{
	    $resourceManager = \Container::getService('zco_vitesse.resource_manager');
		$resourceManager->addFeed(
		    '/forum/messages-flux.html', 
		    array('title' => 'Derniers messages du forum')
		);
		$resourceManager->requireResource(
		    '@ZcoForumBundle/Resources/public/css/forum.css'
		);
	}

	protected function initSujet()
	{
		include(dirname(__FILE__).'/../modeles/sujets.php');

		//Compatibilité
		if(!isset($_GET['s'])) $_GET['s'] = $_GET['id'];
		if(empty($_GET['id'])) $_GET['id'] = $_GET['s'];

		//--- Récupération des infos sur le sujet ---
		if(empty($_GET['id']) || !is_numeric($_GET['id']))
			return array(redirect(45, '/forum/', MSG_ERROR), null);
		else
		{
			$InfosSujet = InfosSujet($_GET['id']);
			$InfosForum = InfosCategorie($InfosSujet['sujet_forum_id']);
			if(empty($InfosSujet))
				return array(redirect(47, '/forum/', MSG_ERROR), null);
		}

		//--- Modification des balises méta ---
		\Page::$titre = htmlspecialchars($InfosSujet['sujet_titre']);

		return array($InfosSujet, $InfosForum);
	}
}
