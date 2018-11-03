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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zco\Bundle\DicteesBundle\Domain\Dictation;

/**
 * Lecture d'une dictée.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class DicteeAction extends Controller
{
	public function execute()
	{
        include_once(__DIR__.'/../modeles/dictees.php');

		$Dictee = $_GET['id'] ? Dictee($_GET['id']) : null;
		if(!$Dictee)
			throw new NotFoundHttpException();

		zCorrecteurs::VerifierFormatageUrl($Dictee->titre, true);

		$Tags = DicteeTags($Dictee);

		Page::$titre = htmlspecialchars($Dictee->titre);
		fil_ariane(Page::$titre);
        $this->get('zco_core.resource_manager')->requireResources(array(
		    '@ZcoCoreBundle/Resources/public/css/zcode.css',
		    '@ZcoDicteesBundle/Resources/public/css/dictees.css',
		));

		return render_to_response('ZcoDicteesBundle::dictee.html.php', [
		    'Dictee' => $Dictee,
            'Tags' => $Tags,
            'DicteeDifficultes' => Dictation::LEVELS,
            'DicteeEtats' => Dictation::STATUSES,
        ]);
	}
}