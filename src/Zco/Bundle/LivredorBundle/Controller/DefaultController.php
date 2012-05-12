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

namespace Zco\Bundle\LivredorBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Zco\Bundle\CoreBundle\Paginator\Paginator;

/**
 * Contrôleur s'occupant des actions concernant la gestion du livre d'or.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class DefaultController extends Controller
{
	/**
	 * Affiche la liste des messages du livre d'or.
	 */
	public function indexAction(Request $request)
	{
		//Si on veut écrire un message
		if (isset($_POST['submit']))
		{
			return new RedirectResponse('ecrire.html');
		}
		
		\zCorrecteurs::VerifierFormatageUrl(null, false, false, 1);
		$page = !empty($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1;
		if ($page > 1)
		{
		    \Page::$titre .= ' - Page '.$page;
	    }

		//On récupère les messages du livre d'or
		$Messages = \Doctrine_Core::getTable('Livredor')->Messages();
		$paginator = new Paginator($Messages, 20);
		try
		{
		    $pager = $paginator->createView($page);
		    $pager->setUri('index-p%s.html');
	    }
	    catch (\InvalidArgumentException $e)
		{
		    throw new NotFoundHttpException('La page demandée n\'existe pas.');
		}

		$NoteMoyenne = \Doctrine_Core::getTable('Livredor')->NoteMoyenne();
		$VerifieDernierPost = \Doctrine_Core::getTable('Livredor')->VerifierDernierPost($request->getClientIp(true));

		//Inclusion de la vue
		fil_ariane('Liste de tous les messages');
		$this->get('zco_vitesse.resource_manager')->requireResource(
		    '@ZcoLivredorBundle/Resources/public/css/livredor.css'
		);
		
		return render_to_response(array(
			'pager' => $pager,
			'NoteMoyenne' => $NoteMoyenne,
			'VerifieDernierPost' => $VerifieDernierPost,
		));
	}

	/**
	 * Enregistre un nouveau message dans le livre d'or.
	 */
	public function ecrireAction(Request $request)
	{
		\zCorrecteurs::VerifierFormatageUrl();
		\Page::$titre = 'Déposer un message dans le livre d\'or';

		//Si on a le droit de poster
		if (\Doctrine_Core::getTable('Livredor')->VerifierDernierPost($request->getClientIp(true)))
		{
			//Si on veut ajouter un message
			if (!empty($_POST['message']) && !empty($_POST['note']) && is_numeric($_POST['note']))
			{
				$_POST['note'] > 5 && $_POST['note'] = 5;
				$_POST['note'] < 1 && $_POST['note'] = 1;

				$msg = new \Livredor();
				$msg['note']           = $_POST['note'];
				$msg['message']        = $_POST['message'];
				$msg['utilisateur_id'] = $_SESSION['id'];
				$msg['ip']             = ip2long($request->getClientIp(true));
				$msg->save();
				
				return redirect(2, 'index.html');
			}

			//Inclusion de la vue
			fil_ariane('Ajouter un nouveau message');
			$this->get('zco_vitesse.resource_manager')->requireResources(array(
			    '@ZcoCoreBundle/Resources/public/css/zform.css',
			    '@ZcoLivredorBundle/Resources/public/css/livredor.css',
			));
			
			return render_to_response(array());
		}
		else
		{
			return redirect(4, 'index.html', MSG_ERROR);
		}
	}

	/**
	 * Modifie un message du livre d'or.
	 */
	public function editerAction()
	{
	    \zCorrecteurs::VerifierFormatageUrl(null, true);
		\Page::$titre = 'Modifier un message du livre d\'or';
		
		//Si on a bien envoyé un message
		if (!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$msg = \Doctrine_Core::getTable('Livredor')->find($_GET['id']);
			if (!$msg)
			{
				return redirect(6, 'index.html', MSG_ERROR);
			}

			//Si on veut éditer le message
			if(!empty($_POST['message']) && !empty($_POST['note']) && is_numeric($_POST['note']))
			{
				$msg['message'] = $_POST['message'];
				$msg['note']    = $_POST['note'];
				$msg->save();
				
				return redirect(3, 'index.html');
			}

			//Inclusion de la vue
			fil_ariane('Modifier un message');
			$this->get('zco_vitesse.resource_manager')->requireResource(
    		    '@ZcoLivredorBundle/Resources/public/css/livredor.css'
    		);
			
			return render_to_response(array('msg' => $msg));
		}
		else
			return redirect(6, 'index.html', MSG_ERROR);
	}

	/**
	 * Supprime un message du livre d'or.
	 */
	public function supprimerAction()
	{
		\Page::$titre = 'Supprimer un message du livre d\'or';
		\zCorrecteurs::VerifierFormatageUrl(null, true);

		//Si on a bien envoyé un message
		if (!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$msg = \Doctrine_Core::getTable('Livredor')->find($_GET['id']);
			if (!$msg)
			{
				return redirect(6, 'index.html', MSG_ERROR);
			}

			//Si on veut supprimer le message
			if(isset($_POST['confirmer']))
			{
				$msg->delete();

				return redirect(5, 'index.html');
			}
			//Si on annule
			elseif(isset($_POST['annuler']))
			{
				return new RedirectResponse('index.html');
			}

			//Inclusion de la vue
			fil_ariane('Supprimer un message');
			$this->get('zco_vitesse.resource_manager')->requireResource(
    		    '@ZcoLivredorBundle/Resources/public/css/livredor.css'
    		);
			
			return render_to_response(array('msg' => $msg));
		}
		else
			return redirect(6, 'index.html');
	}
}
