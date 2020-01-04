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

use Zco\Bundle\DicteesBundle\Controller\BaseController;

/**
 * Passage d'une dictée en/hors ligne.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class ValiderAction extends BaseController
{
	public function execute()
	{
		if($r = zCorrecteurs::verifierToken()) return $r;
		$Dictee = $_GET['id'] ? Dictee($_GET['id']) : null;
		if(!$Dictee)
			redirect(501, 'index.html', MSG_ERROR);

		ValiderDictee($Dictee, $_GET['id2']);
		return redirect($_GET['id2'] ? 502 : 503,
			'dictee-'.$Dictee->id.'-'.rewrite($Dictee->titre).'.html');
	}
}
