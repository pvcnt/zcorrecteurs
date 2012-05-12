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

namespace Zco\Bundle\AdminBundle;

use Zco\Bundle\CoreBundle\Cache\CacheInterface;

/**
 * Classe de comptage des tâches admin.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class Admin
{
	private $taches = array();
	private $time = 600;
	private $cache;

	const MANUAL = 1;
	const AUTO = 2;

	/**
	 * Constructeur.
	 */
	public function __construct(CacheInterface $cache)
	{
		$this->cache = $cache;
		include_once(__DIR__.'/modeles/taches_admin.php');
	}

	/**
	 * Enregistre une certaine tâche.
	 *
	 * @param string $name Le nom de la tâche
	 * @param array $droits Les droits nécessaires pour la voir
	 * @param array $options Des options...
	 */
	public function register($name, $droits, array $options = array())
	{
		if (!is_array($droits))
		{
			$droits = array($droits);
		}
		$type = array_key_exists('type', $options) ? $options['type'] : self::AUTO;
		$count = array_key_exists('count', $options) ? $options['count'] : true;
		
		$this->taches[$name] = array(
			'compteur' => null,
			'type' => $type,
			'droits' => $droits,
			'compteur' => null,
			'count' => $count,
			'refresh' => false,
		);
	}

	/**
	 * Récupère le nombre de tâches en attente d'un certain type.
	 * 
	 * @param  string $name Le nom de la tâche.
	 * @param  boolean $forceRefresh Doit-on forcer le rafraichissement ?
	 * @return integer
	 */
	public function get($name, $forceRefresh = false)
	{
		if (array_key_exists($name, $this->taches))
		{
			//On rafraichit automatiquement si on est sur l'accueil de
			//l'administration.
			$module = \Container::getService('request')->attributes->get('_module');
			if ($module == 'admin')
			{
				$forceRefresh = true;
			}

			//On ne force pas le rafraichissement d'un cache manuel, cela
			//n'a pas de sens.
			if ($this->taches[$name]['type'] == self::MANUAL && $forceRefresh)
			{
				$forceRefresh = false;
			}

			//Si le cache a déjà été rafraichi, ça suffit !
			if ($this->taches[$name]['refresh'] == true && $forceRefresh)
			{
				$forceRefresh = false;
			}

			//Si la donnée est déjà calculée, on la renvoie.
			if (!is_null($this->taches[$name]['compteur']) && !$forceRefresh)
			{
				return $this->taches[$name]['compteur'];
			}

			//Sinon si on peut la récupérer du cache.
			if (($admin = $this->cache->get('taches_admin_'.$name)) !== false && !$forceRefresh)
			{
				$this->taches[$name]['compteur'] = $admin;
				return (int) $admin;
			}

			//Si on doit mettre à jour un compteur automatique.
			elseif ($this->taches[$name]['type'] == self::AUTO)
			{
				if (function_exists($f = 'CompterTaches'.ucfirst($name)))
				{
					$this->write($name, call_user_func($f));
					return $this->taches[$name]['compteur'];
				}
				else
				{
					trigger_error('La fonction de comptage CompterTaches'.ucfirst($name).' n\'existe pas', E_USER_NOTICE);
					$this->write($name, 0);
					
					return 0;
				}
			}

			//Si on doit mettre à jour un compteur manuel.
			else
			{
				$this->write($name, 0);
				return 0;
			}
		}
		
		return 0;
	}

	/**
	 * Force le rafraichissement de toutes les tâches.
	 */
	public function autoRefresh()
	{
		foreach ($this->taches as $key => $value)
		{
			$this->get($key, true);
		}
	}

	/**
	 * Retourne le nombre de tâches en attente pour le visiteur.
	 *
	 * @return integer
	 */
	public function count()
	{
		$count = 0;
		foreach ($this->taches as $key => $value)
		{
			if ($value['count'] == true)
			{
				$current = true;
				foreach ($value['droits'] as $d)
				{
					if (!verifier($d))
					{
						$current = false;
						break;
					}
				}
				
				if ($current)
				{
					$count += $this->get($key);
				}
			}
		}
		
		return $count;
	}

	/**
	 * Incrémente un compteur admin.
	 *
	 * @param string $name Le nom du compteur
	 */
	public function increment($name)
	{
		$admin = $this->get($name);
		$this->write($name, ++$admin);
	}
	
	/**
	 * Affecte une valeur à un compteur.
	 *
	 * @param integer $name Le nom du cache
	 * @param integer $value La valeur à affecter
	 */
	public function write($name, $value)
	{
		if (array_key_exists($name, $this->taches))
		{
			$this->taches[$name]['compteur'] = (int) $value;
			$this->taches[$name]['refresh'] = true;

			$this->cache->set('taches_admin_'.$name, $value, $this->time);
		}
	}
}
