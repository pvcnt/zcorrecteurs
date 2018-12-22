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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Zco\Bundle\BlogBundle\Domain\BlogDAO;
use Zco\Bundle\BlogBundle\Domain\CommentDAO;

/**
 * Contrôleur gérant l'affichage d'un billet du blog et
 * éventuellement de ses commentaires.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class BilletAction extends BlogActions
{
	public function execute()
	{
		//Si on a bien demandé à voir un billet
		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$ret = $this->initBillet();
			if ($ret instanceof Response)
				return $ret;

			if (!$this->verifier_voir)
				throw new AccessDeniedHttpException();

			zCorrecteurs::VerifierFormatageUrl($this->InfosBillet['version_titre'], true, true, 1);

            //Si le billet est un article virtuel.
            if(!is_null($this->InfosBillet['blog_url_redirection']) && !empty($this->InfosBillet['blog_url_redirection']))
            {
                $this->InfosBillet['blog_etat'] == BLOG_VALIDE && BlogDAO::BlogIncrementerVues($_GET['id']);
                return new RedirectResponse(htmlspecialchars($this->InfosBillet['blog_url_redirection']), 301);
            }

            //Si on veut voir un commentaire en particulier
            if(!empty($_GET['id2']) && is_numeric($_GET['id2']))
            {
                $page = CommentDAO::TrouverPageCommentaire($_GET['id2'], $_GET['id']);
                if($page !== false)
                {
                    $page = ($page > 1) ? '-p'.$page : '';
                    return new RedirectResponse('billet-'.$_GET['id'].$page.'-'.rewrite($this->InfosBillet['version_titre']).'.html#m'.$_GET['id2'], 301);
                }
                else
                    throw new NotFoundHttpException();
            }

            //--- Si on veut fermer les commentaires ---
            if(isset($_GET['fermer']) && $_GET['fermer'] == 1 && verifier('blog_choisir_comms'))
            {
                BlogDAO::EditerBillet($_GET['id'], array('commentaires' => COMMENTAIRES_NONE));
                return redirect(
                    'Les commentaires ont bien été fermés.',
                    'billet-'.$_GET['id'].'-'.rewrite($this->InfosBillet['version_titre']).'.html'
                );
            }

            //--- Si on veut ouvrir les commentaires ---
            if(isset($_GET['fermer']) && $_GET['fermer'] == 0 && verifier('blog_choisir_comms'))
            {
                BlogDAO::EditerBillet($_GET['id'], array('commentaires' => COMMENTAIRES_OK));
                return redirect(
                    'Les commentaires ont bien été ouverts.',
                    'billet-'.$_GET['id'].'-'.rewrite($this->InfosBillet['version_titre']).'.html'
                );
            }

            //--- Si on veut voir les commentaires ---
            if(!isset($_GET['comms']) || $_GET['comms'] != 0)
            {
                if(in_array($this->InfosBillet['blog_etat'], array(BLOG_PROPOSE, BLOG_PREPARATION)) &&
                !verifier('voir_coms_billets_proposes'))
                {
                    $this->comms = false;
                }
                else
                {
                    $this->comms = true;
                    $page = (!empty($_GET['p']) && is_numeric($_GET['p'])) ? $_GET['p'] : 1;
                    if ($page > 1)
                    {
                        Page::$titre .= ' - Page '.$page;
                        Page::$description .= ' - Page '.$page;
                    }

                    $this->ListerCommentaires = CommentDAO::ListerCommentairesBillet($_GET['id'], $page);
                    $this->CompterCommentaires = CommentDAO::CompterCommentairesBillet($_GET['id']);
                    $nbCommentairesParPage = 15;
                    $NombrePages = ceil($this->CompterCommentaires / $nbCommentairesParPage);
                    $this->ListePages = liste_pages($page, $NombrePages, $this->CompterCommentaires, $nbCommentairesParPage, 'billet-'.$_GET['id'].'-p%s-'.rewrite($this->InfosBillet['version_titre']).'.html#commentaires');

                    //On marque les commentaires comme lus s'il y en a
                    if(!empty($this->ListerCommentaires) && verifier('connecte'))
                        CommentDAO::MarquerCommentairesLus($this->InfosBillet, $this->ListerCommentaires);
                }
            }
            else
            {
                $this->comms = false;
            }

            //Droit de voir le panel moderation
            if((verifier('blog_editer_commentaires') || verifier('blog_choisir_comms')) && $this->comms == true)
                $this->voir_moderation = true;
            else
                $this->voir_moderation = false;

            $this->ListerBilletsLies = BlogDAO::ListerBilletsLies($_GET['id']);
            $this->ListerTags = BlogDAO::ListerTagsBillet($_GET['id']);
            $this->InfosBillet['blog_etat'] == BLOG_VALIDE && BlogDAO::BlogIncrementerVues($_GET['id']);

            //Inclusion de la vue
            fil_ariane($this->InfosBillet['cat_id'], array(
                htmlspecialchars($this->InfosBillet['version_titre']) => 'billet-'.$_GET['id'].'-'.rewrite($this->InfosBillet['version_titre']).'.html',
                'Lecture du billet'));
            $this->get('zco_core.resource_manager')->requireResources(array(
                '@ZcoForumBundle/Resources/public/css/forum.css',
                '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css',
            ));

            return render_to_response('ZcoBlogBundle::billet.html.php', $this->getVars());
		}
		else
			throw new NotFoundHttpException();
	}
}
