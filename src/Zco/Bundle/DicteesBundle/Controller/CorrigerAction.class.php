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

use Zco\Bundle\DicteesBundle\Controller\BaseController;

/**
 * Lecture d'une dictée.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class CorrigerAction extends BaseController
{
	public function execute()
	{
		$Dictee = $_GET['id'] ? Dictee($_GET['id']) : null;
		if(!$Dictee)
			return redirect(501, 'index.html', MSG_ERROR);

		$url = 'dictee-'.$Dictee->id.'-'.rewrite($Dictee->titre).'.html';
		if(empty($_POST['texte']))
			return new Symfony\Component\HttpFoundation\RedirectResponse($url);

        //On vérifie qu'il y ait un minimum de ressemblance entre les deux textes.
        //Pour cela on vérifie que le nombre de mots soumis soit au moins 60% du 
        //nombre de mots du texte original.
        $nbMotsOriginal = count(explode(' ', $Dictee->texte));
        $nbMotsSoumis   = count(explode(' ', $_POST['texte']));
        if ($nbMotsSoumis/$nbMotsOriginal < 0.6)
            return redirect(513, $url, MSG_ERROR);

		if($r = zCorrecteurs::verifierToken()) return $r;

		list($diff, $note) = CorrigerDictee($Dictee, $_POST['texte']);
		$fautes = $diff->fautes();

		Page::$titre = 'Correction de la dictée';
		fil_ariane(array(
			htmlspecialchars($Dictee->titre) => $url,
			'Correction'
		));

		$this->get('zco_vitesse.resource_manager')->requireResources(array(
		    '@ZcoCoreBundle/Resources/public/css/zcode.css',
		    '@ZcoDicteesBundle/Resources/public/css/dictees.css',
		));
		
		return render_to_response(compact('Dictee', 'note', 'diff'));
	}
}
?>
