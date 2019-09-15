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

namespace Zco\Bundle\AdminBundle;

/**
 * A pending administrative task that is waiting some action to be resolved.
 * Those tasks are shown in the navbar and highlighted in the admin home.
 */
interface PendingTask
{
    /**
     * Count how many actions are pending. The result of this call will be
     * cached later on, implementations do not have to handle this here.
     *
     * @return int
     */
    public function count(): int;

    /**
     * Return a list of all the credentials the user must have to have be
     * interested in this task.
     *
     * @return string[]
     */
    public function getCredentials(): array;
}