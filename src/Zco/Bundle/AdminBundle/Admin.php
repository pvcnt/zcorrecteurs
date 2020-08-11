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

namespace Zco\Bundle\AdminBundle;

use Zco\Bundle\CoreBundle\Cache\CacheInterface;

/**
 * Classe facilitant l'enregistrement et le comptage des tâches d'administration.
 * Chaque tâche est accessible uniquement à des personnes possédant certains 
 * droits. Le comptage des tâches en lui-même se fait ailleurs.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class Admin
{
    private $taches = array();
    private $time = 3600;
    private $cache;

    /**
     * Constructeur.
     */
    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
        include_once(__DIR__ . '/modeles/taches_admin.php');
    }

    /**
     * Enregistre une certaine tâche.
     *
     * @param string $name Le nom de la tâche
     * @param array $credentials Les droits nécessaires pour la voir
     * @param array $options Des options...
     */
    public function register($name, $credentials, array $options = array())
    {
        $count               = isset($options['count']) ? $options['count'] : true;
        $this->taches[$name] = array(
            'credentials' => (array) $credentials,
            'value'       => null,
            'count'       => $count,
            'fresh'       => false,
        );
    }

    /**
     * Récupère le nombre de tâches en attente d'un certain type.
     * 
     * @param  string $name Le nom de la tâche.
     * @param  boolean $forceRefresh Doit-on forcer le rafraichissement ?
     * @return integer
     */
    public function get($name, $forceRefresh = false)
    {
        if (!isset($this->taches[$name])) {
            return 0;
        }

        //Si le cache a déjà été rafraichi, ça suffit !
        if ($this->taches[$name]['fresh'] && $forceRefresh) {
            $forceRefresh = false;
        }

        //Si la donnée est déjà calculée, on la renvoie.
        if (!is_null($this->taches[$name]['value']) && !$forceRefresh) {
            return $this->taches[$name]['value'];
        }

        //Si on peut la récupérer du cache.
        if (($value = $this->cache->get('zco_admin:task_' . $name)) !== false && !$forceRefresh) {
            $value                        = (int) $value;
            $this->taches[$name]['value'] = $value;

            return $value;
        }

        //Sinon on doit mettre à jour le compteur.
        if (function_exists($func = 'CompterTaches' . ucfirst($name))) {
            $value = (int) call_user_func($func);
            $this->write($name, $value);

            return $value;
        }

        trigger_error('La fonction de comptage CompterTaches' . ucfirst($name) . ' n\'existe pas', E_USER_NOTICE);
        $this->write($name, 0);

        return 0;
    }

    /**
     * Déclenche le rafraichissement de toutes les tâches.
     */
    public function refresh()
    {
        foreach (array_keys($this->taches) as $key) {
            $this->get($key, true);
        }
    }

    /**
     * Retourne le nombre de tâches en attente pour le visiteur.
     *
     * @return integer
     */
    public function count()
    {
        $count = 0;
        foreach ($this->taches as $key => $value) {
            if ($value['count']) {
                $current = true;
                foreach ($value['credentials'] as $d) {
                    if (!verifier($d)) {
                        $current = false;
                        break;
                    }
                }

                if ($current) {
                    $count += $this->get($key);
                }
            }
        }

        return $count;
    }

    /**
     * Affecte une valeur à un compteur.
     *
     * @param integer $name Le nom du cache
     * @param integer $value La valeur à affecter
     */
    public function write($name, $value)
    {
        $this->taches[$name]['value'] = (int) $value;
        $this->taches[$name]['fresh'] = true;
        $this->cache->set('zco_admin:task_' . $name, $value, $this->time);
    }

}
