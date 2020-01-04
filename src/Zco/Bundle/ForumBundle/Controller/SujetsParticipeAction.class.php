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

use Zco\Bundle\ForumBundle\Controller\BaseController;

/**
 * Controleur pour le listage des sujets dans auxquels un membre a
 * participé.
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class SujetsParticipeAction extends BaseController
{
	public function execute()
	{
		// Inclusion des modèles
		include(dirname(__FILE__).'/../modeles/membres.php');
		include(dirname(__FILE__).'/../modeles/forums.php');

		$InfosUtilisateur = InfosUtilisateur($_GET['id']);
		if(empty($InfosUtilisateur))
			return redirect(123, '/forum/', MSG_ERROR);

		Page::$titre = 'Liste des sujets auxquels '.htmlspecialchars($InfosUtilisateur['utilisateur_pseudo']).' a participé';
		Page::$description = 'Activité de '.htmlspecialchars($InfosUtilisateur['utilisateur_pseudo']).' sur les forums';
		$titre = $InfosUtilisateur['utilisateur_pseudo'];

		if(!empty($_GET['id2']))
		{
			$InfosCategorie = InfosCategorie($_GET['id2']);
			if(empty($InfosCategorie) || !verifier('voir_sujets', $_GET['id2']))
			{
				return redirect(50, 'sujets-participe-'.$_GET['id'].'.html', MSG_ERROR);
			}
			Page::$titre .= ' - '.$InfosCategorie['cat_nom'];
			$titre .= '-'.$InfosCategorie['cat_nom'];
		}
		else
		{
			$InfosCategorie = null;
		}

		// On compte les sujets
		$CompterSujetsParticipe = CompterSujetsParticipe($_GET['id']);

		$nbSujetsParPage = 30;
		$NombreDePages = ceil($CompterSujetsParticipe / $nbSujetsParPage);
			
		zCorrecteurs::VerifierFormatageUrl($titre, true, true, $NombreDePages);
		$_GET['p'] = !empty($_GET['p']) && is_numeric($_GET['p']) ? $_GET['p'] : $NombreDePages;
		if ($_GET['p'] > 1)
		{
			Page::$titre .= ' - Page '.(int)$_GET['p'];
		}

		// On récupère la liste des numéros des pages.

		$tableau_pages = liste_pages($_GET['p'], $NombreDePages, $CompterSujetsParticipe, $nbSujetsParPage, 'sujets-participe-'.$_GET['id'].'-p%s.html');
		$debut = ($NombreDePages-$_GET['p']) * $nbSujetsParPage;

		// On récupère les sujets.
		$ListerSujetsParticipe = ListerSujetsParticipe($_GET['id'], $debut, $nbSujetsParPage);

		// Appel de la fonction lu / non-lu et de la fonction trouver dernier message non lu.
		$Lu = array();
		$Pages = array();
		if($ListerSujetsParticipe)
		{
			$derniere_lecture = DerniereLecture($_SESSION['id']);
			foreach($ListerSujetsParticipe as $clef => $valeur)
			{
				$EnvoiDesInfos = array(
					'lunonlu_utilisateur_id' => $_SESSION['id'],
					'lunonlu_sujet_id' => $valeur['sujet_id'],
					'lunonlu_message_id' => $valeur['regardeur_dernier_message_lu'],
					'lunonlu_participe' => $valeur['regardeur_participe'],
					'sujet_dernier_message' => $valeur['sujet_dernier_message'],
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

		// Inclusion de la vue
		fil_ariane('Voir les sujets auxquels un membre a participé');
		$this->get('zco_vitesse.resource_manager')->requireResource(
		    '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css'
		);
		
		return render_to_response(array(
			'InfosUtilisateur' => $InfosUtilisateur,
			'InfosCategorie' => $InfosCategorie,
			'tableau_pages' => $tableau_pages,
			'CompterSujetsParticipe' => $CompterSujetsParticipe,
			'ListerSujetsParticipe' => $ListerSujetsParticipe,
			'Lu' => $Lu,
			'Pages' => $Pages,
		));
	}
}
