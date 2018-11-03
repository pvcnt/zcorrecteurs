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

namespace Zco\Bundle\ContentBundle\Search\Searchable;

/**
 * Recherche sur le forum.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class ForumSearchable implements SearchableInterface
{
    public function getIndex()
    {
        return 'forum_messages';
    }

    public function transformResults(array $matches)
    {
        include_once(__DIR__ . '/../../../ForumBundle/modeles/messages.php');
        $ids = array_map(function ($m) {
            return $m['id'];
        }, $matches);

        return ListerMessagesId($ids);
    }

    public function doesCheckCredentials()
    {
        return true;
    }
}