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

namespace Zco\Bundle\ParserBundle\Parser;

/**
 * Parse un document écrit en zCode respectant la syntaxe utilisée sur le SdZ 
 * en réutilisant leur propre librairie. Pour ceux ne disposant pas de la 
 * librairie le texte est affiché dans une forme dégradée.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class SdzParser implements ParserInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function parse($text, array $options = array())
	{
		if (!is_file($vendorFile = BASEPATH.'/vendor/zcode_sdz/parse.php'))
		{
			return nl2br(htmlspecialchars($text));
		}
		
		include_once $vendorFile;
		
		return parse($text);
	}
}
