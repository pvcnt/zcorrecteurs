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

namespace Zco\Bundle\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zco\Bundle\ContentBundle\Domain\CategoryDAO;
use Zco\Bundle\ContentBundle\Form\CategoryType;

/**
 * Actions pour tout ce qui concerne la gestion des catégories du site.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
final class CategoryController extends Controller
{
    /**
     * Affichage de la liste des catégories.
     */
    public function indexAction(Request $request)
    {
        if (!verifier('cats_editer')) {
            throw new AccessDeniedHttpException();
        }

        // Si on veut descendre une catégorie
        if ($request->query->has('down')) {
            $InfosCategorie = CategoryDAO::InfosCategorie($request->query->get('down'));
            if (!$InfosCategorie) {
                throw new NotFoundHttpException();
            }
            if (!CategoryDAO::DescendreCategorie($InfosCategorie)) {
                return redirect(
                    'Impossible de descendre cette catégorie car elle est déjà en bas.',
                    $this->generateUrl('zco_categories_index'),
                    MSG_ERROR
                );
            }

            return redirect('La catégorie a bien été descendue.', $this->generateUrl('zco_categories_index'));
        }

        // Si on veut monter une catégorie
        if ($request->query->has('up')) {
            $InfosCategorie = CategoryDAO::InfosCategorie($request->query->get('up'));
            if (!$InfosCategorie) {
                throw new NotFoundHttpException();
            }
            if (!CategoryDAO::MonterCategorie($InfosCategorie)) {
                return redirect(
                    'Impossible de monter cette catégorie car elle est déjà en haut.',
                    $this->generateUrl('zco_categories_index'),
                    MSG_ERROR
                );
            }

            return redirect('La catégorie a bien été montée.', $this->generateUrl('zco_categories_index'));
        }

        \Page::$titre = 'Catégories';

        return $this->render('ZcoContentBundle:Category:index.html.php', [
            'categories' => CategoryDAO::ListerCategories(),
        ]);
    }

    /**
     * Ajoute une nouvelle catégorie.
     */
    public function newAction(Request $request)
    {
        if (!verifier('cats_editer')) {
            throw new AccessDeniedHttpException();
        }

        $form = $this->createCategoryForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            CategoryDAO::AjouterCategorie($form->getData());
            \Container::cache()->delete('categories');

            return redirect('La catégorie a bien été ajoutée.', $this->generateUrl('zco_categories_index'));
        }

        \Page::$titre = 'Créer une catégorie';
        fil_ariane([
            'Catégories' => $this->generateUrl('zco_categories_index'),
            'Créer une catégorie',
        ]);

        return $this->render('ZcoContentBundle:Category:new.html.php', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Modification d'une catégorie.
     */
    public function editAction($id, Request $request)
    {
        if (!verifier('cats_editer')) {
            throw new AccessDeniedHttpException();
        }

        $InfosCategorie = CategoryDAO::InfosCategorie($id);
        if (!$InfosCategorie) {
            throw new NotFoundHttpException();
        }
        $ListerParents = CategoryDAO::ListerParents($InfosCategorie);
        $parent = !empty($ListerParents) ? $ListerParents[count($ListerParents)-1]['cat_id'] : 0;
        $form = $this->createCategoryForm([
            'nom' => $InfosCategorie['cat_nom'],
            'description' => $InfosCategorie['cat_description'],
            'url' => $InfosCategorie['cat_url'],
            'url_redir' => $InfosCategorie['cat_redirection'],
            'parent' => $parent,
            'archive' => (bool) $InfosCategorie['cat_archive'],
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            CategoryDAO::EditerCategorie($id, $form->getData());
            \Container::cache()->delete('categories');

            return redirect('La catégorie a bien été modifiée.', $this->generateUrl('zco_categories_index'));
        }

        \Page::$titre = htmlspecialchars($InfosCategorie['cat_nom']) . '- Modifier la catégorie';
        fil_ariane([
            'Catégories' => $this->generateUrl('zco_categories_index'),
            htmlspecialchars($InfosCategorie['cat_nom']),
        ]);

        return $this->render('ZcoContentBundle:Category:edit.html.php', [
            'InfosCategorie' => $InfosCategorie,
            'form' => $form->createView(),
        ]);

    }

    /**
     * Suppression d'une catégorie.
     */
    public function deleteAction($id, Request $request)
    {
        if (!verifier('cats_editer')) {
            throw new AccessDeniedHttpException();
        }
        $InfosCategorie = CategoryDAO::InfosCategorie($id);
        if (!$InfosCategorie) {
            throw new NotFoundHttpException();
        }
        if ($InfosCategorie['cat_droite'] - $InfosCategorie['cat_gauche'] > 1) {
            return redirect(
                'Vous ne pouvez pas supprimer cette catégorie car elle a des sous-catégories.',
                $this->generateUrl('zco_categories_index'),
                MSG_ERROR
            );
        }

        if ($request->isMethod('POST')) {
            CategoryDAO::SupprimerCategorie($id);
            \Container::cache()->delete('categories');

            return redirect('La catégorie a bien été supprimée.', $this->generateUrl('zco_categories_index'));
        }

        \Page::$titre = htmlspecialchars($InfosCategorie['cat_nom']) . '- Supprimer la catégorie';
        fil_ariane([
            'Catégories' => $this->generateUrl('zco_categories_index'),
            htmlspecialchars($InfosCategorie['cat_nom']) => $this->generateUrl('zco_categories_edit', ['id' => $id]),
            'Supprimer',
        ]);

        return $this->render('ZcoContentBundle:Category:delete.html.php', [
            'InfosCategorie' => $InfosCategorie,
        ]);
    }

    private function createCategoryForm(array $data = [])
    {
        // TODO: handle this more nicely.
        $parentChoices = [];
        foreach (CategoryDAO::ListerCategories() as $cat) {
            $indent = str_repeat('...', $cat['cat_niveau']);
            $parentChoices[$indent . ' ' . $cat['cat_nom']] = $cat['cat_id'];
        }
        return $this->createForm(CategoryType::class, $data, ['parent_choices' => $parentChoices]);
    }
}
