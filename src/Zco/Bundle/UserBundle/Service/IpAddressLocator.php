<?php

namespace Zco\Bundle\UserBundle\Service;

final class IpAddressLocator
{
    private $conn;

    /**
     * Constructor.
     *
     * @param \Doctrine_Connection $conn
     */
    public function __construct(\Doctrine_Connection $conn)
    {
        $this->conn = $conn;
    }

    public function isLocal($ip)
    {
        $match = explode('.', $ip);
        return $match[0] == '127'
            || $match[0] == '10'
            || ($match[0] == '172' && $match[1] >= '16' && $match[1] <= '31')
            || ($match[0] == '192' && $match[1] == '168');
    }

    public function locate($ip)
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