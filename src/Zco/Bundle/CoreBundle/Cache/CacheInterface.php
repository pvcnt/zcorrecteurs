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

namespace Zco\Bundle\CoreBundle\Cache;

/**
 * Interface devant être implémentée par tous les moteurs de cache.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
interface CacheInterface
{
	/**
	 * Retourne l'index des fichiers mis en cache. Le tableau doit comporter 
	 * en clés les noms des caches et en valeur un tableau avec "lifetime"
	 * (la durée de vie spécifiée) et "time" (la date à laquelle les données 
	 * ont été mises en cache).
	 *
	 * @return array
	 */
	function getIndex();
	
	/**
	 * Vérifie si une clé existe dans le cache et si elle est encore valide 
	 * (c'est-à-dire n'a pas encore expiré, la durée de vie ayant été spécifiée
	 * lors de la mise en cache de la valeur).
	 *
	 * @param  string $id L'identifiant du cache
	 * @return boolean
	 */
	function has($id);

	/**
	 * Renvoie la valeur d'une clé dans le cache. Si la clé n'existe pas ou si 
	 * elle n'est plus valide une valeur par défaut est retournée.
	 *
	 * @param  string $id L'identifiant du cache
	 * @param  mixed $default La valeur par défaut à renvoyer
	 * @return mixed La valeur de la clé ou la valeur par défaut
	 */
	function get($id, $default = false);

	/**
	 * Met en cache une valeur sous la clé donnée et pour une durée de vie 
	 * spécifiée. Du fait de la nature volatile du cache, les implémentations 
	 * n'ont *pas* à garantir que la valeur mise en cache restera bien la 
	 * durée spécifiée, c'est bien une durée *maximale.
	 *
	 * @param string $id L'identifiant du cache
	 * @param mixed $data
	 * @param integer|null $lifetime
	 */
	function set($id, $data, $lifetime = null);
	
	/**
	 * Met en cache une valeur sous la clé donnée et pour une durée de vie 
	 * spécifiée uniquement si aucune valeur n'est déjà présente pour cette 
	 * clé (qu'elle n'ait jamais été spécifiée ou qu'elle ait expiré). Il 
	 * s'agit du « contraire » de replace().
	 *
	 * @param  string $id L'identifiant du cache
	 * @param  mixed $data
	 * @param  integer|null $lifetime
	 * @return boolean
	 */
	function add($id, $data, $lifetime = null);
	
	/**
	 * Met en cache une valeur sous la clé donnée et pour une durée de vie 
	 * spécifiée uniquement si une valeur non expirée existe déjà dans le 
	 * cache. Il s'agit du « contraire » de add().
	 *
	 * @param  string $id L'identifiant du cache
	 * @param  mixed $data
	 * @param  integer|null $lifetime
	 * @return boolean
	 */
	function replace($id, $data, $lifetime = null);
	
	/**
	 * Incrémente une valeur mise en cache sous la clé donnée de l'incrément 
	 * spécifié. Il appartient au développeur de s'assurer que la valeur mise 
	 * en cache est bien numérique.
	 *
	 * @param  string $id L'identifiant du cache
	 * @param  integer $step Le pas d'incrémentation (1 par défaut)
	 * @return integer La nouvelle valeur du cache
	 */
	function increment($id, $step = 1);
	
	/**
	 * Décrémente une valeur mise en cache sous la clé donnée de l'incrément 
	 * spécifié. Il appartient au développeur de s'assurer que la valeur mise 
	 * en cache est bien numérique.
	 *
	 * @param  string $id L'identifiant du cache
	 * @param  integer $step Le pas de décrémentation (1 par défaut)
	 * @return integer La nouvelle valeur du cache
	 */
	function decrement($id, $step = 1);

	/**
	 * Retire une valeur mise en cache sous la clé donnée. Il est possible 
	 * de supprimer des données multiples en insérant une étoile (*) dans 
	 * l'identifiant du cache. Cette étoile correspondra alors à 0, 1 ou 
	 * plusieurs caractères quelconques.
	 *
	 * @param string $id L'identifiant du cache
	 */
	function delete($id);
	
	/**
	 * Vide le cache de toutes ses données.
	 */
	function flush();
}
