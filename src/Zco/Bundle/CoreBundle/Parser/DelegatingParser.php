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
 * Délègue le parsage à un ou plusieurs autres parseurs.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 * @author Savageman <savageman@zcorrecteurs.fr>
 */
class DelegatingParser implements ParserInterface
{
	private $instances;
	private $defaultLanguage;
	private $language;
	
	/**
	 * Constructeur.
	 *
	 * @param string $defaultLanguage Langage utilisé si aucun n'est spécifié
	 * @param array $instances Instances des parseurs à utiliser
	 */
	public function __construct($defaultLanguage, array $instances)
	{
	    $this->defaultLanguage = $defaultLanguage;
	    $this->language        = $defaultLanguage;
	    $this->instances       = $instances;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function parse($text, array $options = array())
	{
	    $retval = $this->getParser($this->language)->parse($text, $options);
	    $this->language = $this->defaultLanguage;
	    
	    return $retval;
	}
	
	/**
	 * Modifie le langage à utiliser pour le prochain appel à parse().
	 *
	 * @param string $language Langage à utiliser
     * @return $this
	 */
	public function with($language)
	{
	    $this->language = $language;
	    
	    return $this;
	}
	
	/**
	 * Renvoie le parseur correspondant au langage demandé.
	 *
	 * @param  string $language
	 * @return ParserInterface
	 */
	private function getParser($language)
	{
		$language = mb_strtolower($language);
		if (!empty($this->instances[$language]))
		{
			return $this->instances[$language];
		}

		throw new \InvalidArgumentException(sprintf(
			'No parser for language "%s" was found (searched among %s).',
			$language, implode(', ', array_keys($this->instances))
		));
	}
}
