<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2018 Corrigraphie
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

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Affichage des sujets en favoris, en coup de coeur, etc
 *
 * @author Original Barbatos <barbatos@f1m.fr>
 */
class SuiviAction extends ForumActions
{
	public function execute()
	{
        if (!verifier('connecte')) {
            throw new AccessDeniedHttpException();
        }

		//Inclusion des modèles
		include(__DIR__.'/../modeles/forums.php');
		include(__DIR__.'/../modeles/categories.php');
		include(__DIR__.'/../modeles/membres.php');

		$CompterSujets = CompterSujets(NULL);
		$nbSujetsParPage = 30;
		$NombreDePages = ceil($CompterSujets / $nbSujetsParPage);
		$_GET['p'] = !empty($_GET['p']) && is_numeric($_GET['p']) ? $_GET['p'] : $NombreDePages;

		//Seule la page 1 de chaque forum est indexée
		if(empty($_GET['p']))
			Page::$robots = 'index,follow';
		else
			Page::$robots = 'noindex,follow';

		$tableau_pages = liste_pages($_GET['p'], $NombreDePages, $CompterSujets, $nbSujetsParPage, '', true);
		$debut = ($NombreDePages-$_GET['p']) * $nbSujetsParPage;
		list($ListerSujets, $Tags) = ListerSujets($debut, $nbSujetsParPage);

		$derniere_lecture = DerniereLecture($_SESSION['id']);
		$Lu = $Pages = array();

		if($ListerSujets)
		{
			foreach($ListerSujets as $clef => $valeur)
			{
				//Appel de la fonction lu / non-lu et de la fonction trouver dernier message non lu.
				$EnvoiDesInfos = array(
				'lunonlu_utilisateur_id' => $valeur['lunonlu_utilisateur_id'],
				'lunonlu_sujet_id' => $valeur['lunonlu_sujet_id'],
				'lunonlu_message_id' => $valeur['lunonlu_message_id'],
				'lunonlu_participe' => $valeur['lunonlu_participe'],
				'sujet_dernier_message' => $valeur['message_id'],
				'date_dernier_message' => $valeur['message_timestamp'],
				'derniere_lecture_globale' => $derniere_lecture
				);
				$Lu[$clef] = LuNonluForum($EnvoiDesInfos);

				// Liste des pages
				$nbMessagesParPage = 20;
				$NombreDePagesSujet = ceil(($valeur['sujet_reponses']+1) / $nbMessagesParPage);
				$Pages[$clef] = liste_pages(-1, $NombreDePagesSujet, $valeur['sujet_reponses'], $nbMessagesParPage, 'sujet-'.$valeur['sujet_id'].'-p%s-'.rewrite($valeur['sujet_titre']).'.html');
			}
		}

		fil_ariane('Suivi des sujets');
		$this->get('zco_core.resource_manager')->requireResources(array(
		    '@ZcoCoreBundle/Resources/public/css/zcode.css',
		    '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css',
		));
		$this->get('zco_core.resource_manager')->requireResource('@ZcoCoreBundle/Resources/public/js/messages.js');
		
		return render_to_response(array(
			'CompterSujets' => $CompterSujets,
			'ListerSujets' => $ListerSujets,
			'Tags' => $Tags,
			'tableau_pages' => $tableau_pages,
			'Pages' => $Pages,
			'Lu' => $Lu,
		));
	}
}
