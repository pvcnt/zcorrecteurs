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

namespace Zco\Bundle\SearchBundle\Search\Searchable;

/**
 * Recherche sur les tweets.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class TwitterSearchable implements SearchableInterface
{
    public function getIndex()
    {
        return 'twitter_tweets';
    }

    public function doesCheckCredentials()
    {
        return false;
    }

    public function transformResults(array $matches)
    {
        $ids = array_map(function ($m) {
            return $m['id'];
        }, $matches);

        return \Doctrine_Core::getTable('TwitterTweet')->getByIds($ids);
    }
}
