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

namespace Zco\Bundle\CoreBundle\Paginator\Adapter;

/**
 * Interface devant être implémentée par tous les adaptateurs du paginateur.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
interface AdapterInterface
{
	/**
	 * Détermine si l'adaptateur supporte l'objet ou le type de l'objet
	 * passé en paramètre.
	 *
	 * @param  mixed $objects Variable dont on souhaite tester le type
	 * @return boolean
	 */
    function supports($objects);
    
	/**
	 * Renvoie le nombre d'objets dans la collection passée en paramètre.
	 *
	 * @param  mixed $objects Variable d'un type supporté par l'adaptateur
	 * @return integer
	 */
    function count($objects);
    
	/**
	 * Découpe la collection.
	 *
	 * @param  mixed $objects Variable d'un type supporté par l'adaptateur
 	 * @param  integer $limit Nombre d'objets maximal à retourner
 	 * @param  integer $offset Indice de l'objet à partir duquel commencer
	 * @return \Iterable Une nouvelle collection itérable
	 */
    function slice($objects, $limit, $offset);
}