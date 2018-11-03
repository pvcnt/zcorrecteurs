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

namespace Zco\Bundle\CoreBundle\Parser;

/**
 * Interface devant être implémentée par tous les parseurs.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
interface ParserInterface
{
	/**
	 * Transforme un texte en code HTML.
	 *
	 * @param  string $data Le texte à parser
	 * @param  array $options Liste d'options
	 * @return string Code HTML
	 */
	function parse($data, array $options = array());
}
