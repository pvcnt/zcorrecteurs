<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012 Corrigraphie
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
		\zCorrecteurs::VerifierFormatageUrl();
		\Page::$titre = 'Ajouter un recrutement';
		
		$recrutement = new Recrutement();
		$form = $this->get('form.factory')->create(new RecrutementType(), $recrutement);
		
		if ($request->getMethod() == 'POST')
		{
		    $form->bindRequest($request);
		    if ($form->isValid())
			{
				$recrutement->save();
				return redirect(1, 'recrutement-'.$recrutement['id'].'-'.rewrite($recrutement['nom']).'.html');
		    }
		}

		//Inclusion de la vue
		fil_ariane('Ajouter un recrutement');
		
		return render_to_response(array(
			'form' => $form->createView(),
			'quiz' => \Doctrine_Core::getTable('Quiz')->findAll(),
		));
	}
}
