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

namespace Zco\Bundle\AccueilBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Affichage de la page d'accueil du site.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class IndexController extends Controller
{
	public function defaultAction()
	{
		$registry = $this->get('zco_core.registry');
		$cache = $this->get('zco_core.cache');
		
		\zCorrecteurs::VerifierFormatageUrl();
		\Page::$titre = 'zCorrecteurs.fr - Les réponses à toutes vos questions concernant la langue française !';

		// Inclusion des modèles
		include_once(BASEPATH.'/src/Zco/Bundle/BlogBundle/modeles/blog.php');
		include_once(BASEPATH.'/src/Zco/Bundle/StatistiquesBundle/modeles/statistiques.php');
		include_once(BASEPATH.'/src/Zco/Bundle/StatistiquesBundle/modeles/statistiques_forum.php');
		include_once(BASEPATH.'/src/Zco/Bundle/RecrutementBundle/modeles/recrutements.php');
		include_once(BASEPATH.'/src/Zco/Bundle/DicteesBundle/modeles/statistiques-accueil.php');

		// Bloc « à tout faire »
		$vars = array();
		$vars['quel_bloc'] = $registry->get('bloc_accueil');
		$vars['Informations'] = $registry->get('accueil_informations');
		$vars['a_vote']   = null;
		$vars['question'] = null;
		$vars['reponses'] = null;
		$vars['sondage']  = null;
		$vars['ListerRecrutements'] = null;
		$vars['QuizSemaine'] = null;
		$vars['SujetSemaine'] = null;
		$vars['BilletSemaine'] = null;
		$vars['BilletHasard'] = null;
		$vars['BilletAuteurs'] = null;
		$vars['Tweets'] = null;
		$vars['Dictee'] = null;

		if($vars['quel_bloc'] == 'sondage')
		{
			$ip = $this->get('request')->getClientIp(true);
			$question = \Doctrine_Core::getTable('SondageQuestion')->getAccueil($_SESSION['id'], $ip);
			$vars['a_vote']   = $question->aVote($_SESSION['id'], $ip);
			$vars['question'] = $question;
			$vars['reponses'] = $question->Reponses;
			$vars['sondage']  = $question->Sondage;
		}
		elseif($vars['quel_bloc'] == 'recrutement')
		{
			$cacheKey = verifier('recrutements_voir_prives') ? 'liste_recrutements_prives' : 'liste_recrutements_publics';
			if(($ListerRecrutements = $cache->get($cacheKey)) === false)
			{
				$ListerRecrutements = ListerRecrutements();
				$cache->set($cacheKey, $ListerRecrutements, 0);
			}
			$vars['ListerRecrutements'] = $ListerRecrutements;
		 }
		 elseif($vars['quel_bloc'] == 'quiz')
		 {
			$vars['QuizSemaine'] = $registry->get('accueil_quiz');
		 }
		 elseif($vars['quel_bloc'] == 'sujet')
		 {
			$vars['SujetSemaine'] = $registry->get('accueil_sujet');
		 }
		 elseif($vars['quel_bloc'] == 'billet')
		 {
			$vars['BilletSemaine'] = $registry->get('accueil_billet');
			$vars['BilletSemaine'] = InfosBillet($vars['BilletSemaine']['billet_id']);
			$vars['BilletAuteurs'] = $vars['BilletSemaine'];
			$vars['BilletSemaine'] = $vars['BilletSemaine'][0];
		 }
		 elseif($vars['quel_bloc'] == 'billet_hasard')
		 {
			if($billet = $cache->get('billet_hasard'))
			{
				$vars['BilletHasard'] = InfosBillet($billet);
				$vars['BilletAuteurs'] = $vars['BilletHasard'];
				$vars['BilletHasard'] = $vars['BilletHasard'][0];
			}
			else
			{
				if(!$categories = $registry->get('categories_billet_hasard'))
					$categories = array();
				$rand = BilletAleatoire($categories);
				$cache->set('billet_hasard', $rand, TEMPS_BILLET_HASARD * 60);
				$vars['BilletHasard'] = InfosBillet($rand);
				$vars['BilletAuteurs'] = $vars['BilletHasard'];
				$vars['BilletHasard'] = $vars['BilletHasard'][0];
			}
		 }
		 elseif ($vars['quel_bloc'] == 'dictee')
		 {
		 	$dictee = $registry->get('dictee_en_avant');
		 	$vars['Dictee'] = ($dictee) ? ($dictee) : (array());
		 }

		// Blog
		list($vars['ListerBillets'], $vars['BilletsAuteurs']) = ListerBillets(array(
			'etat' => BLOG_VALIDE,
			'lecteurs' => false,
			'futur' => false,
		), -1);

		// Dictées
		$vars['DicteesAccueil']       = array_slice(DicteesAccueil(), 0, 2);
		$vars['DicteeHasard']         = DicteeHasard();
		$vars['DicteesLesPlusJouees'] = array_slice(DicteesLesPlusJouees(), 0, 2);

		// Quiz
		$vars['ListerQuizFrequentes'] = \Doctrine_Core::getTable('Quiz')->listerParFrequentation();
		$vars['ListerQuizNouveaux']   = \Doctrine_Core::getTable('Quiz')->listerRecents();
		$vars['QuizHasard']           = \Doctrine_Core::getTable('Quiz')->hasard();

		// Forum
		$vars['StatistiquesForum'] = RecupererStatistiquesForum();

		// Inclusion de la vue
		fil_ariane('Accueil');
		$resourceManager = $this->get('zco_vitesse.resource_manager');
		$resourceManager->addFeed('/informations/flux.html', array('title' => 'La dernière annonce publiée'));
		$resourceManager->requireResource('@ZcoAccueilBundle/Resources/public/css/home.css');
		$resourceManager->requireResource('@ZcoSondagesBundle/Resources/public/css/sondage.css');
		$resourceManager->requireResource('@ZcoCoreBundle/Resources/public/css/zcode.css');
		$resourceManager->requireResource('@ZcoDicteesBundle/Resources/public/css/dictees.css');

		return render_to_response($vars);
	}
}
