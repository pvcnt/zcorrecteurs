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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Contrôleur gérant l'affichage de la liste des candidatures d'un membre
 *
 * @author the_little <thelittle@zcorrecteurs.fr>
 */
class CandidaturesMembreAction extends Controller
{
	public function execute()
	{
		if ($_GET['id'])
		{
            if (!verifier('recrutements_voir_candidatures')) {
                throw new AccessDeniedHttpException();
            }
		    
			$membre = \Doctrine_Query::create()
				->select('u.pseudo')
				->from('Utilisateur u')
				->where('u.id = ?', $_GET['id'])
				->execute()
				->offsetGet(0);
			
			if (!$membre->id)
				throw new NotFoundHttpException();
			
			$vars = array('Membre' => $membre);
			$vars['Candidatures'] = Doctrine_Core::getTable('RecrutementCandidature')->ListerCandidaturesMembre($_GET['id']);
			
			//Inclusion de la vue
			fil_ariane('Liste des candidatures d\'un membre');
			
			return render_to_response($vars);
		}
		else
            throw new NotFoundHttpException();
	}
}
