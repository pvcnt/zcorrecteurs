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

namespace Zco\Bundle\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zco\Bundle\ContentBundle\Domain\QuoteRepository;
use Zco\Bundle\ContentBundle\Form\QuoteType;

class QuotesController extends Controller
{
    /**
     * Display all quotes.
     *
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        if (!verifier('citations_modifier')) {
            throw new AccessDeniedHttpException();
        }
        /** @var QuoteRepository $repository */
        $repository = $this->get('zco.repository.quotes');
        $page = $request->get('page', 1);
        $quotes = $repository->findAll(30, ($page - 1) * 30);
        $totalCount = $repository->countAll();

        $pages = liste_pages($page, ceil($totalCount / 30), 0, 0, $this->generateUrl('zco_quote_index') . '?page=%s');

        \Page::$titre = 'Citations';
        $this->get('zco_core.resource_manager')->requireResources([
            '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css',
        ]);

        return $this->render('ZcoContentBundle:Quotes:index.html.php', [
            'quotes' => $quotes,
            'totalCount' => $totalCount,
            'pages' => $pages,
        ]);
    }

    public function newAction(Request $request)
    {
        if (!verifier('citations_modifier')) {
            throw new AccessDeniedHttpException();
        }

        $form = $this->createForm(QuoteType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var QuoteRepository $repository */
            $repository = $this->get('zco.repository.quotes');
            $data = $form->getData();
            $data['utilisateur_id'] = $_SESSION['id'];
            $repository->save($data);

            return redirect('La citation a bien été créée.', $this->generateUrl('zco_quote_index'));
        }

        \Page::$titre = 'Nouvelle citation';
        fil_ariane([
            'Citations' => $this->generateUrl('zco_quote_index'),
            'Nouvelle citation',
        ]);

        return $this->render('ZcoContentBundle:Quotes:new.html.php', [
            'form' => $form->createView(),
        ]);
    }

    public function editAction($id, Request $request)
    {
        if (!verifier('citations_modifier')) {
            throw new AccessDeniedHttpException();
        }

        /** @var QuoteRepository $repository */
        $repository = $this->get('zco.repository.quotes');
        $quote = $repository->get($id);
        if (!$quote) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(QuoteType::class);
        $form->setData($quote);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $repository->save($data);

            return redirect('La citation a bien été modifiée.', $this->generateUrl('zco_quote_index'));
        }

        \Page::$titre = 'Modifier une citation';
        fil_ariane([
            'Citations' => $this->generateUrl('zco_quote_index'),
            'Modifier une citation',
        ]);

        return $this->render('ZcoContentBundle:Quotes:edit.html.php', [
            'form' => $form->createView(),
        ]);
    }

    public function deleteAction($id, Request $request)
    {
        if (!verifier('citations_modifier')) {
            throw new AccessDeniedHttpException();
        }

        /** @var QuoteRepository $repository */
        $repository = $this->get('zco.repository.quotes');
        $quote = $repository->get($id);
        if (!$quote) {
            throw new NotFoundHttpException();
        }

        if ($request->getMethod() === 'POST') {
            $repository->delete($id);

            return redirect('La citation a bien été supprimée.', $this->generateUrl('zco_quote_index'));
        }

        \Page::$titre = 'Supprimer une citation';
        fil_ariane([
            'Citations' => $this->generateUrl('zco_quote_index'),
            'Modifier une citation',
        ]);

        return $this->render('ZcoContentBundle:Quotes:delete.html.php', [
            'quote' => $quote,
        ]);
    }
}
