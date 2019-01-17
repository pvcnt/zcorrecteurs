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
use Zco\Bundle\ForumBundle\Domain\ForumDAO;
use Zco\Bundle\ForumBundle\Domain\PollDAO;
use Zco\Bundle\ForumBundle\Domain\TopicDAO;

/**
 * Contrôleur gérant l'affichage d'un sujet.
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class SujetAction extends ForumActions
{
	public function execute()
	{
		//On récupère les infos sur le sujet
		list($InfosSujet, $InfosForum) = $this->initSujet();
		zCorrecteurs::VerifierFormatageUrl($InfosSujet['sujet_titre'], true, true, 1);
		
		// Si le forum est archivé
		if( $InfosForum['cat_archive'] == 1 && !verifier('voir_archives')) {
			return redirect('Le forum n\'est plus accessible.', '/forum/', MSG_ERROR);
		}

		// Détermination de la page courante
		$_GET['p'] = ($_GET['p'] != '' && is_numeric($_GET['p'])) ? $_GET['p'] : 1;
		if ($_GET['p'] > 1)
		{
			Page::$titre .= ' - Page '.$_GET['p'];
		}

		//--- Redirection de la mort qui tue pour le référencement. :D ---
		if(!empty($_GET['id2']) AND is_numeric($_GET['id2']))
		{
			$_GET['p'] = TopicDAO::TrouverLaPageDeCeMessage($_GET['id'], $_GET['id2']);
			if($_GET['p'] == 1)
			{
				return new RedirectResponse('sujet-'.$_GET['id'].'-'.rewrite($InfosSujet['sujet_titre']).'.html#m'.$_GET['id2'], 301);
			}
			else
			{
				return new RedirectResponse('sujet-'.$_GET['id'].'-p'.$_GET['p'].'-'.rewrite($InfosSujet['sujet_titre']).'.html#m'.$_GET['id2'], 301);
			}
		}

		//--- Si on veut mettre le sujet en favori ---
		if(isset($_GET['changer_favori']) && $_GET['changer_favori'] == 1 && verifier('mettre_sujet_favori'))
		{
			if(empty($_GET['token']) || $_GET['token'] != $_SESSION['token'])
				throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();

            TopicDAO::ChangerFavori($_GET['id'], $InfosSujet['lunonlu_favori']);
			return redirect(
			    ($InfosSujet['lunonlu_favori'] ? 'Le sujet a bien été enlevé de vos favoris.' : 'Le sujet a bien été mis en favori.'),
                'sujet-'.$_GET['id'].'-'.rewrite($InfosSujet['sujet_titre']).'.html'
            );
		}

		//On récupère la liste des numéros des pages.
		$nbMessagesParPage = 20;
		$NombreDePages = ceil($InfosSujet['nombre_de_messages'] / $nbMessagesParPage);
		if($_GET['p'] > $NombreDePages)
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		$tableau_pages = liste_pages($_GET['p'],$NombreDePages,$InfosSujet['nombre_de_messages'],$nbMessagesParPage,'sujet-'.$_GET['id'].'-p%s-'.rewrite($InfosSujet['sujet_titre']).'.html');
		$debut = ($_GET['p'] - 1) * $nbMessagesParPage;

		if($_GET['p'] > 1)
		{
			$debut--;
			$nombreDeMessagesAafficher = $nbMessagesParPage+1;
		}
		else
		{
			$nombreDeMessagesAafficher = $nbMessagesParPage;
		}

		$ListerMessages = TopicDAO::ListerMessages($_GET['id'], $debut, $nombreDeMessagesAafficher);
		$SautRapide = ForumDAO::RecupererSautRapide($InfosSujet['sujet_forum_id']);
		$PremierMessage = TopicDAO::ListerMessages($_GET['id'], 0, 1);

		//--- Gestion des lus / non-lus ---
		$InfosLuNonlu = array(
			'lunonlu_utilisateur_id' => $InfosSujet['lunonlu_utilisateur_id'],
			'lunonlu_message_id' =>  $InfosSujet['lunonlu_message_id']
		);
		if (verifier('connecte'))
		{
            TopicDAO::RendreLeSujetLu($_GET['id'], $NombreDePages, $InfosSujet['sujet_dernier_message'], $ListerMessages, $InfosLuNonlu);
		}

		//Pour un meilleur référencement : ajout du début du premier message de la
		//page courante en balise meta description.
		$haystack = strip_tags($ListerMessages[0]['message_texte']);
		if(mb_strlen($haystack) > 10)
		{
			$offset = mb_strlen($haystack)-10;
			$mettre_description = true;
		}
		else
		{
			$mettre_description = false;
		}
		if(mb_strlen($haystack) > 250)
		{
			$offset = 240;
		}

		if($mettre_description)
		{
			Page::$description = htmlspecialchars(mb_substr($haystack, 0, mb_strpos($haystack, ' ', $offset)));
			if ($_GET['p'] > 1)
			{
				Page::$description .= ' - Page '.$_GET['p'];
			}
		}

		//Si le sujet est un sondage, on récupère les infos du sondage.
		if($InfosSujet['sujet_sondage'] > 0)
		{
			$ListerResultatsSondage = PollDAO::ListerResultatsSondage($InfosSujet['sujet_sondage']);

			//On compte le nombre total de votes
			$nombre_total_votes = 0;
			foreach($ListerResultatsSondage as $clef => $valeur)
			{
				$nombre_total_votes += $valeur['nombre_votes'];
			}
		}
		else
		{
			$ListerResultatsSondage = null;
			$nombre_total_votes = null;
		}

		$_SESSION['sujet_dernier_message'][$_GET['id']] = $InfosSujet['sujet_dernier_message'];

		//Inclusion des vues
		fil_ariane($InfosSujet['sujet_forum_id'], array(
			htmlspecialchars($InfosSujet['sujet_titre']) => 'sujet-'.$_GET['id'].'-'.rewrite($InfosSujet['sujet_titre']).'.html',
			'Voir le sujet'
		));
		$this->get('zco_core.resource_manager')->requireResources(array(
		    '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css',
		    '@ZcoCoreBundle/Resources/public/js/zform.js',
		));

		//Cette big condition permet de savoir si on affiche ou pas les options de modération.
		if
		(
			(
				(
					verifier('resolu_ses_sujets', $InfosSujet['sujet_forum_id']) AND $_SESSION['id'] == $InfosSujet['sujet_auteur']
				)
				OR verifier('resolu_sujets', $InfosSujet['sujet_forum_id'])
			)
			OR verifier('voir_alertes', $InfosSujet['sujet_forum_id'])
			OR verifier('signaler_sujets', $InfosSujet['sujet_forum_id'])
			OR verifier('epingler_sujets', $InfosSujet['sujet_forum_id'])
			OR verifier('fermer_sujets', $InfosSujet['sujet_forum_id'])
			OR verifier('editer_sujets', $InfosSujet['sujet_forum_id'])
			OR verifier('mettre_sujets_coup_coeur')
			OR verifier('deplacer_sujets', $InfosSujet['sujet_forum_id'])
			OR verifier('corbeille_sujets', $InfosSujet['sujet_forum_id'])
			OR verifier('suppr_sujets', $InfosSujet['sujet_forum_id'])
			OR verifier('diviser_sujets', $InfosSujet['sujet_forum_id'])
			OR verifier('fusionner_sujets', $InfosSujet['sujet_forum_id'])
		)
		{
			$afficher_options = true;
		}
		else
		{
			$afficher_options = false;
		}
		
		return render_to_response('ZcoForumBundle::sujet.html.php', array(
			'InfosSujet' => $InfosSujet,
			'InfosForum' => $InfosForum,
			'tableau_pages' => $tableau_pages,
			'ListerMessages' => $ListerMessages,
			'SautRapide' => $SautRapide,
			'InfosLuNonlu' => $InfosLuNonlu,
			'afficher_options' => $afficher_options,
			'ListerResultatsSondage' => $ListerResultatsSondage,
			'nombre_total_votes' => $nombre_total_votes,
			'NombreDePages' => $NombreDePages,
			'PremierMessage' => $PremierMessage[0],
		));
	}
}
