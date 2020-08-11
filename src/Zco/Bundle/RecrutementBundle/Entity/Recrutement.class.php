<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2020 Corrigraphie
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
 * Recrutement
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     vincent1870 <vincent@zcorrecteurs.fr>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
class Recrutement extends BaseRecrutement
{
	const OUVERT = 1;
	const CACHE = 2;
	const FINI = 4;
	
	private static $etats = array(
		self::CACHE => 'en préparation (masqué)',
		self::OUVERT => 'en cours',
		self::FINI => 'terminé', 
	);
	
	public static function getEtats()
	{
		return self::$etats;
	}
	
	public function getEtatAffichage()
	{
		return self::$etats[$this['etat']];
	}
	
	public function incrementerNbLus()
	{
		$this['nb_lus'] = $this['nb_lus'] + 1;
		$this->save();
	}
	
	public function depotPossible()
	{
		return empty($this['date_fin_depot']) || $this['etat'] == \Recrutement::OUVERT && time() <= strtotime($this['date_fin_depot']);
	}
}