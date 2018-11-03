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

namespace Zco\Bundle\CoreBundle\Javelin;

/**
 * Assure la liaison avec les comportements Javascripts définis côté client.
 * Voir la bibliothèque Behavior.js pour plus de détails. Code très lourdement 
 * repris de la bibliothèque proposée sur @link{www.http://javelinjs.com}.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class Javelin
{
	protected $onload = array();
	protected $behavior = array();
	protected $dirty = true;
	protected $block = 0;
	protected $resourceManager;
	
	/**
	 * Constructeur.
	 *
	 * @param ResourceManager $resourceManager
	 */
	public function __construct(ResourceManager $resourceManager)
	{
		$this->resourceManager = $resourceManager;
		
		if (isset($_REQUEST['__metablock__']))
		{
			$this->block = (int) $_REQUEST['__metablock__'];
		}
	}

	/**
	 * Destructeur. S'assure que toutes les données enregistrées ont bien été 
	 * été transmises au client.
	 */
	public function __destruct()
	{
		/*if ($this->dirty)
		{
			throw new \LogicException(
				'Javelin has behaviors, metadata or onload functions to include in '.
				'the response but you did not call renderHTMLFooter() or '.
				'renderAjaxResponse() after registering them.'
			);
		}*/
	}

	/**
	 * Enregistre un code Javascript à exécuter au chargement de la page, une 
 	 * fois que l'arbre DOM est prêt.
 	 *
	 * @param string $call Code à exécuter
	 */
	public function onload($call)
	{
		$this->onload[] = 'function(){'.$call.'}';
	}

	/**
	 * Instancie un comportement.
	 * 
	 * @param string $behavior Le nom du comportement
	 * @param array $data Les données associées
	 */
	public function initBehavior($behavior, array $data = array())
	{
		$this->resourceManager->requireResource('vitesse-behavior-'.$behavior);
		$this->behavior[$behavior][] = $data;
	}

	/**
	 * Génère du code HTML à insérer dans le pied de page du site, à côté 
	 * de la balise fermante </body> pour transférer toutes les données 
	 * mémorisées ici au code exécuté côté client.
	 *
	 * @return string Code HTML
	 */
	public function renderHTMLFooter()
	{
		$data = array();

		if ($this->behavior)
		{
			$behavior = json_encode($this->behavior);
			$this->onload('Behavior.init('.$behavior.');');
			$this->behavior = array();
		}

		if ($this->onload)
		{
			foreach ($this->onload as $func)
			{
				$data[] = 'window.addEvent(\'domready\', '.$func.');';
			}
		}

		$this->dirty = false;

		if ($data)
		{
			$data = implode(' ', $data);
			return '<script type="text/javascript">/*<![CDATA[*/'.
				   $data.'/*]]>*/</script>';
		}
		
		return '';
   }

	/**
	 * Génère du code JSON dans un format compréhensible pour transférer 
	 * les méta-données et les comportements mémorisés ici au code exécuté 
	 * côté client.
	 * 
	 * @param  string $payload
	 * @param  string $error
	 * @return string Code JSON
	 */
	public function renderAjaxResponse($payload, $error = null)
	{
		$response = array(
			'error'	=> $error,
			'payload' => $payload,
		);

		if ($this->behavior)
		{
			$response['javelin_behaviors'] = $this->behavior;
			$this->behavior = array();
		}

		/*if ($this->onload)
		{
			throw new \LogicException(
				'Javelin onload functions have been registered, but the response is '.
				'being rendered as an Ajax response. This is invalid; use behaviors '.
				'instead.'
			);
		}*/

		$this->dirty = false;
		
		$response = 'for (;;);'.json_encode($response);
		
		return $response;
   }
}
