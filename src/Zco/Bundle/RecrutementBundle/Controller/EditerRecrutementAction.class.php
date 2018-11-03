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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zco\Bundle\RecrutementBundle\Form\Type\RecrutementType;

/**
 * Contrôleur gérant la modification d'un recrutement.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>, Vanger
 */
class EditerRecrutementAction extends Controller
{
    public function execute(Request $request)
    {
        if (!verifier('recrutements_editer')) {
            throw new AccessDeniedHttpException();
        }
        if (empty($_GET['id']) || !is_numeric($_GET['id'])) {
            throw new NotFoundHttpException();
        }
        $recrutement = \Doctrine_Core::getTable('Recrutement')->recuperer($_GET['id']);
        if (!$recrutement) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(RecrutementType::class, $recrutement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recrutement->save();
            return redirect(
                'Le recrutement a bien été modifié.',
                'recrutement-' . $recrutement['id'] . '-' . rewrite($recrutement['nom']) . '.html'
            );
        }

        Page::$titre = htmlspecialchars($recrutement['nom']);

        fil_ariane(array(
            htmlspecialchars($recrutement['nom']) => 'recrutement-' . $recrutement['id'] . '.html',
            'Modifier le recrutement'
        ));

        return render_to_response(array(
            'form' => $form->createView(),
            'recrutement' => $recrutement,
            'quiz' => $this->get('zco_quiz.manager.quiz')->lister(true),
        ));
    }
}
