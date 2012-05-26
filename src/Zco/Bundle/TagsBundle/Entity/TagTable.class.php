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

/**
 */
class TagTable extends Doctrine_Table
{
	/**
	 * Extrait les tags d'une chaine de caractères.
	 * @param string $texte			Le texte à analyser.
	 * @param boolean $analyse		Si à false, extrait les tags séparés par des
	 * 								virgules. Sinon analyse le texte à la recherche
	 * 								de tags déjà existants.
	 * @return array				La liste des id des tags trouvés.
	 */
	public function Extraire($texte, $analyse = false, $utilise = true)
	{
		$_tags = Doctrine_Core::getTable('Tag')->findAll();
		$tags = array();
		$retour = array();
		foreach($_tags as $tag)
			$tags[mb_strtolower($tag['nom'])] = $tag['id'];

		if($analyse == false)
		{
			if(empty($texte))
				return array();

			$extraction = explode(',', $texte);
			foreach($extraction as $mot)
			{
				$mot = trim($mot);
				if(array_key_exists(mb_strtolower($mot), $tags))
					$retour[] = $tags[mb_strtolower($mot)];
				elseif(!empty($mot))
				{
					$tag = new Tag;
					$tag['nom'] = $mot;
					$tag->save();
					$retour[] = $tag['id'];
				}
			}
		}
		else
		{
			foreach($tags as $tag => $id)
			{
				if(stripos($tag, $texte) !== false)
					$retour[] = $id;
			}
		}

		return $retour;
	}

	public function ajouter($nom)
	{
		$nom = trim($nom);
		if (!$nom)
			throw new InvalidArgumentException('Empty tag name');

		$existing = $this->createQuery('t')
			->select('t.id')
			->where('t.nom = ?', $nom)
			->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
		if ($existing)
			return $existing;

		$Tag = new Tag;
		$Tag->utilisateur_id = $_SESSION['id'];
		$Tag->couleur = '';
		$Tag->nom = $nom;
		$Tag->save();

		return $Tag->id;
	}
}
