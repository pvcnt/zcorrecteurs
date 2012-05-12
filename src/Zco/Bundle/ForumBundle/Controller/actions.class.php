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
use Symfony\Component\HttpFoundation\Response;

class ForumActions extends Controller
{
	public function __construct()
	{
	    $resourceManager = \Container::getService('zco_vitesse.resource_manager');
		$resourceManager->addFeed(
		    '/forum/messages-flux.html', 
		    array('title' => 'Derniers messages du forum')
		);
		$resourceManager->requireResource(
		    '@ZcoForumBundle/Resources/public/css/forum.css'
		);
	}

	public function executeAjaxAutocompleteTitre()
	{
		$dbh = Doctrine_Manager::connection()->getDbh();

		$stmt = $dbh->prepare("SELECT sujet_titre, sujet_forum_id
			FROM zcov2_forum_sujets
			WHERE sujet_titre LIKE ".$dbh->quote($_POST['titre'].'%'));
		$stmt->execute();
		$donnees = $stmt->fetchAll();
		$retour = array();
		foreach($donnees as $row)
			if(verifier('voir_sujets', $row['sujet_forum_id']))
				$retour[] = $row['sujet_titre'];
		$response = new Symfony\Component\HttpFoundation\Response;
		$response->headers->set('Content-type',  'application/json');
		$response->setContent(json_encode($retour));
		return $response;
	}

	public function executeAjaxEditInPlaceTitre()
	{
		if(!empty($_POST['id_suj']))
		{
			include(BASEPATH.'/src/Zco/Bundle/ForumBundle/modeles/sujets.php');
			$infos = InfosSujet($_POST['id_suj']);
			if(verifier('editer_sujets', $infos['sujet_forum_id']) ||
				($infos['sujet_auteur'] == $_SESSION['id'] && verifier('editer_ses_sujets', $infos['sujet_forum_id']))
			)
			{
				$dbh = Doctrine_Manager::connection()->getDbh();
				$stmt = $dbh->prepare("UPDATE zcov2_forum_sujets
					SET sujet_titre = :titre
					WHERE sujet_id = :id");
				$stmt->bindParam(':id', $_POST['id_suj']);
				$stmt->bindValue(':titre', trim($_POST['data']));
				$stmt->execute();
				return new Symfony\Component\HttpFoundation\Response($_POST['data']);
			}
			else
				return new Symfony\Component\HttpFoundation\Response('Vous n\'avez pas l\'autorisation de modifier le titre.');
		}
		else
			return new Symfony\Component\HttpFoundation\Response('ERREUR');
	}

	public function executeAjaxEditInPlaceSousTitre()
	{
		if(!empty($_POST['id_suj']))
		{
			include(BASEPATH.'/src/Zco/Bundle/ForumBundle/modeles/sujets.php');
			$infos = InfosSujet($_POST['id_suj']);
			if(verifier('editer_sujets', $infos['sujet_forum_id']) ||
				($infos['sujet_auteur'] == $_SESSION['id'] && verifier('editer_ses_sujets', $infos['sujet_forum_id']))
			)
			{
				$dbh = Doctrine_Manager::connection()->getDbh();
				$stmt = $dbh->prepare("UPDATE zcov2_forum_sujets
					SET sujet_sous_titre = :sous_titre
					WHERE sujet_id = :id");
				$stmt->bindParam(':id', $_POST['id_suj']);
				$stmt->bindValue(':sous_titre', trim($_POST['data']));
				$stmt->execute();
				return new Symfony\Component\HttpFoundation\Response($_POST['data']);
			}
			else
				return new Symfony\Component\HttpFoundation\Response('Vous n\'avez pas l\'autorisation de modifier le sous-titre.');
		}
		else
			return new Symfony\Component\HttpFoundation\Response('ERREUR');
	}

	public function executeAjaxDeplacementMassif()
	{
		//Inclusion du modèle
		include(BASEPATH.'/src/Zco/Bundle/ForumBundle/modeles/categories.php');

		if(verifier('deplacer_sujets', $_GET['f']))
		{
			$CategoriesForums = ListerCategoriesForum();
			if($CategoriesForums)
			{
				$ret = '<select name="forum_cible">';
				$i=0;
				foreach($CategoriesForums as $clef => $valeur)
				{
					if($valeur['cat_niveau'] == 2 && $_GET['f'] != $valeur['cat_id'])
					{
						//Dans ce if on affiche que les catégories
						if($clef > 1)
						{
							$ret .= '</optgroup>';
						}
						$categorie_deplacement = $valeur['cat_id'];
						$ret .= '<optgroup label="'.htmlspecialchars($valeur['cat_nom']).'">';
					}
					//Ici on affiche que les forums
					else
					{
						$ret .= '<option value="'.$valeur['cat_id'].'">'.htmlspecialchars($valeur['cat_nom']).'</option>';
					}
					$i++;
				}
				$ret .= '</optgroup></select>';
			}
			else
			{
				$ret = 'Ce forum n\'existe pas.';
			}
		}
		else
		{
			$ret = 'Vous n\'avez pas les droits requis ou un paramètre a été omis.';
		}
        
		return new Symfony\Component\HttpFoundation\Response($ret);
	}

	public function executeAjaxDeplacerSujet()
	{
		//Inclusion du modèle
		include(BASEPATH.'/src/Zco/Bundle/ForumBundle/modeles/categories.php');

		if(!empty($_POST['fofo_actuel']) AND is_numeric($_POST['fofo_actuel']) AND verifier('deplacer_sujets', $_POST['fofo_actuel']) AND !empty($_POST['id']) AND is_numeric($_POST['id']))
		{
			$CategoriesForums = ListerCategoriesForum();
			if($CategoriesForums)
			{
				$ret = '
				<form action="deplacer-'.$_POST['id'].'.html" method="post">
				<select name="forum_cible">
				';
				$i=0;
				foreach($CategoriesForums as $clef => $valeur)
				{
					if($valeur['cat_niveau'] == 2 && $_POST['fofo_actuel'] != $valeur['cat_id'])
					{
						//Dans ce if on affiche que les catégories
						if($i > 1)
						{
							$ret .= '</optgroup>';
						}
						$categorie_deplacement = $valeur['cat_id'];
						$ret .= '<optgroup label="'.htmlspecialchars($valeur['cat_nom']).'">';
					}
					//Ici on affiche que les forums
					else
					{
						$ret .= '<option value="'.$valeur['cat_id'].'">'.htmlspecialchars($valeur['cat_nom']).'</option>';
						if (!empty($valeur['sous_forums']))
						{
							foreach ($valeur['sous_forums'] as $forum)
							{
								$ret .= '<option value="'.$forum['cat_id'].'">'.str_pad('', ($forum['cat_niveau']-3)*3, '...').htmlspecialchars($forum['cat_nom']).'</option>';
							}
						}
					}
					$i++;
				}
				$ret .= '
				</optgroup></select>
				<input type="submit" value="Déplacer" />
				</form>
				';
			}
			else
			{
				$ret = 'Ce forum n\'existe pas.';
			}

		}
		else
		{
			$ret = 'Vous n\'avez pas les droits requis ou un paramètre a été omis.';
		}
		return new Symfony\Component\HttpFoundation\Response($ret);
	}

	public function executeAjaxMultiCiter()
	{
		//Inclusion du modèle
		include(BASEPATH.'/src/Zco/Bundle/ForumBundle/modeles/messages.php');

		if (!empty($_POST['action']) && !empty($_POST['url']) && verifier('connecte'))
		{
			if(preg_match('`repondre-([0-9]+)-([0-9]+)\.html`', $_POST['url'], $matches))
			{
				$id_suj = $matches[1];
				$id_msg = $matches[2];
				$infos = InfosMessage($id_msg);
				if (!empty($infos) && verifier('voir_sujets', $infos['sujet_forum_id']))
				{
					if ($_POST['action'] == 'ajoute')
					{
						$_SESSION['forum_citations'][$id_suj][] = $id_msg;
						
						return new Response('<citation rid="'.$id_msg.'">'.$infos['message_texte'].'</citation>');
					}
					else
					{
						unset($_SESSION['forum_citations'][$id_suj][array_search($infos['message_id'], $_SESSION['forum_citations'][$id_suj])]);
						
						return new Response($id_msg);
					}
				}
				else
					return new Response('Message inexistant.');
			}
			else
				return new Response('URL incorrecte.');
		}
		else
			return new Response('Vous n\'avez pas les droits requis ou un paramètre a été omis.');
	}

	public function executeAjaxReponseAuto()
	{
		if(verifier('poster_reponse_auto', $_POST['fofo_actuel']))
		{
			$messages = Doctrine_Core::getTable('ForumMessageAuto')->Lister();

			$ret = '<form method="post" action=""><select name="message" id="message">';
			foreach($messages as $message)
			{
				$ret .= '<option value="'.$message['id'].'">'.$message['nom'].'</option>';
			}
			$ret .= '</select><input type="submit" value="Aller" /></form>';
			return new Symfony\Component\HttpFoundation\Response($ret);
		}
		else
		{
			return new Symfony\Component\HttpFoundation\Response('Vous n\'avez pas l\'autorisation de voir les messages automatiques.');
		}
	}

	public function executeAjaxRetourSondage()
	{
		//Inclusion du modèle
		include(BASEPATH.'/src/Zco/Bundle/ForumBundle/modeles/sondages.php');

		if(!empty($_GET['forum']) && is_numeric($_GET['forum']))
		{
			if(verifier('voir_votants', $_GET['forum']) AND !empty($_GET['sondage']) AND is_numeric($_GET['sondage']))
			{
				$ListerLesVotants = ListerLesVotants($_GET['sondage']);
				$retour = '';
				if($ListerLesVotants)
				{
					foreach($ListerLesVotants as $clef => $valeur)
					{
						// Removed on may, 6th 2008
						// $classe_groupe_utilisateur = classe_groupe($valeur['utilisateur_id_groupe']);
						if(!empty($valeur['utilisateur_id']))
						{
							$retour .= '<a href="/membres/profil-'.$valeur['vote_membre_id'].'.html" style="color: '.$valeur['groupe_class'].';">';
						}
						$retour .= $valeur['utilisateur_pseudo'];
						if(!empty($valeur['utilisateur_id']))
						{
							$retour .= '</a>';
						}
						$retour .= ', ';
					}
					return new Symfony\Component\HttpFoundation\Response(mb_substr($retour, 0, -2));
				}
				else
				{
					return new Symfony\Component\HttpFoundation\Response('Ce sondage n\'existe pas ou personne n\'y a voté.');
				}

			}
			else
			{
				return new Symfony\Component\HttpFoundation\Response('Vous n\'avez pas le droit de voir qui a voté.');
			}
		}
		else
		{
			return new Symfony\Component\HttpFoundation\Response('Le forum visionné actuellement n\'est pas spécifié.');
		}
	}

	public function executeAjaxOrdre($ordre = false)
	{
		$dbh = Doctrine_Manager::connection()->getDbh();
		if(!$ordre && isset($_POST['ordre']))
		{
			$stmt = $dbh->prepare('REPLACE INTO zcov2_forum_ordre '
				.'(utilisateur_id, ordre) VALUES ('
				.':id, :ordre )');
			$stmt->bindParam(':id', $_SESSION['id']);
			$stmt->bindValue(':ordre', $_POST['ordre']);
			$stmt->execute();
			return new Symfony\Component\HttpFoundation\Response('');
		}

		$stmt = $dbh->prepare('SELECT ordre FROM zcov2_forum_ordre '
			.'WHERE utilisateur_id = :id');
		$stmt->bindParam(':id', $_SESSION['id']);
		$stmt->execute();
		$d = $stmt->fetchColumn(0);
		if($ordre) return $d;
		return new Symfony\Component\HttpFoundation\Response($d);
	}

	public function initSujet()
	{
		include(dirname(__FILE__).'/../modeles/sujets.php');

		//Compatibilité
		if(!isset($_GET['s'])) $_GET['s'] = $_GET['id'];
		if(empty($_GET['id'])) $_GET['id'] = $_GET['s'];

		//--- Récupération des infos sur le sujet ---
		if(empty($_GET['id']) || !is_numeric($_GET['id']))
			return array(redirect(45, '/forum/', MSG_ERROR), null);
		else
		{
			$InfosSujet = InfosSujet($_GET['id']);
			$InfosForum = InfosCategorie($InfosSujet['sujet_forum_id']);
			if(empty($InfosSujet))
				return array(redirect(47, '/forum/', MSG_ERROR), null);
		}

		//--- Modification des balises méta ---
		Page::$titre = htmlspecialchars($InfosSujet['sujet_titre']);

		return array($InfosSujet, $InfosForum);
	}
}
