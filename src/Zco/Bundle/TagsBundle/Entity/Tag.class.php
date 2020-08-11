<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2020 Corrigraphie
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

class Tag extends BaseTag
{
	/**
	 * Renvoie la liste des ressources liées à un tag.
	 * @return array
	 */
	public function listerRessourcesLiees()
	{
		$dbh = Doctrine_Manager::connection()->getDbh();
		$ressources = array();

		//Blog
		$stmt = $dbh->prepare("SELECT DISTINCT blog_id AS res_id, " .
				"version_titre AS res_titre, blog_date_publication AS res_date, " .
				"'billet' AS objet, '/blog/billet-%d-%s.html' AS res_url, " .
				"blog_id_categorie " .
				"FROM zcov2_blog_tags " .
				"LEFT JOIN zcov2_blog ON id_blog = blog_id " .
				"LEFT JOIN zcov2_blog_versions ON blog_id_version_courante = version_id " .
				"WHERE id_tag = :id AND blog_etat = ".BLOG_VALIDE);
		$stmt->bindValue(':id', $this['id']);
		$stmt->execute();
		$temp = $stmt->fetchAll();
		foreach($temp as $cle =>$suj)
		{
			if(!verifier('blog_voir', $suj['blog_id_categorie']))
				unset($temp[$cle]);
		}
		$ressources = array_merge($ressources, $temp);
		$stmt->closeCursor();

		//Dictées
		if (verifier('dictees_voir'))
		{
			$temp = Doctrine_Core::getTable('DicteeTag')->getDictees($this->id);
			foreach ($temp as $t)
			{
				$ressources[] = array(
					'objet'     => 'dictee',
					'res_id'    => $t->id,
					'res_titre' => $t->titre,
					'res_date'  => $t->validation,
					'res_url'   => '/dictees/dictee-%d-%s.html',
					NULL
				);
			}
		}
		return $ressources;
	}
}
