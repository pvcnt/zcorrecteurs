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
	 * Les keywords de la page.
	 * @static
	 * @access public
	 * @var string
	 */
	public static $mots_cles = '';

	/**
	 * Le layout à utiliser
	 * @static
	 * @access public
	 * @var array
	 */
	public static $layout = 'default';

	/**
	 * La liste des templates à inclure.
	 * @static
	 * @access public
	 * @var array
	 */
	public static $templates = array();

	/**
	 * La liste des templates de config à évaluer.
	 * @static
	 * @access public
	 * @var array
	 */
	public static $configs = array();

	/**
	 * La liste des balises meta.
	 * @static
	 * @access public
	 * @var array
	 */
	public static $metas = array();

	/**
	 * Du contenu à ajouter avant inclusion des templates.
	 * @static
	 * @access public
	 * @var string
	 */
	public static $content = '';

	/**
	 * Le fil d'arianne.
	 * @static
	 * @access public
	 * @var array
	 */
	public static $fil_ariane = array();
}
