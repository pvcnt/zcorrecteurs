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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zco\Bundle\BlogBundle\Domain\BlogDAO;

/**
 * Contrôleur gérant l'affichage de l'historique de validation.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class ValidationAction extends BlogActions
{
	public function execute()
	{
        if (!verifier('blog_voir_historique')) {
            throw new AccessDeniedHttpException();
        }

		//Si on a bien demandé à voir un billet
		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$ret = $this->initBillet();
			if ($ret instanceof Response)
				return $ret;
			Page::$titre .= ' - Historique des propositions';

			//Si on a bien le droit de voir ce billet
			if($this->verifier_voir && ($this->autorise == true || verifier('blog_voir_historique')))
			{

				$this->Historique = BlogDAO::HistoriqueValidation($_GET['id']);

				//Inclusion de la vue
				fil_ariane($this->InfosBillet['cat_id'], array(
					htmlspecialchars($this->InfosBillet['version_titre']) => 'admin-billet-'.$_GET['id'].'.html',
					'Voir l\'historique de validation'
				));
				
				return render_to_response('ZcoBlogBundle::validation.html.php', $this->getVars());
			}
			else
				throw new AccessDeniedHttpException();
		}
		else
			throw new NotFoundHttpException();
	}
}
