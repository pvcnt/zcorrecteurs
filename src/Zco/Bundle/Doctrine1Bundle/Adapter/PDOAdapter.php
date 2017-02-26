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

namespace Zco\Bundle\Doctrine1Bundle\Adapter;

/**
 * Extension de PDO pour pouvoir enregistrer les requêtes effectuées
 * et le temps d'exécution.
 *
 * @author Savageman <savageman@zcorrecteurs.fr>
 *         vincent1870 <vincent@zcorrecteurs.fr>
 */
class PDOAdapter extends \PDO
{
    private $queries = array();
    private $debug = false;
    private $connexionTime;
    private $dsn;
    private $username;
    private $password;

    public $temp;

    /**
     * {@inheritdoc}
     */
    public function __construct($dsn, $username, $password, $options = array())
    {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;

        $now = microtime(true);
        parent::__construct($dsn, $username, $password, $options);
        $this->connexionTime = microtime(true) - $now;

        $this->setAttribute(\PDO::ATTR_STATEMENT_CLASS, array('\Zco\Bundle\Doctrine1Bundle\Adapter\PDOStatement', array($this)));
        $this->setAttribute(\PDO::ATTR_PERSISTENT, false);

        // http://wezfurlong.org/blog/2006/apr/using-pdo-mysql/
        $this->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);

        // Compabibility with MySQL >= 5.0.2
        $this->exec('SET sql_mode = \'\'');
    }

    /**
     * Active ou désactive le mode de débogage. L'enregistrement des requêtes
     * effectuées ne fonctionnera que si ce mode est activé.
     *
     * @param boolean $debug
     */
    public function setDebug($debug)
    {
        $this->debug = (boolean)$debug;
    }

    /**
     * Vérifie si le mode de débogage est activé.
     *
     * @return boolean
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * {@inheritdoc}
     */
    public function exec($statement)
    {
        $now = microtime(true);
        $stmt = parent::exec($statement);
        $this->addQuery($statement, microtime(true) - $now);

        return $stmt;
    }

    /**
     * {@inheritdoc}
     */
    public function query($statement, $mode = \PDO::ATTR_DEFAULT_FETCH_MODE, $arg3 = null, array $ctorargs = array())
    {
        $now = microtime(true);
        $stmt = call_user_func_array(array($this, 'parent::query'), func_get_args());
        if (false !== $stmt) {
            $this->addQuery($statement, microtime(true) - $now);

            return $stmt;
        }
        throw new \RuntimeException('Error while invoking PDO::query(), maybe wrong arguments.');
    }

    /**
     * {@inheritdoc}
     */
    public function prepare($statement, $driverOptions = array())
    {
        $this->temp = $statement;
        return parent::prepare($statement, $driverOptions);
    }

    /**
     * Retourne le temps de connexion à la base de données.
     *
     * @return float
     */
    public function getConnexionTime()
    {
        return $this->connexionTime;
    }

    /**
     * Retourne la liste des requêtes enregistrées.
     *
     * @return array
     */
    public function getQueries()
    {
        return $this->queries;
    }

    /**
     * Ajoute une requête aux logs.
     *
     * @param string $sql Requête SQL
     * @param float $time Temps d'exécution
     * @param array $params Paramètres de la requête
     */
    public function addQuery($sql, $time, array $params = array())
    {
        if (!$this->debug) {
            return;
        }

        $array = array('sql' => $sql, 'time' => $time, 'params' => $params);
        $dbt = debug_backtrace();
        $dbt = $dbt[1];
        if ($dbt['file'] && $dbt['line']) {
            $array['file'] = str_replace(BASEPATH, '~', $dbt['file']);
            $array['line'] = $dbt['line'];
        }

        $this->queries[] = $array;
    }
}
