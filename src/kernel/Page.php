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

/**
 * Classe magique permettant de gérer des variables concernant la page.
 *
 * @author Savageman <savageman@zcorrecteurs.fr>
 */
class Page
{
	/**
	 * Le titre de la page.
	 * @static
	 * @access public
	 * @var string
	 */
	public static $titre = '';

	/**
	 * Action pour les robots.
	 * @static
	 * @access public
	 * @var string
	 */
	public static $robots = 'index,follow';

	/**
	 * La description de la page.
	 * @static
	 * @access public
	 * @var string
	 */
	public static $description = '';

	/**
	 * Le fil d'arianne.
	 * @static
	 * @access public
	 * @var array
	 */
	public static $fil_ariane = array();

	public static function breadcrumb()
    {
        // Génération d'un fil d'Ariane par défaut si aucun n'a été créé.
        if (empty(self::$fil_ariane) && !empty(self::$titre)) {
            fil_ariane(self::$titre);
        }

        return self::$fil_ariane;
    }
}
