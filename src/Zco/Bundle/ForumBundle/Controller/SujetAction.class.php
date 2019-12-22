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

use Symfony\Component\HttpFoundation\Response;

/**
 * Contrôleur gérant l'affichage d'un sujet.
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class SujetAction extends ForumActions
{
	public function execute()
	{
		//Inclusion des modèles
		include(dirname(__FILE__).'/../modeles/forums.php');
		include(dirname(__FILE__).'/../modeles/messages.php');
		include(dirname(__FILE__).'/../modeles/moderation.php');
		include(dirname(__FILE__).'/../modeles/categories.php');

		include(dirname(__FILE__).'/../modeles/sondages.php');
		include(dirname(__FILE__).'/../modeles/votes.php');

		//On récupère les infos sur le sujet
		list($InfosSujet, $InfosForum) = $this->initSujet();
		if ($InfosSujet instanceof Response)
			return $InfosSujet;
		zCorrecteurs::VerifierFormatageUrl($InfosSujet['sujet_titre'], true, true, 1);
		
		// Si le forum est archivé
		if( $InfosForum['cat_archive'] == 1 && !verifier('voir_archives')) {
			return redirect(357, '/forum/', MSG_ERROR);
		}

		// Détermination de la page courante
		$_GET['p'] = ($_GET['p'] != '' && is_numeric($_GET['p'])) ? $_GET['p'] : 1;
		if ($_GET['p'] > 1)
		{
			Page::$titre .= ' - Page '.$_GET['p'];
		}

		//-- Si on veut poster un message automatique ---
		if(!empty($_POST['message']) AND is_numeric($_POST['message']) AND verifier('poster_reponse_auto', $InfosSujet['sujet_forum_id']))
		{
			$message = Doctrine_Core::getTable('ForumMessageAuto')->find($_POST['message']);
			if ($message !== false)
			{
				$_POST['texte'] = $message['texte'];
				$ferme = $message['ferme'] ? 1 : $InfosSujet['sujet_ferme'];
				$resolu = $message['resolu'] ? 1 : $InfosSujet['sujet_resolu'];
				$nouveau_message_id = EnregistrerNouveauMessage($_GET['id'], $InfosSujet['sujet_forum_id'],
					$InfosSujet['sujet_annonce'], $ferme, $resolu,
					$InfosSujet['sujet_corbeille'], $InfosSujet['sujet_auteur']);
				
				return redirect(66, 'sujet-'.$_GET['id'].'-'.$nouveau_message_id.'-'.rewrite($InfosSujet['sujet_titre']).'.html');
			}
		}

		//--- Si on veut ajouter un sondage au sujet ---
		elseif(verifier('ajouter_sondages', $InfosSujet['sujet_forum_id']) &&
			!empty($_POST['ajouter_sondage']) &&
			!empty($_POST['sondage_question']))
		{
			// Nettoyage des réponses
			$reponses = isset($_POST['reponses']) ? $_POST['reponses'] : array();
			foreach($reponses as $k => &$v)
			{
				$v = trim($v);
				if($v == '')
					unset($reponses[$k]);
			}

			// Au moins deux réponses
			if(count($reponses) >= 2)
				CreerSondageSujet($_GET['id'], $reponses);
			return redirect(478, 'sujet-'.$_GET['id'].'-'.rewrite($InfosSujet['sujet_titre']).'.html');
		}

		//--- Redirection de la mort qui tue pour le référencement. :D ---
		if(!empty($_GET['id2']) AND is_numeric($_GET['id2']))
		{
			$_GET['p'] = TrouverLaPageDeCeMessage($_GET['id'], $_GET['id2']);
			if($_GET['p'] == 1)
			{
				return new Symfony\Component\HttpFoundation\RedirectResponse('sujet-'.$_GET['id'].'-'.rewrite($InfosSujet['sujet_titre']).'.html#m'.$_GET['id2'], 301);
			}
			else
			{
				return new Symfony\Component\HttpFoundation\RedirectResponse('sujet-'.$_GET['id'].'-p'.$_GET['p'].'-'.rewrite($InfosSujet['sujet_titre']).'.html#m'.$_GET['id2'], 301);
			}
		}

		//--- Si on veut mettre le sujet en favori ---
		if(isset($_GET['changer_favori']) && $_GET['changer_favori'] == 1 && verifier('mettre_sujet_favori'))
		{
			if(empty($_GET['token']) || $_GET['token'] != $_SESSION['token'])
				return redirect(1, 'sujet-'.$_GET['id'].'.html', MSG_ERROR);

			ChangerFavori($_GET['id'], $InfosSujet['lunonlu_favori']);
			return redirect(($InfosSujet['lunonlu_favori'] ? 372 : 371), 'sujet-'.$_GET['id'].'-'.rewrite($InfosSujet['sujet_titre']).'.html');
		}

		//On récupère la liste des numéros des pages.
		$nbMessagesParPage = 20;
		$NombreDePages = ceil($InfosSujet['nombre_de_messages'] / $nbMessagesParPage);
		if($_GET['p'] > $NombreDePages)
			return redirect(352, 'sujet-'.$_GET['id'].'-'.rewrite($InfosSujet['sujet_titre']).'.html', MSG_ERROR);
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
		
		//Mise à jour de la position sur le site.
		\Doctrine_Core::getTable('Online')->updateUserPosition($_SESSION['id'], 'ZcoForumBundle:sujet', $_GET['id']);

		$ListerMessages = ListerMessages($_GET['id'], $debut, $nombreDeMessagesAafficher);
		$ListerVisiteurs = ListerVisiteursSujet($_GET['id']);
		$SautRapide = RecupererSautRapide($InfosSujet['sujet_forum_id']);
                $PremierMessage = ListerMessages($_GET['id'], 0, 1);

		//--- Gestion des lus / non-lus ---
		$InfosLuNonlu = array(
			'lunonlu_utilisateur_id' => $InfosSujet['lunonlu_utilisateur_id'],
			'lunonlu_message_id' =>  $InfosSujet['lunonlu_message_id']
		);
		if (verifier('connecte'))
		{
			RendreLeSujetLu($_GET['id'], $NombreDePages, $InfosSujet['sujet_dernier_message'], $ListerMessages, $InfosLuNonlu);
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
			$ListerResultatsSondage = ListerResultatsSondage($InfosSujet['sujet_sondage']);

			//On compte le nombre total de votes
			$nombre_total_votes = 0;
			foreach($ListerResultatsSondage as $clef => $valeur)
			{
				$nombre_total_votes += $valeur['nombre_votes'];
			}
			$DejaVote = VerifierDejaVote($InfosSujet['vote_membre_id']);
		}
		else
		{
			$ListerResultatsSondage = null;
			$DejaVote = null;
			$nombre_total_votes = null;
		}

		$_SESSION['sujet_dernier_message'][$_GET['id']] = $InfosSujet['sujet_dernier_message'];
		if(!empty($_SESSION['forum_citations'][$_GET['id']]))
			unset($_SESSION['forum_citations'][$_GET['id']]);

		//Inclusion des vues
		fil_ariane($InfosSujet['sujet_forum_id'], array(
			htmlspecialchars($InfosSujet['sujet_titre']) => 'sujet-'.$_GET['id'].'-'.rewrite($InfosSujet['sujet_titre']).'.html',
			'Voir le sujet'
		));
		$this->get('zco_vitesse.resource_manager')->requireResources(array(
		    '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css',
		    '@ZcoForumBundle/Resources/public/js/sujet.js',
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
			OR verifier('poster_reponse_auto', $InfosSujet['sujet_forum_id'])
			OR verifier('mettre_sujets_coup_coeur')
			OR verifier('code')
			OR
			(
				verifier('fermer_sondage', $InfosSujet['sujet_forum_id']) AND !empty($InfosSujet['sujet_sondage'])
			)
			OR
			(
				verifier('ajouter_sondages', $InfosSujet['sujet_forum_id']) AND empty($InfosSujet['sujet_sondage'])
			)
			OR
			(
				verifier('editer_sondages', $InfosSujet['sujet_forum_id']) AND !empty($InfosSujet['sujet_sondage'])
			)
			OR
			(
				verifier('supprimer_sondages', $InfosSujet['sujet_forum_id']) AND !empty($InfosSujet['sujet_sondage'])
			)
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
		
		return render_to_response(array(
			'InfosSujet' => $InfosSujet,
			'InfosForum' => $InfosForum,
			'tableau_pages' => $tableau_pages,
			'ListerMessages' => $ListerMessages,
			'ListerVisiteurs' => $ListerVisiteurs,
			'SautRapide' => $SautRapide,
			'InfosLuNonlu' => $InfosLuNonlu,
			'afficher_options' => $afficher_options,
			'ListerResultatsSondage' => $ListerResultatsSondage,
			'DejaVote' => $DejaVote,
			'nombre_total_votes' => $nombre_total_votes,
			'NombreDePages' => $NombreDePages,
			'PremierMessage' => $PremierMessage[0],
		));
	}
}
