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
use \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zco\Bundle\BlogBundle\Domain\BlogDAO;

/**
 * Contrôleur gérant la validation d'un billet.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class RepondreAction extends BlogActions
{
	public function execute()
	{
		Page::$titre .= ' - Répondre à une proposition de billet';

		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$ret = $this->initBillet();
			if ($ret instanceof Response)
				return $ret;

			if(verifier('blog_valider', $this->InfosBillet['blog_id_categorie']))
			{
				//Si on a pris la décision
				if(!empty($_POST['texte']) && !empty($_POST['decision']))
				{
					//Validation
                    BlogDAO::AjouterHistoriqueValidation($_GET['id'], $_SESSION['id'],
					$this->InfosBillet['blog_id_version_courante'], $_POST['texte'], $_POST['decision']);

					//Mise à jour du billet
					$etat = $_POST['decision'] == DECISION_VALIDER ? BLOG_PREPARATION : BLOG_REFUSE;
                    BlogDAO::EditerBillet($_GET['id'], array('etat' => $etat, 'date_validation' => 'NOW()'));

					//Envoi du mail
					foreach($this->Auteurs as $a)
					{
						//Validation
						if($_POST['decision'] == DECISION_VALIDER)
						{
							$message = render_to_string('::mail_auto/blog_validation.html.php', array(
								'pseudo'       => $a['utilisateur_pseudo'],
								'raison'       => $_POST['texte'],
								'pseudo_admin' => $_SESSION['pseudo'],
								'id_admin'     => $_SESSION['id'],
							));
							
							send_mail($a['utilisateur_email'], $a['utilisateur_pseudo'], '[zCorrecteurs.fr] Un de vos billets a été validé', $message);
						}
						//Refus
						else
						{
							$message = render_to_string('::mail_auto/blog_refus.html.php', array(
								'pseudo'       => $a['utilisateur_pseudo'],
								'raison'       => $_POST['texte'],
								'pseudo_admin' => $_SESSION['pseudo'],
								'id_admin'     => $_SESSION['id'],
							));
							
							send_mail($a['utilisateur_email'], $a['utilisateur_pseudo'], '[zCorrecteurs.fr] Un de vos billets a été refusé', $message);
						}
					}

					if($_POST['decision'] == DECISION_REFUSER)
						return redirect('Le billet a bien été refusé.', 'refus.html');
					else
						return redirect('Le billet a bien été validé.', 'propositions.html');
				}

				//Infos sur le commentaire
				$this->InfosValidation = BlogDAO::InfosValidationVersion($this->InfosBillet['blog_id_version_courante']);
				$this->ListerTags = BlogDAO::ListerTagsBillet($_GET['id']);

				//Inclusion de la vue
				fil_ariane($this->InfosBillet['cat_id'], array(
					htmlspecialchars($this->InfosBillet['version_titre']) => 'billet-'.$_GET['id'].'-'.rewrite($this->InfosBillet['version_titre']).'.html',
					'Répondre à une proposition'
				));
				
				return render_to_response($this->getVars());
			}
			else
			{
				throw new AccessDeniedHttpException();
			}
		}
		else
		{
			throw new NotFoundHttpException();
		}
	}
}
