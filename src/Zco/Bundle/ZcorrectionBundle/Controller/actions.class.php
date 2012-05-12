<?php

/**
 * Copyright 2012 Corrigraphie
 * 
 * This file is part of zCorrecteurs.fr.
 *
 * zCorrecteurs.fr is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * zCorrecteurs.fr is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with zCorrecteurs.fr. If not, see <http://www.gnu.org/licenses/>.
 */

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Actions concernant le processus de zCorrection de tutoriels du SdZ.
 *
 * @author Savageman, vincent1870, DJ Fox
 */
class ZcorrectionActions extends Controller
{
	public function __construct()
	{
		//Inclusion des modèles
		include(dirname(__FILE__).'/../modeles/tutoriels.php');
		include(dirname(__FILE__).'/../modeles/corrections.php');
		include(dirname(__FILE__).'/../modeles/soumissions.php');
	}

	public function executeAjaxDropMark()
	{
		$db = Doctrine_Manager::connection()->getDbh();
		$stmt = $db->prepare("UPDATE zcov2_push_corrections SET correction_marque = :marque WHERE correction_id = :id");
		$stmt->bindParam(':marque', $_POST['url']);
		$stmt->bindParam(':id', $_POST['cid']);
		$stmt->execute();

		return new Symfony\Component\HttpFoundation\Response('OK');
	}

	public function executeAjaxParseTexte()
	{
		$_POST['texte'] = str_replace('%u20AC', '&euro;', $_POST['texte']);
		$dbh = Doctrine_Manager::connection()->getDbh();

		/* Liste des valeurs possibles :
		mini-intro-[ID]
		sous_partie-[ID]-[NUM]
		mini-ccl-[ID]
		*/
		list(,$type, $arg1, $arg2) = explode('-', $_POST['id']);

		if ($type == 'mini')
		{
			if ($arg1 == 'intro') { $champ = 'mini_tuto_introduction'; }
			else if ($arg1 == 'ccl') { $champ = 'mini_tuto_conclusion'; }

			$stmt = $dbh->prepare("UPDATE zcov2_push_mini_tutos
			SET ".$champ." = :texte
			WHERE mini_tuto_id = :mini_tuto_id");

			$stmt->bindParam(':mini_tuto_id', $arg2);
			$stmt->bindParam(':texte', $_POST['texte']);

			if (!$stmt->execute())
			{
				return new Symfony\Component\HttpFoundation\Response('ERREUR');
			}

			return new Symfony\Component\HttpFoundation\Response(str_replace('&amp;euro;', '&euro;', $this->get('zco_parser.parser')->with('sdz')->parse($_POST['texte'])));

		}
		else if ($type == 'sous_partie')
		{
			$stmt = $dbh->prepare("SELECT
			sous_partie_texte
			FROM zcov2_push_mini_tuto_sous_parties
			WHERE sous_partie_id = :sous_partie_id");

			$stmt->bindParam(':sous_partie_id', $arg1);

			if (!$stmt->execute() || !$resultat = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				return new Symfony\Component\HttpFoundation\Response('ERREUR');
			}

			/*
			$paragraphes = preg_split('`(\r\n|\n|\r){3,}`', trim($resultat['sous_partie_texte']));
			$paragraphes = array_map('trim', $paragraphes);

			$paragraphes[$arg2] = preg_replace('`(\r\n|\n|\r){3,}`', '$1$1', trim($_POST['texte']));
			$paragraphes = implode("\r\n\r\n", $paragraphes);
			// */

			preg_match_all('`<titre([12])\>(.+?)\<\/titre(?:\1)\>`', trim($resultat['sous_partie_texte']), $pma);

			$pos = array(0);
			$i = 0;
			foreach($pma[0] as $v) {
				$pos[] = mb_strpos($resultat['sous_partie_texte'], $v, $pos[$i++]);
			}
			$pos[] = mb_strlen($resultat['sous_partie_texte']);

			$paragraphes = array();

			foreach($pos as $k=>$v) {
				if (isset($pos[$k+1])) {
					$paragraphes[] = mb_substr($resultat['sous_partie_texte'], $v, $pos[$k+1]-$v);
				}
			}

			//Remplacement de la correction (c'est mieux pour que ça marche)
			$paragraphes[$arg2] = $_POST['texte'];

			$paragraphes = implode("\r\n\r\n", array_map('trim', $paragraphes));

			$stmt->closeCursor();

			$stmt = $dbh->prepare("UPDATE zcov2_push_mini_tuto_sous_parties
			SET sous_partie_texte = :sous_partie_texte
			WHERE sous_partie_id = :sous_partie_id");

			$stmt->bindParam(':sous_partie_texte', $paragraphes);
			$stmt->bindParam(':sous_partie_id', $arg1);

			if (!$stmt->execute())
			{
				return new Symfony\Component\HttpFoundation\Response('ERREUR');
			}
			return new Symfony\Component\HttpFoundation\Response(str_replace('&amp;euro;', '&euro;', $this->get('zco_parser.parser')->with('sdz')->parse($_POST['texte'])));
			//echo '<br /><br />envoyé bdd : '.$paragraphes;
			// echo strtr(parse($_POST['texte']), array('&amp;' => '&','&lt;' => '<','&gt;' => '>'));
		}
		else if ($type == 'partie')
		{
			if ($arg1 == 'intro') { $champ = 'partie_introduction'; }
			else if ($arg1 == 'ccl') { $champ = 'partie_conclusion'; }

			$stmt = $dbh->prepare("UPDATE zcov2_push_big_tutos_parties
			SET ".$champ." = :texte
			WHERE partie_id = :partie_id");

			$stmt->bindParam(':partie_id', $arg2);
			$stmt->bindParam(':texte', $_POST['texte']);

			if (!$stmt->execute())
			{
				return new Symfony\Component\HttpFoundation\Response('ERREUR');
			}

			return new Symfony\Component\HttpFoundation\Response(str_replace('&amp;euro;', '&euro;', $this->get('zco_parser.parser')->with('sdz')->parse($_POST['texte'])));
		}
		else if ($type == 'big')
		{
			if ($arg1 == 'intro') { $champ = 'big_tuto_introduction'; }
			else if ($arg1 == 'ccl') { $champ = 'big_tuto_conclusion'; }

			$stmt = $dbh->prepare("UPDATE zcov2_push_big_tutos
			SET ".$champ." = :texte
			WHERE big_tuto_id = :big_tuto_id");

			$stmt->bindParam(':big_tuto_id', $arg2);
			$stmt->bindParam(':texte', $_POST['texte']);

			if (!$stmt->execute())
			{
				return new Symfony\Component\HttpFoundation\Response('ERREUR');
			}

			return new Symfony\Component\HttpFoundation\Response(str_replace('&amp;euro;', '&euro;', $this->get('zco_parser.parser')->with('sdz')->parse($_POST['texte'])));
		}
		else if ($type == 'qcm')
		{
			if ($arg1 == 'question') { $champ = 'question_label'; }
			else if ($arg1 == 'explication') { $champ = 'question_explications'; }

			$stmt = $dbh->prepare("UPDATE zcov2_push_qcm_questions
			SET ".$champ." = :texte
			WHERE question_id = :question_id");

			$stmt->bindParam(':question_id', $arg2);
			$stmt->bindParam(':texte', $_POST['texte']);

			if (!$stmt->execute())
			{
				return new Symfony\Component\HttpFoundation\Response('ERREUR');
			}

			return new Symfony\Component\HttpFoundation\Response(str_replace('&amp;euro;', '&euro;', $this->get('zco_parser.parser')->with('sdz')->parse($_POST['texte'])));
		}
		else
		{
			return new Symfony\Component\HttpFoundation\Response('Erreur AJAX, code : [texte, else]');
		}
	}

	public function executeAjaxParseTitre()
	{
		$_POST['texte'] = str_replace('%u20AC', '&euro;', $_POST['texte']);
		$dbh = Doctrine_Manager::connection()->getDbh();

		/* Liste des valeurs possibles :
		mini-[ID]
		sous_partie-[ID]
		big-[ID]
		partie-[ID]
		reponse-[ID]
		*/
		list(,$type, $id) = explode('-', $_POST['id']);

		if ($type == 'mini')
		{
			$stmt = $dbh->prepare("UPDATE zcov2_push_mini_tutos
			SET mini_tuto_titre = :mini_tuto_titre
			WHERE mini_tuto_id = :mini_tuto_id");

			$stmt->bindParam(':mini_tuto_titre', $_POST['texte']);
			$stmt->bindParam(':mini_tuto_id', $id);

			if (!$stmt->execute())
			{
				return new Symfony\Component\HttpFoundation\Response('ERREUR');
			}

			return new Symfony\Component\HttpFoundation\Response(str_replace('&amp;euro;', '&euro;', $_POST['texte']));
		}
		else if ($type == 'sous_partie')
		{
			$stmt = $dbh->prepare("UPDATE zcov2_push_mini_tuto_sous_parties
			SET sous_partie_titre = :sous_partie_titre
			WHERE sous_partie_id = :sous_partie_id");

			$stmt->bindParam(':sous_partie_titre', $_POST['texte']);
			$stmt->bindParam(':sous_partie_id', $id);

			if (!$stmt->execute())
			{
				return new Symfony\Component\HttpFoundation\Response('ERREUR');
			}

			return new Symfony\Component\HttpFoundation\Response(str_replace('&amp;euro;', '&euro;', $_POST['texte']));
		}
		else if ($type == 'partie')
		{
			$stmt = $dbh->prepare("UPDATE zcov2_push_big_tutos_parties
			SET partie_titre = :partie_titre
			WHERE partie_id = :partie_id");

			$stmt->bindParam(':partie_titre', $_POST['texte']);
			$stmt->bindParam(':partie_id', $id);

			if (!$stmt->execute())
			{
				exit('ERREUR');
			}

			return new Symfony\Component\HttpFoundation\Response(str_replace('&amp;euro;', '&euro;', $_POST['texte']));
		}
		else if ($type == 'big')
		{
			$stmt = $dbh->prepare("UPDATE zcov2_push_big_tutos
			SET big_tuto_titre = :big_tuto_titre
			WHERE big_tuto_id = :big_tuto_id");

			$stmt->bindParam(':big_tuto_titre', $_POST['texte']);
			$stmt->bindParam(':big_tuto_id', $id);

			if (!$stmt->execute())
			{
				return new Symfony\Component\HttpFoundation\Response('ERREUR');
			}

			return new Symfony\Component\HttpFoundation\Response(str_replace('&amp;euro;', '&euro;', $_POST['texte']));
		}
		else if ($type == 'reponse')
		{
			$stmt = $dbh->prepare("UPDATE zcov2_push_qcm_reponses
			SET reponse_texte = :texte
			WHERE reponse_id = :reponse_id");

			$stmt->bindParam(':texte', $_POST['texte']);
			$stmt->bindParam(':reponse_id', $id);

			if (!$stmt->execute())
			{
				return new Symfony\Component\HttpFoundation\Response('ERREUR');
			}

			return new Symfony\Component\HttpFoundation\Response(str_replace('&amp;euro;', '&euro;', $_POST['texte']));
		}
		else
		{
			return new Symfony\Component\HttpFoundation\Response('Erreur AJAX, code : [titre, else]');
		}
	}

	public function executeAjaxUnparseTexte()
	{
		/* Liste des valeurs possibles :
		mini-intro-[ID]
		sous_partie-[ID]-[NUM]
		mini-ccl-[ID]
		qcm-question-[ID]
		qcm-explication-[ID]
		*/
		list(,$type, $arg1, $arg2) = explode('-', $_POST['id']);

		$dbh = Doctrine_Manager::connection()->getDbh();

		if ($type == 'mini')
		{
			if ($arg1 == 'intro') { $champ = 'mini_tuto_introduction'; }
			else if ($arg1 == 'ccl') { $champ = 'mini_tuto_conclusion'; }

			$stmt = $dbh->prepare("SELECT
			".$champ."
			FROM zcov2_push_mini_tutos
			WHERE mini_tuto_id = :mini_tuto_id");

			$stmt->bindParam(':mini_tuto_id', $arg2);

			if (!$stmt->execute() || !$resultat = $stmt->fetch(PDO::FETCH_NUM))
			{
				return new Symfony\Component\HttpFoundation\Response('ERREUR');
			}
			return new Symfony\Component\HttpFoundation\Response(trim($resultat[0]));
		}
		else if ($type == 'sous_partie')
		{
			$stmt = $dbh->prepare("SELECT
			sous_partie_titre,
			sous_partie_texte
			FROM zcov2_push_mini_tuto_sous_parties
			WHERE sous_partie_id = :sous_partie_id");

			$stmt->bindParam(':sous_partie_id', $arg1);

			if (!$stmt->execute() || !$resultat = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				return new Symfony\Component\HttpFoundation\Response('ERREUR');
			}
			/*
			$paragraphes = preg_split('`(\r\n|\n|\r){3,}`', trim($resultat['sous_partie_texte']));
			// */

			preg_match_all('`<titre([12])\>(.+?)\<\/titre(?:\1)\>`', trim($resultat['sous_partie_texte']), $pma);

			$pos = array(0);
			$i = 0;
			foreach($pma[0] as $v) {
				$pos[] = mb_strpos($resultat['sous_partie_texte'], $v, $pos[$i++]);
			}
			$pos[] = mb_strlen($resultat['sous_partie_texte']);

			$paragraphes = array();

			foreach($pos as $k=>$v) {
				if (isset($pos[$k+1])) {
					$paragraphes[] = mb_substr($resultat['sous_partie_texte'], $v, $pos[$k+1]-$v);
				}
			}
			$paragraphes = array_map('trim', $paragraphes);

			return new Symfony\Component\HttpFoundation\Response($paragraphes[$arg2]);
		}
		else if ($type == 'partie')
		{
			if ($arg1 == 'intro') { $champ = 'partie_introduction'; }
			else if ($arg1 == 'ccl') { $champ = 'partie_conclusion'; }

			$stmt = $dbh->prepare("SELECT
			".$champ."
			FROM zcov2_push_big_tutos_parties
			WHERE partie_id = :partie_id");

			$stmt->bindParam(':partie_id', $arg2);

			if (!$stmt->execute() || !$resultat = $stmt->fetch(PDO::FETCH_NUM))
			{
				return new Symfony\Component\HttpFoundation\Response('ERREUR');
			}
			return new Symfony\Component\HttpFoundation\Response(trim($resultat[0]));
		}
		else if ($type == 'big')
		{
			if ($arg1 == 'intro') { $champ = 'big_tuto_introduction'; }
			else if ($arg1 == 'ccl') { $champ = 'big_tuto_conclusion'; }

			$stmt = $dbh->prepare("SELECT
			".$champ."
			FROM zcov2_push_big_tutos
			WHERE big_tuto_id = :big_tuto_id");

			$stmt->bindParam(':big_tuto_id', $arg2);

			if (!$stmt->execute() || !$resultat = $stmt->fetch(PDO::FETCH_NUM))
			{
				return new Symfony\Component\HttpFoundation\Response('ERREUR');
			}
			return new Symfony\Component\HttpFoundation\Response(trim($resultat[0]));
		}
		else if ($type == 'qcm')
		{
			if ($arg1 == 'question') { $champ = 'question_label'; }
			else if ($arg1 == 'explication') { $champ = 'question_explications'; }

			$stmt = $dbh->prepare("SELECT
			".$champ."
			FROM zcov2_push_qcm_questions
			WHERE question_id = :question_id");

			$stmt->bindParam(':question_id', $arg2);

			if (!$stmt->execute() || !$resultat = $stmt->fetch(PDO::FETCH_NUM))
			{
				return new Symfony\Component\HttpFoundation\Response('ERREUR');
			}
			return new Symfony\Component\HttpFoundation\Response(trim($resultat[0]));
		}
		else
		{
			return new Symfony\Component\HttpFoundation\Response('Erreur AJAX, code : [texte, else]');
		}
	}

	public function executeAjaxUnparseTitre()
	{
		/* Liste des valeurs possibles :
		mini-intro-[ID]
		sous_partie-[ID]-[NUM]
		mini-ccl-[ID]
		qcm-question-[ID]
		qcm-explication-[ID]
		*/
		@list(,$type, $arg1, $arg2) = explode('-', $_POST['id']);
		$dbh = Doctrine_Manager::connection()->getDbh();

		if ($type == 'mini')
		{
			$stmt = $dbh->prepare("SELECT
			mini_tuto_titre
			FROM zcov2_push_mini_tutos
			WHERE mini_tuto_id = :mini_tuto_id");

			$stmt->bindParam(':mini_tuto_id', $arg1);

			if (!$stmt->execute() || !$resultat = $stmt->fetch(PDO::FETCH_NUM))
			{
				return new Symfony\Component\HttpFoundation\Response('ERREUR');
			}
			return new Symfony\Component\HttpFoundation\Response(trim($resultat[0]));
		}
		else if ($type == 'sous_partie')
		{
			$stmt = $dbh->prepare("SELECT
			sous_partie_titre
			FROM zcov2_push_mini_tuto_sous_parties
			WHERE sous_partie_id = :sous_partie_id");

			$stmt->bindParam(':sous_partie_id', $arg1);

			if (!$stmt->execute() || !$resultat = $stmt->fetch(PDO::FETCH_NUM))
			{
				return new Symfony\Component\HttpFoundation\Response('ERREUR');
			}

			return new Symfony\Component\HttpFoundation\Response(trim($resultat[0]));
		}
		else if ($type == 'partie')
		{
			$stmt = $dbh->prepare("SELECT
			partie_titre
			FROM zcov2_push_big_tutos_parties
			WHERE partie_id = :partie_id");

			$stmt->bindParam(':partie_id', $arg1);

			if (!$stmt->execute() || !$resultat = $stmt->fetch(PDO::FETCH_NUM))
			{
				return new Symfony\Component\HttpFoundation\Response('ERREUR');
			}
			return new Symfony\Component\HttpFoundation\Response(trim($resultat[0]));
		}
		else if ($type == 'big')
		{
			$stmt = $dbh->prepare("SELECT
			big_tuto_titre
			FROM zcov2_push_big_tutos
			WHERE big_tuto_id = :big_tuto_id");

			$stmt->bindParam(':big_tuto_id', $arg1);

			if (!$stmt->execute() || !$resultat = $stmt->fetch(PDO::FETCH_NUM))
			{
				return new Symfony\Component\HttpFoundation\Response('ERREUR');
			}
			return new Symfony\Component\HttpFoundation\Response(trim($resultat[0]));
		}
		else if ($type == 'reponse')
		{
			$stmt = $dbh->prepare("SELECT
			reponse_texte
			FROM zcov2_push_qcm_reponses
			WHERE reponse_id = :reponse_id");

			$stmt->bindParam(':reponse_id', $arg1);

			if (!$stmt->execute() || !$resultat = $stmt->fetch(PDO::FETCH_NUM))
			{
				return new Symfony\Component\HttpFoundation\Response('ERREUR');
			}
			return new Symfony\Component\HttpFoundation\Response(trim($resultat[0]));
		}
		else
		{
			return new Symfony\Component\HttpFoundation\Response('Erreur AJAX, code : [titre, else]');
		}
	}

	public function executeAjaxSaveCommentaires()
	{
		$dbh = Doctrine_Manager::connection()->getDbh();

		$stmt = $dbh->prepare("UPDATE zcov2_push_corrections
		SET correction_commentaire = :texte
		WHERE correction_id = :id");
		$stmt->bindParam(':id', $_POST['id']);
		$stmt->bindParam(':texte', $_POST['texte']);
		$stmt->execute();

		return new Symfony\Component\HttpFoundation\Response('OK');
	}

	public function executeAjaxSaveCommentaires2()
	{
		$dbh = Doctrine_Manager::connection()->getDbh();

		$stmt = $dbh->prepare("UPDATE zcov2_push_soumissions
		SET soumission_commentaire = :texte
		WHERE soumission_id = :id
		");
		$stmt->bindParam(':id', $_POST['id']);
		$stmt->bindParam(':texte', $_POST['texte']);
		$stmt->execute();

		return new Symfony\Component\HttpFoundation\Response('OK');
	}

	public function executeAjaxSaveConfidentialite()
	{
		$dbh = Doctrine_Manager::connection()->getDbh();

		$conf = $_POST['value'] ? 1 : 0;
		$stmt = $dbh->prepare("UPDATE zcov2_push_corrections
		SET correction_correcteur_invisible = :conf
		WHERE correction_id = :id");
		$stmt->bindParam(':id', $_POST['id']);
		$stmt->bindParam(':conf', $conf);
		$stmt->execute();

		return new Symfony\Component\HttpFoundation\Response('OK');
	}
}
