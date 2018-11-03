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

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Zco\Bundle\RecrutementBundle\Form\Type\RecrutementType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Contrôleur gérant l'ajout d'un nouveau recrutement.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>, Vanger
 */
class AjouterAction extends Controller
{
	public function execute(Request $request)
	{
		if (!verifier('recrutements_editer')) {
		    throw new AccessDeniedHttpException();
        }
		\Page::$titre = 'Ajouter un recrutement';
		
		$recrutement = new Recrutement();
		$form = $this->createForm(RecrutementType::class, $recrutement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $recrutement->save();
            return redirect('Le recrutement a bien été ajouté.', 'recrutement-'.$recrutement['id'].'-'.rewrite($recrutement['nom']).'.html');
        }

		fil_ariane('Ajouter un recrutement');
		
		return render_to_response(array(
			'form' => $form->createView(),
			'quiz' => $this->get('zco_quiz.manager.quiz')->lister(true),
		));
	}
}
