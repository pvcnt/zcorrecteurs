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

namespace Zco\Bundle\AnnoncesBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Contrôleur gérant toutes les actions liées aux annonces pouvant apparaître 
 * en haut de chaque page du site.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class DefaultController extends Controller
{
    /**
     * Contrôleur en charge de la prévisualisation d'une annonce.
     */
    public function ajaxPrevisualiserAction()
    {
		if (empty($_POST['contenu']))
		{
			return new Response('');
		}
		
		return new Response(\Doctrine_Core::getTable('Annonce')->genererHTML(array(
			'id'              => !empty($_POST['id']) ? $_POST['id'] : 0,
			'contenu'         => $_POST['contenu'],
			'url_destination' => '/',
		)));
    }
    
    /**
     * Contrôleur en charge de retourner le contenu d'une bannière adaptée 
     * au profil de l'utilisateur. Il est possible de forcer certains 
     * paramètres via des paramètres passés dans l'adresse de la page.
     */
	public function afficherAction()
	{
		$annonce = !empty($_GET['annonce']) ? $_GET['annonce'] : null;
		$params  = array();
		if (!empty($_GET['pays']) && verifier('annonces_publier'))
		{
			$objet = \Doctrine_Core::getTable('Pays')->findOneByCode(strtoupper($_GET['pays']));
			if ($objet)
			{
				$params['pays'] = $objet['id'];
			}
		}
		if (isset($_GET['groupe']) && verifier('annonces_publier'))
		{
			$params['groupes'] = array((int) $_GET['groupe']);
		}
		if (!empty($_GET['categorie']))
		{
			$params['categorie'] = (int) $_GET['categorie'];
		}
		
		return new Response(\Doctrine_Core::getTable('Annonce')->recuperer($params, $annonce));
	}
	
	/**
     * Contrôleur en charge de l'ajout d'une nouvelle annonce. Tous les paramètres 
     * de ciblage peuvent être configurés dès l'ajout.
     */
	public function ajouterAction()
	{
	    \Page::$titre = 'Ajouter une annonce';
	    
		if (!isset($_POST['previsualiser']) && !empty($_POST['nom']) && !empty($_POST['contenu']))
		{
			$annonce = new \Annonce();
			$annonce->utilisateur_id   	= $_SESSION['id'];
			$annonce->nom			  	= $_POST['nom'];
			$annonce->date_debut	   	= $_POST['date_debut'];
			$annonce->date_fin			= !empty($_POST['date_fin']) ? $_POST['date_fin'] : new \Doctrine_Expression('NULL');
			$annonce->poids				= $_POST['poids'];
			$annonce->actif				= (isset($_POST['actif']) && verifier('annonces_publier'));
			$annonce->contenu		   	= $_POST['contenu'];
			$annonce->aff_pays_inconnu 	= isset($_POST['aff_pays_inconnu']);
			$annonce->url_destination  	= $_POST['url_destination'];
			$annonce->save();
			
			//Ciblage par pays.
			if (isset($_POST['cibler_pays']))
			{
				foreach ($_POST['pays'] as $pays)
				{
					$_pays = new \AnnoncePays();
					$_pays['annonce_id']  = $annonce['id'];
					$_pays['pays_id']	 = $pays;
					$_pays->save();
					$_pays = null;
				}
			}
			
			//Ciblage par groupe.
			if (isset($_POST['cibler_groupes']))
			{
				foreach ($_POST['groupes'] as $groupe)
				{
					$_groupe = new \AnnonceGroupe();
					$_groupe['annonce_id']  = $annonce['id'];
					$_groupe['groupe_id']   = $groupe;
					$_groupe->save();
					$_groupe = null;
				}
			}
			
			//Ciblage par section.
			if (isset($_POST['cibler_categories']))
			{
				foreach ($_POST['categories'] as $section)
				{
					$_section = new \AnnonceCategorie();
					$_section['annonce_id']   = $annonce['id'];
					$_section['categorie_id'] = $section;
					$_section->save();
					$_section = null;
				}
			}
			
			//Ciblage par domaine.
			if (isset($_POST['cibler_domaines']))
			{
				foreach ($_POST['domaines'] as $domaine)
				{
					$_domaine = new \AnnonceDomaine();
					$_domaine['annonce_id']  = $annonce['id'];
					$_domaine['domaine']	 = $domaine;
					$_domaine->save();
					$_domaine = null;
				}
			}
			
			//On n'oublie pas de supprimer le cache des annonces actives.
			if ($annonce['actif'])
			{
				$this->get('zco_core.cache')->delete('annonces');
			}
			
			return redirect(2, 'index.html');
		}
		
		if (!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$annonce = \Doctrine_Core::getTable('Annonce')->find($_GET['id']);
			if ($annonce)
			{
				$annonce['nom'] = $annonce['nom'].' — copie';
			}
		}
		
		return render_to_response(array(
			'annonce'        => !empty($annonce) ? $annonce : null,
			'attrPays'	   	 => !empty($annonce) ? $annonce->getPaysId() : null,
			'attrCategories' => !empty($annonce) ? $annonce->getCategoriesId() : null,
			'attrGroupes'	 => !empty($annonce) ? $annonce->getGroupesId() : null,
			'attrDomaines'   => !empty($annonce) ? $annonce->getDomainesId() : null,
			'categories'    => \Doctrine_Core::getTable('Categorie')->getCategoriesCiblables(false),
			'pays'	        => \Doctrine_Core::getTable('Pays')->findAll(),
			'groupes'	    => \Doctrine_Core::getTable('Groupe')->findAll(),
			'domaines'      => array('www.zcorrecteurs.fr', 'test.zcorrecteurs.fr'),
		));
	}
	
	/**
     * Contrôleur en charge de la vérification de l'allocation des annonces 
     * pour un certain profil d'utilisateur.
     */
	public function allocationAction()
	{
	    \Page::$titre = 'Allocation des annonces';
	    
		if (isset($_POST['submit']))
		{
			$attrPays	   = !empty($_POST['pays']) ? (int) $_POST['pays'] : null;
			$attrGroupe	   = !empty($_POST['groupe']) ? (int) $_POST['groupe'] : null;
			$attrDomaine   = !empty($_POST['domaine']) ? (string) $_POST['domaine'] : null;
			$attrCategorie = !empty($_POST['categorie']) ? (int) $_POST['categorie'] : null;
			
			$annonces = \Doctrine_Core::getTable('Annonce')->chercher(array(
				'pays'  	=> $attrPays, 
				'groupes'   => array($attrGroupe), 
				'domaine'   => $attrDomaine, 
				'categorie' => $attrCategorie,
			));
			
			$sommePoids = 0;
			foreach ($annonces as $annonce)
			{
				$sommePoids += $annonce['poids'];
			}
		}
		
		return render_to_response(array(
			'annonces' 	 	=> isset($_POST['submit']) ? $annonces : null,
			'sommePoids' 	=> isset($_POST['submit']) ? $sommePoids : null,
			'categories'	=> \Doctrine_Core::getTable('Categorie')->getCategoriesCiblables(false),
			'pays'	   		=> \Doctrine_Core::getTable('Pays')->findAll(),
			'groupes'		=> \Doctrine_Core::getTable('Groupe')->findAll(),
			'domaines'  	=> array('www.zcorrecteurs.fr', 'test.zcorrecteurs.fr'),
			'attrPays'   	=> isset($_POST['submit']) ? $attrPays : null,
			'attrCategorie' => isset($_POST['submit']) ? $attrCategorie : null,
			'attrGroupe' 	=> isset($_POST['submit']) ? $attrGroupe : null,
			'attrDomaine' 	=> isset($_POST['submit']) ? $attrDomaine : null,
		));
	}
	
	/**
     * Contrôleur mémorisant le clic sur une annonce et redirigeant ensuite vers 
     * l'adresse appropriée. Une annonce peut avoir plusieurs adresses de redirection 
     * configurée : à ce moment est alors sélectionnée une unique adresse.
     */
	public function clicAction()
    {
        if (!empty($_GET['id']) && is_numeric($_GET['id']))
        {
            $annonce = \Doctrine_Core::getTable('Annonce')->find($_GET['id']);
            if (!empty($annonce))
            {
                $annonce->nb_clics += 1;
                $annonce->save();

				$url = array_map('trim', explode(',', $annonce['url_destination']));
				$url = $url[mt_rand(0, count($url) - 1)];
				
				if (!empty($_GET['_page']))
				{
					$url = str_replace('%page%', substr(urldecode($_GET['_page']), 1), $url);
				}
				
				return new RedirectResponse($url ?: '/');
            }
        }
        
        return new RedirectResponse('/');
    }
    
    /**
     * Contrôlant gérant la demande de fermeture d'une annonce de la part de 
     * l'utilisateur. Cette action est normalement appelée uniquement en asynchrone.
     * Le nombre de demandes de fermetures d'annonces est mémorisé.
     */
    public function fermerAction()
    {
        if (!empty($_GET['id']) && is_numeric($_GET['id']))
        {
            $annonce = \Doctrine_Core::getTable('Annonce')->find($_GET['id']);
            if (!empty($annonce))
            {
                $annonce->nb_fermetures += 1;
                $annonce->save();
                
				//Les annonces masquées sont mémorisées dans un cookie pour une 
				//durée de 6 mois.
                $cookie = !empty($_COOKIE['annonces_masquees']) ? explode('|', $_COOKIE['annonces_masquees']) : array();
                $cookie[] = $annonce['id'];
                $cookie = array_unique($cookie);
                setcookie('annonces_masquees', implode('|', $cookie), time()+3600*24*30*6, '/');
            }
        }
        
        return new RedirectResponse(!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/');
    }
    
    /**
     * Contrôleur gérant l'affichage de toutes les annonces disponibles.
     */
    public function indexAction()
    {
        \Page::$titre = 'Liste des annonces';
        
        if (isset($_POST['modifier']) && (verifier('annonces_publier') || verifier('annonces_modifier')))
        {
            $annonces = \Doctrine_Core::getTable('Annonce')->lister();
            $cache = $this->get('zco_core.cache');
            foreach ($annonces as $annonce)
            {
                $change = false;
                if (verifier('annonces_publier') && $annonce['actif'] != isset($_POST['actif'.$annonce['id']]))
                {
                    $annonce['actif'] = !$annonce['actif'];
                    $change = true;
                }
                if (verifier('annonces_modifier') && $annonce['poids'] != (int) $_POST['poids'.$annonce['id']])
                {
                    $annonce['poids'] = (int) $_POST['poids'.$annonce['id']];
                    $change = true;
                }
                
                if ($change)
                {
                    $annonce->save();
                    $cache->delete('annonce_details-'.$annonce['id']);
                }
            }
            $cache->delete('annonces');
            
            return redirect(5);
        }
        
        return render_to_response(array(
            'annonces' => \Doctrine_Core::getTable('Annonce')->lister(),
        ));
    }
    
    /**
     * Contrôleur en charge de la modification d'une bannière et de ses paramètres 
     * de ciblage.
     */
    public function modifierAction()
	{
		if (!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$annonce = \Doctrine_Core::getTable('Annonce')->find($_GET['id']);
			if (!$annonce)
			{
				return redirect(1, 'index.html', MSG_ERROR);
			}
			
			\Page::$titre = $annonce['nom'];
		
			if (!isset($_POST['previsualiser']) && !empty($_POST['nom']) && !empty($_POST['contenu']))
			{
				$annonce->nom			 	= $_POST['nom'];
				$annonce->date_debut	  	= $_POST['date_debut'];
				$annonce->date_fin			= !empty($_POST['date_fin']) ? $_POST['date_fin'] : new \Doctrine_Expression('NULL');
				$annonce->poids		   		= $_POST['poids'];
				$annonce->contenu		 	= $_POST['contenu'];
				$annonce->aff_pays_inconnu 	= isset($_POST['aff_pays_inconnu']);
				$annonce->url_destination  	= $_POST['url_destination'];
				if (verifier('annonces_publier'))
				{
					$annonce->actif = isset($_POST['actif']);
				}
				$annonce->save();
				$cache = $this->get('zco_core.cache');
				
				foreach (array(
					'pays' => array('champ' => 'pays_id', 'table' => 'AnnoncePays'),
					'categories' => array('champ' => 'categorie_id', 'table' => 'AnnonceCategorie'),
					'groupes' => array('champ' => 'groupe_id', 'table' => 'AnnonceGroupe'),
					'domaines' => array('champ' => 'domaine', 'table' => 'AnnonceDomaine'),
				) as $type => $params)
				{
					$method = 'get'.ucfirst($type).'Id';
					$class = '\\'.$params['table'];
					$elements = $annonce->$method();
					
					//Si on a demandé un ciblage suivant le critère en question, 
					//on commence par enregistrer tous les éléments n'étant pas 
					//actuellement ciblés.
					if (isset($_POST['cibler_'.$type]))
					{
						foreach ($_POST[$type] as $element)
						{
							if (!in_array($element, $elements))
							{
								$_pays = new $class();
								$_pays['annonce_id'] = $annonce['id'];
								$_pays[$params['champ']] = $element;
								$_pays->save();
								$_pays = null;
							}
							unset($elements[$element]);
						}
					}
					//Suppression des éléments de ciblage restants. On doit le faire car soit 
					//ils n'ont pas été sélectionnés lors de la soumission du formulaire, soit 
					//on a annulé le ciblage selon ce critère et on doit alors tous les supprimer 
					//pour signifier cela.
					if (!empty($elements))
					{
						\Doctrine_Query::create()
							->delete($params['table'])
							->where('annonce_id = ?', $annonce['id'])
							->andWhereIn($params['champ'], array_values($elements))
							->execute();
					}
				}
			
				//On n'oublie pas de supprimer le cache de l'annonce et celui 
				//des annonces actives.
				$cache->delete('annonce_details-'.$annonce->id);
				$cache->delete('annonces');
			
				return redirect(3, 'index.html');
			}
			
			return render_to_response(array(
				'annonce'		 => $annonce,
				'attrPays'	   	 => $annonce->getPaysId(),
				'attrCategories' => $annonce->getCategoriesId(),
				'attrGroupes'	 => $annonce->getGroupesId(),
				'attrDomaines'   => $annonce->getDomainesId(),
				'categories'	 => \Doctrine_Core::getTable('Categorie')->getCategoriesCiblables(false),
				'pays'		   	 => \Doctrine_Core::getTable('Pays')->findAll(),
				'groupes'		 => \Doctrine_Core::getTable('Groupe')->findAll(),
				'domaines'	   	 => array('www.zcorrecteurs.fr', 'test.zcorrecteurs.fr'),
			));
		}
		
		return redirect(1, 'index.html', MSG_ERROR);
	}
	
	/**
     * Contrôleur en charge de l'affichage des statistiques de performance 
     * des différentes annonces.
     */
	public function statistiquesAction()
    {
        \Page::$titre = 'Statistiques des annonces';
        
        return render_to_response(array(
            'annonces' => \Doctrine_Core::getTable('Annonce')->lister(),
        ));
    }
	
	/**
	 * Contrôleur en charge de la suppression d'une annonce.
	 */
	public function supprimerAction()
    {
        if (!empty($_GET['id']) && is_numeric($_GET['id']))
        {
            $annonce = \Doctrine_Core::getTable('Annonce')->find($_GET['id']);
            if (!$annonce)
            {
                return redirect(1, 'index.html', MSG_ERROR);
            }
            
            \Page::$titre = $annonce['nom'];
        
            if (isset($_POST['confirmer']))
            {
                $cache = $this->get('zco_core.cache');
                $cache->delete('annonce_details-'.$annonce->id);
                if ($annonce->actif)
                {
                    $cache->delete('annonces');
                }
                $annonce->delete();
            
                return redirect(4, 'index.html');
            }
            elseif (isset($_POST['annuler']))
            {
                return new RedirectResponse('index.html');
            }
            
            return render_to_response(compact('annonce'));
        }
        
        return redirect(1, 'index.html', MSG_ERROR);
    }
}