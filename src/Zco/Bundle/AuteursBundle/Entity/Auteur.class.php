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
class Auteur extends BaseAuteur
{
    public function __toString()
    {
        return (empty($this->prenom) ? '' : $this->prenom.' ').$this->nom;
    }

    /**
	 * Renvoie la liste des ressources liées à un auteur.
	 * @return array
	 */
	public function listerRessourcesLiees()
	{
		$ressources = array();
        $temp = Doctrine_Core::getTable('Dictee')->findByAuteurId($this->id);
        foreach ($temp as $t)
        {
            $ressources[] = array(
                'objet'     => 'dictee',
                'res_id'    => $t->id,
                'res_titre' => $t->titre,
                'res_date'  => $t->validation,
                'res_url'   => '/dictees/dictee-%d-%s.html',
                null
            );
        }
		return $ressources;
	}
}
