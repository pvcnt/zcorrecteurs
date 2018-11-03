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

namespace Zco\Bundle\ContentBundle\Domain;

class TagRepository
{
    private $conn;

    public function __construct(\Doctrine_Connection $conn)
    {
        $this->conn = $conn;
    }

    public static function instance()
    {
        return new TagRepository(\Doctrine_Manager::connection());
    }

    /**
     * Retrieve all tags, ordered by name.
     *
     * @return array
     */
    public function findAll()
    {
        $stmt = $this->conn->prepare('SELECT id, nom FROM zcov2_tags ORDER BY nom');
        $stmt->execute();
        $rows = $stmt->fetchAll();
        $stmt->closeCursor();

        return $rows;
    }

    /**
     * Retrieve a single tag by its identifier.
     *
     * @param int $id Tag identifier.
     * @return array|false
     */
    public function get($id)
    {
        $stmt = $this->conn->prepare('SELECT id, nom FROM zcov2_tags WHERE id = ?');
        $stmt->bindValue(1, $id);
        $stmt->execute();
        $row = $stmt->fetch(\Doctrine_Core::FETCH_ASSOC);
        $stmt->closeCursor();

        return $row;
    }

    /**
     * Extrait les tags d'une chaine de caractères.
     *
     * @param string $text Le texte à analyser.
     * @return array La liste des id des tags trouvés.
     */
    public function extract($text)
    {
        if (empty($text)) {
            return [];
        }

        $tags = [];
        foreach ($this->findAll() as $tag) {
            $tags[mb_strtolower($tag['nom'])] = $tag['id'];
        }

        $result = [];
        $extraction = explode(',', $text);
        foreach ($extraction as $mot) {
            $mot = trim($mot);
            if (array_key_exists(mb_strtolower($mot), $tags)) {
                $result[] = $tags[mb_strtolower($mot)];
            } elseif (!empty($mot)) {
                $result[] = $this->create(['nom' => $mot]);
            }
        }

        return $result;
    }

    public function create($row)
    {
        $stmt = $this->conn->prepare('INSERT INTO zcov2_tags(nom) VALUES(?)');
        $stmt->bindValue(1, $row['nom']);
        $stmt->execute();

        return $this->conn->lastInsertId('zcov2_tags');
    }

    /**
     * Retrieve all objects related to a tag.
     *
     * @param int $id Tag identifier.
     * @return array
     */
    public function findRelatedObjects($id)
    {
        $result = array();

        // Blog.
        $stmt = $this->conn->prepare("SELECT DISTINCT blog_id AS res_id, " .
            "version_titre AS res_titre, blog_date_publication AS res_date, " .
            "'billet' AS objet, '/blog/billet-%d-%s.html' AS res_url, " .
            "blog_id_categorie " .
            "FROM zcov2_blog_tags " .
            "LEFT JOIN zcov2_blog ON id_blog = blog_id " .
            "LEFT JOIN zcov2_blog_versions ON blog_id_version_courante = version_id " .
            "WHERE id_tag = :id AND blog_etat = " . BLOG_VALIDE);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $result = array_merge($result, $stmt->fetchAll());
        $stmt->closeCursor();

        // Dictations.
        $temp = \Doctrine_Core::getTable('DicteeTag')->getDictees($id);
        foreach ($temp as $t) {
            $result[] = array(
                'objet' => 'dictee',
                'res_id' => $t->id,
                'res_titre' => $t->titre,
                'res_date' => $t->validation,
                'res_url' => '/dictees/dictee-%d-%s.html',
                null,
            );
        }

        return $result;
    }

    public function getDefinition()
    {
        return [
            'tableName' => 'zcov2_tags',
            'columns' => [
                'id' => ['type' => 'integer', 'length' => 11],
                'nom' => ['type' => 'string', 'length' => 100],
            ],
        ];
    }
}