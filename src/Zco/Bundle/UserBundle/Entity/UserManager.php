<?php

namespace Zco\Bundle\UserBundle\Entity;

class UserManager
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

    public function get($id)
    {
        return InfosUtilisateur($id);
    }
}