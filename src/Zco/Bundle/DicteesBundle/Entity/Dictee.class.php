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

/**
 * Gestion des dictées
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class Dictee extends BaseDictee
{
	public function getTags()
	{
		$tags = Doctrine_Core::getTable('Dictee')->getTags($this);
		$o = array();
		foreach ($tags as $tag)
		{
			$o[] = htmlspecialchars($tag->Tag->nom);
		}
		return implode(', ', $o);
	}

	public function soundFilename($kind)
    {
        switch ($kind) {
            case 'lecture_lente':
                return $this->slowPaceSoundFilename();
            case 'lecture_rapide':
                return $this->fastPaceSoundFilename();
            default:
                throw new \InvalidArgumentException();
        }
    }

	public function slowPaceSoundFilename()
    {
        return sha1('sdfgurIR}J?F4' . $this->id . '$lecture_lente') . '.' . $this->format;
    }

    public function fastPaceSoundFilename()
    {
        return sha1('sdfgurIR}J?F4' . $this->id . '$lecture_rapide') . '.' . $this->format;
    }
}
