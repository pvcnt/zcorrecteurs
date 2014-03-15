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

namespace Zco\Bundle\TwitterBundle\Service;

/**
 * Interactions avec l'API de Twitter.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class Twitter extends OAuth
{
    private $apiEndpoint;

    /**
     * Constructeur.
     *
     * @param string $apiEndpoint Point d'entrée dans l'API Twitter
     * @param string $oauthEndpoint Point d'entrée pour l'authentification
     * @param array $consumerKey Clés d'authentification
     */
    public function __construct($apiEndpoint, $oauthEndpoint, array $consumerKey)
    {
        $this->apiEndpoint = rtrim($apiEndpoint, '/');
        parent::__construct($oauthEndpoint, $consumerKey);
    }

    /**
     * Retourne la configuration de l'API.
     *
     * @return array
     */
    public function getConfiguration()
    {
        return $this->send('POST', $this->apiEndpoint . '/help/configuration.json');
    }

    /**
     * Ajoute un nouveau tweet.
     *
     * @param  string $status Texte de statut
     * @param  integer|null $rid ID du tweet auquel on répond
     * @return array
     */
    public function addTweet($status, $rid = null)
    {
        $params = compact('status');
        if ($rid !== null) {
            $params['in_reply_to_status_id'] = $rid;
        }

        return $this->send(
                'POST', $this->apiEndpoint . '/statuses/update.json', $params
        );
    }

    /**
     * Supprime un tweet.
     *
     * @param  integer $id ID du tweet à supprimer
     * @return array
     */
    public function deleteTweet($id)
    {
        return $this->send(
                'POST', $this->apiEndpoint . '/statuses/destroy/' . $id . '.json', array()
        );
    }

    /**
     * Retourne les mentions adressées à l'utilisateur.
     *
     * @param  integer $lastID ID de la dernière mention récupérée
     * @return array
     */
    public function getMentions($lastID = 0)
    {
        $lastID = $lastID ? array('since_id' => $lastID) : array();

        return $this->send(
                'GET', $this->apiEndpoint . '/statuses/mentions_timeline.json', $lastID
        );
    }

}