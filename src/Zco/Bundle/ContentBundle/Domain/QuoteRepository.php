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

namespace Zco\Bundle\ContentBundle\Domain;

class QuoteRepository
{
    private $conn;

    public function __construct(\Doctrine_Connection $conn)
    {
        $this->conn = $conn;
    }

    public static function instance()
    {
        return new QuoteRepository(\Doctrine_Manager::connection());
    }

    /**
     * Retrieve all quotes.
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function findAll(int $limit, int $offset)
    {
        $stmt = $this->conn->prepare('SELECT * FROM zcov2_citations LIMIT ? OFFSET ?');
        $stmt->bindValue(1, $limit, \Doctrine_Core::PARAM_INT);
        $stmt->bindValue(2, $offset, \Doctrine_Core::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        $stmt->closeCursor();

        $results = [];
        foreach ($rows as $row) {
            $results[] = $this->hydrate($row);
        }

        return $results;
    }

    /**
     * Count the total number of quotes.
     *
     * @return int
     */
    public function countAll()
    {
        $stmt = $this->conn->prepare('SELECT COUNT(1) FROM zcov2_citations');
        $stmt->execute();
        $count = (int)$stmt->fetchColumn();
        $stmt->closeCursor();

        return $count;
    }

    /**
     * Retrieve a single quote by its identifier.
     *
     * @param int $id Quote identifier.
     * @return array|false
     */
    public function get($id)
    {
        $stmt = $this->conn->prepare('SELECT * FROM zcov2_citations WHERE id = ?');
        $stmt->bindValue(1, $id);
        $stmt->execute();
        $row = $stmt->fetch(\Doctrine_Core::FETCH_ASSOC);
        $stmt->closeCursor();

        if (!$row) {
            return false;
        }

        return $this->hydrate($row);
    }

    public function save(array &$data)
    {
        if (isset($data['id'])) {
            $stmt = $this->conn->prepare('UPDATE zcov2_citations 
                SET auteur_nom = ?, auteur_prenom = ?, auteur_autres = ?, contenu = ?, statut = ?
                WHERE id = ?');
            $stmt->bindValue(1, $data['auteur_nom']);
            $stmt->bindValue(2, $data['auteur_prenom']);
            $stmt->bindValue(3, $data['auteur_autres']);
            $stmt->bindValue(4, $data['contenu']);
            $stmt->bindValue(5, $data['statut']);
            $stmt->bindValue(6, $data['id']);
            $stmt->execute();
        } else {
            $stmt = $this->conn->prepare('INSERT INTO zcov2_citations(utilisateur_id, auteur_nom, auteur_prenom, auteur_autres, contenu, date, statut)
                VALUES(?, ?, ?, ?, ?, ?, ?)');
            $stmt->bindValue(1, $data['utilisateur_id']);
            $stmt->bindValue(2, $data['auteur_nom']);
            $stmt->bindValue(3, $data['auteur_prenom']);
            $stmt->bindValue(4, $data['auteur_autres']);
            $stmt->bindValue(5, $data['contenu']);
            $stmt->bindValue(6, $data['date'] ?? date('Y-m-d H:i:s'));
            $stmt->bindValue(7, $data['statut']);
            $stmt->execute();

            $data['id'] = $this->conn->lastInsertId('zcov2_citations');
        }
    }

    public function getRandom()
    {
        $stmt = $this->conn->prepare('SELECT * FROM zcov2_citations WHERE statut = 1 ORDER BY RAND() LIMIT 1');
        $stmt->execute();
        $row = $stmt->fetch(\Doctrine_Core::FETCH_ASSOC);
        $stmt->closeCursor();

        if (!$row) {
            return false;
        }

        return $this->hydrate($row);
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare('DELETE FROM zcov2_citations WHERE id = ?');
        $stmt->bindValue(1, $id);
        $stmt->execute();
        $stmt->closeCursor();
    }

    public function getDefinition()
    {
        return [
            'tableName' => 'zcov2_citations',
            'columns' => [
                'id' => ['type' => 'integer', 'length' => 11],
                'utilisateur_id' => ['type' => 'integer', 'length' => 11],
                'auteur_nom' => ['type' => 'string', 'length' => 100],
                'auteur_prenom' => ['type' => 'string', 'length' => 100],
                'auteur_autres' => ['type' => 'string', 'length' => 100],
                'contenu' => ['type' => 'text'],
                'date' => ['type' => 'timestamp'],
                'statut' => ['type' => 'boolean'],
            ],
        ];
    }

    private function hydrate($data)
    {
        $data['id'] = (int)$data['id'];
        $data['utilisateur_id'] = (int)$data['utilisateur_id'];
        $data['statut'] = (bool)$data['statut'];
        return $data;
    }
}