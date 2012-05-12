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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Contrôleur gérant les opérations liées à la publicité et aux partenaires.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class PubliciteActions extends Controller
{
	/**
	 * Modifie le nom d'une campagne de publicité en Ajax.
	 * @return Response
	 */
	public function executeAjaxModifierNomCampagne()
	{
		$campagne = Doctrine_Core::getTable('PubliciteCampagne')->find($_POST['id']);
		if (verifier('publicite_voir') || $campagne['utilisateur_id'] == $_SESSION['id'])
		{
			$campagne['nom'] = $_POST['nom'];
			$campagne->save();
			
			return new Response(htmlspecialchars($campagne['nom']));
		}
		else
			return new Response('ERREUR');
	}

	/**
	 * Modifie l'état d'une campagne de publicité en Ajax.
	 * @return Response
	 */
	public function executeAjaxModifierEtatCampagne()
	{
		$campagne = Doctrine_Core::getTable('PubliciteCampagne')->find($_POST['id']);
		if (verifier('publicite_editer_etat') || ($campagne['utilisateur_id'] == $_SESSION['id'] && verifier('publicite_editer_etat_siens')))
		{
			$campagne['etat'] = $_POST['etat'];
			$campagne->save();
			$this->get('zco_core.cache')->delete('pub-*');
			
			return new Response($campagne->getEtatFormat());
		}
		else
			return new Response('ERREUR');
	}

	/**
	 * Modifie les dates d'une campagne de publicité en Ajax.
	 * @return Response
	 */
	public function executeAjaxModifierDatesCampagne()
	{
		$campagne = Doctrine_Core::getTable('PubliciteCampagne')->find($_POST['id']);
		if (verifier('publicite_editer_etat') || ($campagne['utilisateur_id'] == $_SESSION['id'] && verifier('publicite_editer_etat_siens')))
		{
			$campagne['date_debut'] = $_POST['date_debut'];
			$campagne['date_fin'] = !empty($_POST['date_fin']) ? $_POST['date_fin'] : null;
			$campagne->save();
			$this->get('zco_core.cache')->delete('pub-*');
			
			return new Response(dateformat($campagne['date_debut']).' - '.dateformat($campagne['date_fin']));
		}
		else
			return new Response('ERREUR');
	}

	/**
	 * Modifie l'état d'une publicité en Ajax.
	 * @return Response
	 */
	public function executeAjaxModifierEtatPublicite()
	{
		$publicite = Doctrine_Core::getTable('Publicite')->find($_POST['id']);
		if (verifier('publicite_activer') || ($publicite->Campagne['utilisateur_id'] == $_SESSION['id'] && verifier('publicite_activer_siens')))
		{
			$publicite['actif'] = ($_POST['etat'] == 'oui');
			$publicite->save();
			$this->get('zco_core.cache')->delete('pub-'.$publicite['emplacement']);
			return new Response($publicite->getEtatFormat());
		}
		else
			return new Response('ERREUR');
	}

	/**
	 * Enregistre un clic sur une publicité. Met à jour les compteurs de clics
	 * et d'affichage de la publicité et de la campagne associée.
	 *
	 * @return Response
	 */
	public function executeAjaxPubClic()
	{
		//Vérification de l'anti-flood sur les publicités.
		//Maximum : un clic par heure par publicité par session.
		if (!empty($_SESSION['pub_clic'][$_POST['id']]) && $_SESSION['pub_clic'][$_POST['id']] + 3600 > time())
		{
			return new Response('OK');
		}

		$publicite = Doctrine_Core::getTable('Publicite')->find($_POST['id']);

		if ($publicite !== false)
		{
			$campagne = Doctrine_Core::getTable('PubliciteCampagne')->find($publicite['campagne_id']);
			$nbv = $this->get('zco_core.cache')->get('pub_nbv-'.$publicite['id'], 0);

			$clic = new PubliciteClic();
			$clic['publicite_id'] = $publicite['id'];
			$clic['categorie_id'] = $_POST['cat'];
			$clic['pays']         = isset($_SESSION['pays']) ? $_SESSION['pays'] : 'Inconnu';
			$clic['age']          = isset($_SESSION['age']) ? $_SESSION['age'] : 0;
			$clic['ip']           = ip2long($this->get('request')->getClientIp(true));
			$clic['date']         = new Doctrine_Expression('NOW()');
			$clic->save();

			$ret = Doctrine_Query::create()
				->update('PubliciteStat')
				->set('nb_clics', 'nb_clics + 1')
				->set('nb_affichages', 'nb_affichages + ?', $nbv)
				->where('publicite_id = ?', $publicite['id'])
				->andWhere('date = ?', date('Y-m-d'))
				->execute();
			
			if (!$ret)
			{
				$stat = new PubliciteStat();
				$stat['publicite_id']  = $publicite['id'];
				$stat['date']          = date('Y-m-d');
				$stat['nb_clics']      = 1;
				$stat['nb_affichages'] = $nbv;
				$stat->save();
			}

			$publicite['nb_clics']      = $publicite['nb_clics'] + 1;
			$publicite['nb_affichages'] = $publicite['nb_affichages'] + $nbv;
			$publicite->save();

			$campagne['nb_clics']      = $campagne['nb_clics'] + 1;
			$campagne['nb_affichages'] = $campagne['nb_affichages'] + $nbv;
			$campagne->save();

			$this->get('zco_core.cache')->delete('pub_nbv-'.$publicite['id']);
			$_SESSION['pub_clic'][$_POST['id']] = time();
			
			return new Response('OK');
		}
		else
		{
			return new Response('ERREUR');
		}
	}
}
