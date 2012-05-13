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

/**
 * Contrôleur gérant l'affichage des forums d'une catégorie.
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class CategorieAction extends ForumActions
{
	public function execute()
	{
		//Inclusion du modèle
		include(dirname(__FILE__).'/../modeles/categories.php');
		include(dirname(__FILE__).'/../modeles/membres.php');

		//Si aucune catégorie n'a été spécifiée
		if(empty($_GET['id']) || !is_numeric($_GET['id']))
		{
			return redirect(64, '/forum/', MSG_ERROR);
		}
		//Si elle n'existe pas on si on n'a pas le droit de la voir
		$InfosCategorie = InfosCategorie($_GET['id']);

		$droit = !empty($_GET['trash']) ? 'corbeille_sujets' : 'voir_sujets';
		if(empty($InfosCategorie) || !verifier($droit, $_GET['id']))
		{
			return redirect(65, '/forum/', MSG_ERROR);
		}

		zCorrecteurs::VerifierFormatageUrl($InfosCategorie['cat_nom'], true);
		
		//Mise à jour de la position sur le site.
		\Doctrine_Core::getTable('Online')->updateUserPosition($_SESSION['id'], 'ZcoForumBundle:categorie', $_GET['id']);

		//On récupère la catégorie et ses forums.
		$ListerUneCategorie = ListerCategoriesForum($InfosCategorie);
		Page::$robots = 'noindex,follow';

		//Appel de la fonction lu / non-lu
		if ($ListerUneCategorie)
		{
			$derniere_lecture = DerniereLecture($_SESSION['id']);
			$Lu = array();
			foreach ($ListerUneCategorie as $cat)
			{
				//Si le forum est vide, l'image lu/non-lu sera une ampoule blanche.
				if ($cat['cat_last_element'] == 0)
				{
					$Lu[$cat['cat_id']] = array(
						'image' => 'lightbulb_off',
						'title' => 'Pas de nouvelles réponses, jamais participé'
					);
				}
				else
				{
					$Lu[$cat['cat_id']] = LuNonluCategorie(array(
						'lunonlu_utilisateur_id'   => $cat['lunonlu_utilisateur_id'],
						'lunonlu_sujet_id'         => $cat['lunonlu_sujet_id'],
						'lunonlu_message_id'       => $cat['lunonlu_message_id'],
						'lunonlu_participe'        => $cat['lunonlu_participe'],
						'sujet_dernier_message'    => $cat['message_id'],
						'date_dernier_message'     => $cat['message_timestamp'],
						'derniere_lecture_globale' => $derniere_lecture,
					));
				}
			}
		}

		//Pas de mise en cache / TODO : utiliser la réponse.
		header('Pragma: no-cache');
		header('Cache-control: no-cache');

		//Inclusion de la vue
		if(!empty($_GET['trash']))
			fil_ariane($_GET['id'], 'Liste des forums de la corbeille');
		else
			fil_ariane($_GET['id'], 'Liste des forums');
		
		$this->get('zco_vitesse.resource_manager')->requireResource(
		    '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css'
		);
		
		return render_to_response(array(
			'InfosCategorie' => $InfosCategorie,
			'ListerUneCategorie' => $ListerUneCategorie,
			'Lu' => $Lu,
		));
	}
}
