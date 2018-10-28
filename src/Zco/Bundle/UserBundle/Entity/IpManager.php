<?php

namespace Zco\Bundle\UserBundle\Entity;

use Doctrine\Common\Cache\Cache;

class IpManager
{
    private $conn;
    private $cache;

    /**
     * Constructor.
     *
     * @param \Doctrine_Connection $conn
     * @param Cache $cache
     */
    public function __construct(\Doctrine_Connection $conn, Cache $cache)
    {
        $this->conn = $conn;
        $this->cache = $cache;
    }

    public function BannirIP($ip, $raison, $raison_admin, $duree)
    {
        $ip = ip2long($ip);
        if ($ip && $ip != -1) {
            $stmt = $this->conn->prepare("INSERT INTO zcov2_ips_bannies(ip_ip, ip_raison, ip_raison_admin, ip_date, ip_duree, ip_duree_restante, ip_id_admin) " .
                "VALUES(:ip, :raison, :raison_admin, NOW(), :duree, :duree_restante, :id)");
            $stmt->bindParam(':ip', $ip);
            $stmt->bindParam(':raison', $raison);
            $stmt->bindParam(':raison_admin', $raison_admin);
            $stmt->bindParam(':duree', $duree);
            $stmt->bindParam(':duree_restante', $duree);
            $stmt->bindParam(':id', $_SESSION['id']);
            $stmt->execute();

            $contenu = $this->cache->fetch('ips_bannies');
            $contenu[] = $ip;
            $this->cache->save('ips_bannies', $contenu, 0);

            return true;
        } else {
            return false;
        }
    }

    public function DebannirIP($id)
    {
        $this->conn->exec('UPDATE zcov2_ips_bannies SET ip_fini = 1 WHERE ip_id = ?', [$id]);
        $this->cache->Delete('ips_bannies');
    }

    public function SupprimerIP($id)
    {
        $this->conn->exec('DELETE FROM zcov2_ips_bannies WHERE ip_id = ?', [$id]);
        $this->cache->Delete('ips_bannies');
    }

    public function ListerIPsBannies($fini = null, $ip = null)
    {
        if (!is_null($fini)) {
            $add = 'WHERE ip_fini = ' . ($fini ? 1 : 0) . ' ';
        } elseif (!is_null($ip)) {
            $add = 'WHERE ip_ip = ' . ip2long($ip) . ' ';
        } else {
            $add = '';
        }

        return $this->conn->fetchAll('
            SELECT ip_id, ip_ip, ip_duree, ip_duree_restante, ip_raison, ip_raison_admin, utilisateur_id, 
            utilisateur_pseudo, ip_fini, ip_date AS ip_date_debut, 
            CASE WHEN ip_duree = 0 
            THEN \'Jamais\' 
            ELSE (DATE(ip_date) + INTERVAL ip_duree DAY) 
            END AS ip_date_fin 
            FROM zcov2_ips_bannies 
            LEFT JOIN zcov2_utilisateurs ON ip_id_admin=utilisateur_id '
            . $add .
            'ORDER BY ip_fini, ip_date DESC');
    }

    public function isLocal($ip)
    {
        $match = explode('.', $ip);
        return $match[0] == '127'
            || $match[0] == '10'
            || ($match[0] == '172' && $match[1] >= '16' && $match[1] <= '31')
            || ($match[0] == '192' && $match[1] == '168');
    }

    public function Geolocaliser($ip)
    {
        if ($this->isLocal($ip)) {
            return [];
        }
        if (!is_file(BASEPATH . '/data/GeoLiteCity.dat')) {
            return null;
        }
        $gi = \geoip_open(BASEPATH . '/data/GeoLiteCity.dat', GEOIP_STANDARD);
        $location = \geoip_record_by_addr($gi, $ip);
        \geoip_close($gi);

        if (empty($location)) {
            return [];
        }

        return [
            'country' => $location->country_name ?? null,
            'city' => $location->city ?? null,
            'latitude' => $location->latitude,
            'longitude' => $location->longitude,
        ];
    }
}