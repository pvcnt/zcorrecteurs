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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zco\Bundle\DicteesBundle\Domain\Dictation;

/**
 * Lecture d'une dictée.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class CorrigerAction extends Controller
{
	public function execute()
	{
        include_once(__DIR__.'/../modeles/dictees.php');

		$Dictee = $_GET['id'] ? Dictee($_GET['id']) : null;
		if(!$Dictee)
			throw new NotFoundHttpException();

		$url = 'dictee-'.$Dictee->id.'-'.rewrite($Dictee->titre).'.html';
		if(empty($_POST['texte']))
			return new RedirectResponse($url);

        //On vérifie qu'il y ait un minimum de ressemblance entre les deux textes.
        //Pour cela on vérifie que le nombre de mots soumis soit au moins 60% du 
        //nombre de mots du texte original.
        $nbMotsOriginal = count(explode(' ', $Dictee->texte));
        $nbMotsSoumis   = count(explode(' ', $_POST['texte']));
        if ($nbMotsSoumis/$nbMotsOriginal < 0.6)
            return redirect(
                'Soit vous avez fait beaucoup trop de fautes, soit vous n\'avez pas terminé la dictée, soit vous vous êtes trompé de texte !',
                $url,
                MSG_ERROR
            );

		if($r = zCorrecteurs::verifierToken()) return $r;

		list($diff, $note) = CorrigerDictee($Dictee, $_POST['texte']);
		$fautes = $diff->fautes();

		Page::$titre = 'Correction de la dictée';
		fil_ariane(array(
			htmlspecialchars($Dictee->titre) => $url,
			'Correction'
		));

		$this->get('zco_core.resource_manager')->requireResources(array(
		    '@ZcoCoreBundle/Resources/public/css/zcode.css',
		    '@ZcoDicteesBundle/Resources/public/css/dictees.css',
		));

		return render_to_response('ZcoDicteesBundle::corriger.html.php', [
		    'Dictee' => $Dictee,
            'note' => $note,
            'diff' => $diff,
            'DicteeEtats' => Dictation::STATUSES,
            'DicteeDifficultes' => Dictation::LEVELS,
        ]);
	}
}