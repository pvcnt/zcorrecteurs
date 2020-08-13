<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2020 Corrigraphie
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

use Doctrine\Common\Cache\Cache;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Zco\Bundle\CoreBundle\Registry;
use Zco\Bundle\InformationsBundle\Event\FilterSitemapEvent;
use Zco\Bundle\InformationsBundle\InformationsEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contrôleur gérant les pages génériques d'information du site (pages
 * statiques, plan du site, etc.).
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class DefaultController extends Controller
{
	/**
	 * Affichage d'un sitemap pour le site.
	 *
	 * @return Response
	 */
	public function sitemapAction()
	{
		$cache = $this->container->get('zco_core.cache');
		if (($content = $cache->get('zco_informations.sitemap')) === false)
		{
			$xml = new \DomDocument();
			$xml->formatOutput = true;
			
			$urlset = $xml->createElement('urlset');
			$urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
			$urlset->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
			$urlset->setAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9
				http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');
			$urlset = $xml->appendChild($urlset);
			
			$dispatcher = $this->container->get('event_dispatcher');
			$event = new FilterSitemapEvent();
			$dispatcher->dispatch(InformationsEvents::SITEMAP, $event);
			
			//Ajout des pages au sitemap
			foreach ($event->getLinks() as $link => $options)
			{
				$url = $xml->createElement('url');
				$url = $urlset->appendChild($url);

				$loc = $xml->createElement('loc');
				$loc = $url->appendChild($loc);
				$loc->appendChild($xml->createTextNode($link));

				$changefreq = $xml->createElement('changefreq');
				$changefreq = $url->appendChild($changefreq);
				$changefreq->appendChild($xml->createTextNode($options['changefreq']));

				$priority = $xml->createElement('priority');
				$priority = $url->appendChild($priority);
				$priority->appendChild($xml->createTextNode($options['priority']));
			}
			
			//Sauvegarde du XML
			$content = $xml->saveXML();
			$cache->set('zco_informations.sitemap', $content, 3600 * 24);
		}
		
		$response = new Response($content);
		$response->headers->set('Content-type',  'text/xml');
		
		return $response;
	}
	
	public function ajaxParseZcodeAction(Request $request)
	{
		return new Response($this->get('zco_parser.parser')->parse($_POST['texte']));

	}

	public function ajaxSaveZformAction(Request $request)
	{
		if (!verifier('connecte'))
		{
			return new Response('ERROR');
		}
		
		$backup = new \ZformBackup();
		$backup->setUrl($request->request->get('url'));
		$backup->setUserId($_SESSION['id']);
		$backup->setContent($request->request->get('texte'));
		$backup->save();

		return new Response('OK');
	}
	
	/**
	 * Action permettant l'édition des annonces en page d'accueil.
	 */
	public function editerAnnoncesAction()
	{
        if (!verifier('gerer_breve_accueil')) {
            throw new AccessDeniedHttpException();
        }
	    /** @var Registry $registry */
		$registry = $this->get('zco_core.registry');

		//--- Si on veut modifier le bloc ---
		$bloc_accueil = $registry->get('bloc_accueil');
		if(isset($_POST['choix_bloc']))
		{
			$registry->set('bloc_accueil', $_POST['choix_bloc']);
			return redirect('Le contenu du bloc à tout faire a bien été changé.');
		}

		//--- Cas de l'annonce personnalisée ---
		$texte_zform = $registry->get('accueil_informations');
		if(isset($_POST['texte']))
		{
			$registry->set('accueil_informations', $_POST['texte']);
			return redirect('Le contenu du bloc à tout faire a bien été changé.');
		}

		//Cas du sujet mis en valeur
		//Inclusion des modèles
		include(BASEPATH.'/src/Zco/Bundle/ForumBundle/modeles/sujets.php');
		include(BASEPATH.'/src/Zco/Bundle/ForumBundle/modeles/forums.php');

		$infos_sujet = $registry->get('accueil_sujet');
		if(empty($infos_sujet)) $infos_sujet = array();
		$image_sujet = array_key_exists('image', $infos_sujet) ? $infos_sujet['image'] : '';

		if(!empty($_POST['sujet']))
		{
			$choix_sujets = ListerSujetsTitre($_POST['sujet']);
			if(count($choix_sujets) == 1)
			{
				$sujet = array(
					'sujet_id' => $choix_sujets[0]['sujet_id'],
					'sujet_titre' => $choix_sujets[0]['sujet_titre'],
					'sujet_sous_titre' => $choix_sujets[0]['sujet_sous_titre'],
					'cat_id' => $choix_sujets[0]['cat_id'],
					'cat_nom' => $choix_sujets[0]['cat_nom'],
					'image' => $image_sujet,
				);
				$registry->set('accueil_sujet', $sujet);
				return redirect('Le contenu du bloc à tout faire a bien été changé.');
			}
		}
		if(!empty($_GET['sujet']) && is_numeric($_GET['sujet']))
		{
			$sujet = InfosSujet($_GET['sujet']);
			if(!empty($sujet))
			{
				$cat = InfosCategorie($sujet['sujet_forum_id']);
				$sujet = array(
					'sujet_id' => $sujet['sujet_id'],
					'sujet_titre' => $sujet['sujet_titre'],
					'sujet_sous_titre' => $sujet['sujet_sous_titre'],
					'cat_id' => $sujet['sujet_forum_id'],
					'cat_nom' => $cat['cat_nom'],
					'image' => $image_sujet,
				);
				$registry->set('accueil_sujet', $sujet);
				return redirect('Le contenu du bloc à tout faire a bien été changé.');
			}
		}
		if(isset($_POST['image_sujet']))
		{
			$infos_sujet['image'] = $_POST['image_sujet'];
			$registry->set('accueil_sujet', $infos_sujet);
			return redirect('Le contenu du bloc à tout faire a bien été changé.');
		}

		//--- Cas du quiz mis en valeur ---
		$infos_quiz = $registry->get('accueil_quiz');
		if(empty($infos_quiz)) $infos_quiz = array();
		$image_quiz = array_key_exists('image', $infos_quiz) ? $infos_quiz['image'] : '';

		if(!empty($_POST['quiz']))
		{
			$choix_quiz = Doctrine_Core::getTable('Quiz')->findByNom($_POST['quiz']);
			if(count($choix_quiz) == 1)
			{
				$quiz = array(
					'id' => $choix_quiz[0]['id'],
					'nom' => $choix_quiz[0]['nom'],
					'description' => $choix_quiz[0]['description'],
					'image' => $image_quiz,
					'Categorie' => array(
						'id' => $choix_quiz[0]->Categorie['id'],
						'nom' => $choix_quiz[0]->Categorie['nom'],
					),
				);
				$registry->set('accueil_quiz', $quiz);
				return redirect('Le contenu du bloc à tout faire a bien été changé.');
			}
		}
		if(!empty($_GET['quiz']) && is_numeric($_GET['quiz']))
		{
			$choix_quiz = Doctrine_Core::getTable('Quiz')->find($_GET['quiz']);
			if($choix_quiz !== false)
			{
				$quiz = array(
					'id' => $choix_quiz['id'],
					'nom' => $choix_quiz['nom'],
					'description' => $choix_quiz['description'],
					'image' => $image_quiz,
					'Categorie' => array(
						'id' => $choix_quiz->Categorie['id'],
						'nom' => $choix_quiz->Categorie['nom'],
					),
				);
				$registry->set('accueil_quiz', $quiz);
				return redirect('Le contenu du bloc à tout faire a bien été changé.');
			}
		}
		if(isset($_POST['image_quiz']))
		{
			$infos_quiz['image'] = $_POST['image_quiz'];
			$registry->set('accueil_quiz', $infos_quiz);
            return redirect('Le contenu du bloc à tout faire a bien été changé.');
		}

		//--- Cas du blog mis en valeur ---
		//Inclusion des modèles
		include(BASEPATH.'/src/Zco/Bundle/BlogBundle/modeles/blog.php');

		$infos_billet = $registry->get('accueil_billet');
		if(empty($infos_billet)) $infos_billet = array();
		$image_billet = array_key_exists('image', $infos_billet) ? $infos_billet['image'] : '';

		if(!empty($_POST['billet']))
		{
			$choix_billet = ChercherBillets($_POST['billet']);
			if(count($choix_billet) == 1)
			{
				$billet = array(
					'billet_id' => $choix_billet[0]['blog_id'],
					'billet_nom' => $choix_billet[0]['version_titre'],
					'cat_nom' => $choix_billet[0]['cat_nom']
				);
				$registry->set('accueil_billet', $billet);
                return redirect('Le contenu du bloc à tout faire a bien été changé.');
			}
		}
		if(!empty($_GET['billet']))
		{
			$billet = InfosBillet($_GET['billet']);
			if(!empty($billet))
			{
				$billet = array(
					'billet_id' => $billet[0]['blog_id'],
					'billet_nom' => $billet[0]['version_titre'],
					'cat_nom' => $billet[0]['cat_nom']
				);
				$registry->set('accueil_billet', $billet);
				return redirect('Le contenu du bloc à tout faire a bien été changé.');
			}
		}

		$categories = ListerEnfants(GetIdCategorie('blog'), false);
		$categories_actuelles = $registry->get('categories_billet_hasard');

		if(isset($_POST['categories']))
		{
			$registry->set('categories_billet_hasard', $_POST['categories']);
			return redirect('Le contenu du bloc à tout faire a bien été changé.');
		}

		if(isset($_GET['supprimer_cache']))
		{
			$this->get('zco_core.cache')->delete('billet_hasard');
			
			return redirect('Le billet au hasard a bien été régénéré.');
		}


		// Twitter
		$accueil_tweets = $registry->get('accueil_tweets');
		if(isset($_POST['tweets']))
		{
			$nb = (int)$_POST['tweets'];
			$nb < 1  && $nb = 1;
			$nb > 10 && $nb = 10;

			$registry->set('accueil_tweets', $nb);
			return redirect('Le contenu du bloc à tout faire a bien été changé.');
		}
		
		// Dictée
		include(BASEPATH.'/src/Zco/Bundle/DicteesBundle/modeles/dictees.php');
		
		$listDictees = array();
		if(isset($_POST['dictee']))
		{
			$listDictees = searchDictees($_POST['dictee']);
			if ( sizeof($listDictees) == 1 )
			{
				$dictee = Dictee($listDictees[0]['id']);
				$registry->set('dictee_en_avant', $dictee);
				return redirect('Le contenu du bloc à tout faire a bien été changé.');
			}
		}
		
		if(!empty($_GET['dictee']) && is_numeric($_GET['dictee']))
		{
			settype($_GET['dictee'],'int');
			$dictee = Dictee($_GET['dictee']);
			$registry->set('dictee_en_avant', $dictee);
			return redirect('Le contenu du bloc à tout faire a bien été changé.');
		}
		
		$selectDictee = $registry->get('dictee_en_avant');
		if ( !$selectDictee ) {
			$selectDictee = null;
		}

		//Inclusion de la vue
		fil_ariane('Modifier les annonces');
		
		return render_to_response(compact(
			'bloc_accueil',
			'texte_zform',
			'choix_quiz',
			'infos_quiz',
			'image_quiz',
			'infos_sujet',
			'image_sujet',
			'infos_billet',
			'image_billet',
			'choix_billet',
			'categories',
			'categories_actuelles',
			'accueil_tweets',
			'selectDictee',
			'listDictees'
		));
	}
	
	/**
	 * Action affichant une page d'erreur demandée par Apache.
	 */
	public function erreurAction()
	{
	    $erreur = !empty($_GET['erreur']) && in_array($_GET['erreur'], array(403, 404, 500)) ? $_GET['erreur'] : 404;
	    
	    return render_to_response('TwigBundle:Exception:error'.$erreur.'.html.twig');
	}
}