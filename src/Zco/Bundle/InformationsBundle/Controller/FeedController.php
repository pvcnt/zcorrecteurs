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

namespace Zco\Bundle\InformationsBundle\Controller;

/**
 * Classe définissant le flux du module d'annonces de la page d'accueil.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class FeedController extends \Feed
{
	protected $title = 'Annonces de zCorrecteurs.fr';
	protected $description = 'La dernière annonce publiée par zCorrecteurs.fr';
	protected $link = URL_SITE;

	public function homeAction()
	{
		return $this->execute();
	}

	protected function getItems($object)
	{
		$registry  = $this->get('zco_core.registry');
		$quel_bloc = $registry->get('bloc_accueil');

		//Recrutements
		if ($quel_bloc == 'recrutement' && verifier('recrutements_voir'))
		{
			$cache = verifier('recrutements_voir_prives') ? 'liste_recrutements_prives' : 'liste_recrutements_publics';
			include(BASEPATH.'/src/Zco/Bundle/RecrutementBundle/modeles/recrutements.php');
			if(($ListerRecrutements = $this->get('zco_core.cache')->get($cache)) === false)
			{
				$ListerRecrutements = ListerRecrutements();
				$this->get('zco_core.cache')->set($cache, $ListerRecrutements, 0);
			}

			$description = '<ul>';
			foreach($ListerRecrutements as $r)
			{
				$description .= sprintf('<li><a href="%s/recrutement/recrutement-%s-%s.html">%s</a>
					(<span style="color: %s;">%s</span>)</li>',
					URL_SITE,
					$r['recrutement_id'],
					rewrite($r['recrutement_nom']),
					htmlspecialchars($r['recrutement_nom']),
					$r['groupe_class'],
					htmlspecialchars($r['groupe_nom']));
			}
			$description .= '</ul>';
			$content = array('title' => 'Recrutements en cours', 'content' => $description, 'link' => URL_SITE.'/recrutement/');
		 }
		 //Quiz du moment
		 elseif($quel_bloc == 'quiz' && verifier('quiz_voir'))
		 {
			$quiz_semaine = $registry->get('accueil_quiz');
			$description = '';
			if(!empty($quiz_semaine['image']))
				$description .= sprintf('<img style="float: right;" src="%s" alt="" /></a>',
					htmlspecialchars($quiz_semaine['image']));

			$description .= sprintf('Le quiz suivant dans la catégorie « %s »
			est actuellement mis en valeur par l\'équipe du site :<br /><br />
			<strong><a href="%s/quiz/quiz-%s-%s.html">%s</a></strong>',
				htmlspecialchars($quiz_semaine['cat_nom']),
				URL_SITE,
				$quiz_semaine['quiz_id'],
				rewrite($quiz_semaine['quiz_nom']),
				htmlspecialchars($quiz_semaine['quiz_nom']));
			if(!empty($quiz_semaine['quiz_description']))
				$description .= '<br />'.htmlspecialchars($quiz_semaine['quiz_description']);
			$content = array('title' => 'Quiz de la semaine', 'content' => $description,
				'link' => sprintf('%s/quiz/quiz-%s-%s.html',
					URL_SITE,
					$quiz_semaine['quiz_id'],
					rewrite($quiz_semaine['quiz_nom']))
			);
		 }
		 //Sujet du moment
		 elseif($quel_bloc == 'sujet' && verifier('voir_sujets'))
		 {
			 $sujet_semaine = $registry->get('accueil_sujet');
			 $description = '';
			 if(!empty($sujet_semaine['image']))
				$description .= sprintf('<img style="float: right;" src="%s" alt="" />',
					htmlspecialchars($sujet_semaine['image']));

			$description .= sprintf('Le sujet suivant du forum « %s »
				est actuellement mis en valeur par l\'équipe du site :<br /><br />
				<strong><a href="%s/forum/sujet-%s-%s.html">%s</a></strong>',
				htmlspecialchars($sujet_semaine['cat_nom']),
				URL_SITE,
				$sujet_semaine['sujet_id'],
				rewrite($sujet_semaine['sujet_titre']),
				htmlspecialchars($sujet_semaine['sujet_titre']));
			if(!empty($sujet_semaine['sujet_sous_titre']))
				$description .= '<br />'.htmlspecialchars($sujet_semaine['sujet_sous_titre']);
			$content = array('title' => 'Sujet du moment', 'content' => $description,
				'link' => sprintf('%s/forum/sujet-%s-%s.html',
					URL_SITE,
					$sujet_semaine['sujet_id'],
					rewrite($sujet_semaine['sujet_titre']))
			);
		 }
		 //Sondage
		 elseif($quel_bloc == 'sondage' && verifier('sondages_voir'))
		 {
		 	$question = \Doctrine_Core::getTable('SondageQuestion')->getAccueil(-1, null);
			$description = sprintf('Un sondage est en cours : '
				.'<strong><a href="%s/sondages/sondage-%s-%s.html">%s</a></strong><br /><br />'
				.'Connectez-vous pour voter !',
				URL_SITE,
				$question['sondage_id'],
				rewrite($question->Sondage['nom']),
				htmlspecialchars($question->Sondage['nom'])
			);
			$content = array('title' => 'Sondage', 'content' => $description,
				'link' => sprintf('%s/sondages/sondage-%s-%s.html',
					URL_SITE,
					$question['sondage_id'],
					rewrite($question->Sondage['nom'])
				)
			);
		 }
		//Billet du moment
		elseif($quel_bloc == 'billet')
		{
			include(BASEPATH.'/src/Zco/Bundle/BlogBundle/modeles/blog.php');
			$accueil_billet = $registry->get('accueil_billet');
			$InfosBillet = InfosBillet($accueil_billet['billet_id']);
			$parser = $this->get('zco_parser.parser');
			$description = sprintf('Un billet est actuellement mis en valeur : '
				.'<strong><a href="%s/blog/billet-%s-%s.html">%s</a>.</strong><br /><hr />%s',
				URL_SITE,
				$accueil_billet['billet_id'],
				rewrite($InfosBillet[0]['version_titre']),
				htmlspecialchars($InfosBillet[0]['version_titre']),
				$parser->parse($InfosBillet[0]['version_intro'], array(
					'files.entity_id' => $billet_hasard,
					'files.entity_class' => 'Blog',
					'files.part' => 1,
				))
				."\n\n".
				$parser->parse($InfosBillet[0]['version_texte'], array(
					'files.entity_id' => $billet_hasard,
					'files.entity_class' => 'Blog',
					'files.part' => 2,
				))
			);
			$content = array('title' => 'Billet du moment', 'content' => $description,
				'link' => sprintf('%s/blog/billet-%s-%s.html',
					URL_SITE,
					$accueil_billet['billet_id'],
					rewrite($InfosBillet[0]['version_titre'])
				)
			);
		}
		//Billet au hasard
		elseif($quel_bloc == 'billet_hasard')
		{
			include(BASEPATH.'/src/Zco/Bundle/BlogBundle/modeles/blog.php');
			$billet_hasard = $this->get('zco_core.cache')->get('billet_hasard');
			$InfosBillet = InfosBillet($billet_hasard);
			$parser = $this->get('zco_parser.parser');
			$description = sprintf('Un billet aléatoire est sélectionné toutes les %d minutes. '.
			'Actuellement il s\'agit de <strong><a href="%s/blog/billet-%s-%s.html">%s</a></strong>.<br /><hr />%s',
				TEMPS_BILLET_HASARD,
				URL_SITE,
				$billet_hasard,
				rewrite($InfosBillet[0]['version_titre']),
				htmlspecialchars($InfosBillet[0]['version_titre']),
				$parser->parse($InfosBillet[0]['version_intro'], array(
					'files.entity_id' => $billet_hasard,
					'files.entity_class' => 'Blog',
					'files.part' => 1,
				))
				."\n\n".
				$parser->parse($InfosBillet[0]['version_texte'], array(
					'files.entity_id' => $billet_hasard,
					'files.entity_class' => 'Blog',
					'files.part' => 2,
				))
			);
			$content = array('title' => 'Billet du moment', 'content' => $description,
				'link' => sprintf('%s/blog/billet-%s-%s.html',
					URL_SITE,
					$billet_hasard,
					rewrite($InfosBillet[0]['version_titre'])
				)
			);
		}
		//Annonces
		else
		{
			$Informations = $registry->get('accueil_informations');
			$content = array('title' => 'Annonces', 'content' => $this->get('zco_parser.parser')->parse($Informations), 'link' => URL_SITE);
		}

		return array($content);
	}

	protected function getItemTitle($item)
	{
		return $item['title'];
	}

	protected function getItemContent($item)
	{
		return $item['content'];
	}

	protected function getItemLink($item)
	{
		return $item['link'];
	}

	protected function getItemGuid($item)
	{
		return $item['link'];
	}

	protected function getAuthors($item)
	{
		return array(array('zCorrecteurs.fr', 'contact@zcorrecteurs.fr'));
	}

	protected function getUpdated($object)
	{
		if(!$f = $this->get('zco_core.cache')->get('accueil_flux_maj'))
			$f = $this->get('zco_core.cache')->set('accueil_flux_maj', date('c'), 0);
		return $f;
	}
}
