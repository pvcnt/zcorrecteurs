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

use Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zco\Bundle\BlogBundle\Domain\BlogDAO;

/**
 * Contrôleur gérant la proposition d'un billet.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class ProposerAction extends BlogActions
{
	public function execute()
	{
		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			//On récupère des infos sur le billet
			$ret = $this->initBillet();
			if ($ret instanceof Response)
				return $ret;
			Page::$titre .= ' - Proposer le billet';

			if((in_array($this->InfosBillet['blog_etat'], array(BLOG_BROUILLON, BLOG_REFUSE)) && $this->createur == true))
			{
				//Si on veut proposer le billet
				if(isset($_POST['submit']))
				{
					//Ajout de l'entrée dans l'historique de validation et
					//modification du billet
                    BlogDAO::AjouterHistoriqueValidation($_GET['id'], $_SESSION['id'],
					$this->InfosBillet['blog_id_version_courante'], $_POST['texte'], DECISION_NONE);

                    BlogDAO::EditerBillet($_GET['id'], array(
						'etat' => BLOG_PROPOSE,
						'date_proposition' => 'NOW'
					));
					
					return redirect(
					    'Votre billet a bien été proposé. Votre demande sera examinée par les administrateurs.',
                        'mes-billets.html'
                    );
				}

				//Inclusion de la vue
				fil_ariane($this->InfosBillet['cat_id'], array(
					htmlspecialchars($this->InfosBillet['version_titre']) => 'admin-billet-'.$_GET['id'].'-'.rewrite($this->InfosBillet['version_titre']).'.html',
					'Proposer le billet à la validation'
				));
				
				return render_to_response($this->getVars());
			}
			else
				throw new AccessDeniedHttpException();
		}
		else
			throw new NotFoundHttpException();
	}
}
