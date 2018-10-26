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

namespace Zco\Bundle\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zco\Bundle\ContentBundle\Domain\TagRepository;

/**
 * Actions related to tags.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class TagsController extends Controller
{
    /**
     * Modification d'un mot-clé existant.
     *
     * @param int $id Tag identifier.
     * @param string $slug Tag slug.
     * @return Response
     */
    public function viewAction($id, $slug)
    {
        /** @var TagRepository $repository */
        $repository = $this->get('zco.repository.tags');

        $tag = $repository->get($id);
        if (!$tag) {
            throw new NotFoundHttpException();
        }

        \Page::$titre = htmlspecialchars($tag['nom']);
        // TODO: check slug.

        return render_to_response('ZcoContentBundle::tag.html.php', [
            'tag' => $tag,
            'objects' => $repository->findRelatedObjects($id),
        ]);
    }
}
