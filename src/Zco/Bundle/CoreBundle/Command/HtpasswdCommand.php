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
namespace Zco\Bundle\CoreBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

/**
 * Cron s'exécutant tous les jours toutes les 4 heures. À 00H34, 04H34, 08H34,
 * etc. Il met à jour dynamiquement différents fichiers .htpasswd
 * Attention, exceptionnellement, ce cron est exécuté par www-data, pas par
 * zcoprod. Il est donc dans le crontab éponyme.
 *
 * IMPORTANT : Les fichiers .htpasswd dynamiques doivent avoir le droit
 * d'écriture pour le groupe propriétaire du fichier.
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class HtpasswdCommand extends Command
{
    /**
     * {@inheritdoc}
     */
	protected function configure()
	{
		$this
			->setName('cron:htpasswd')
			->setDescription('Recreates all .htpasswd files');
	}

    /**
     * {@inheritdoc}
     */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
	    $dbh = \Doctrine_Manager::connection()->getDbh();
	    
        //Spéciale dédicace à Tonton Savageman pour sa requête.
        //http://www.zcorrecteurs.fr/evolution/tache-95-htpasswds-dynamiques.html
        $stmt = $dbh->prepare("SELECT DISTINCT u.utilisateur_id, u.utilisateur_pseudo, u.utilisateur_mot_de_passe
        FROM zcov2_utilisateurs u
        JOIN zcov2_groupes g ON u.utilisateur_id_groupe = g.groupe_id
        JOIN zcov2_groupes_droits gd ON gd.gd_id_groupe = u.utilisateur_id_groupe
        AND gd.gd_valeur = 1
        JOIN zcov2_droits d ON gd.gd_id_droit = d.droit_id
        AND droit_nom = :droit
        GROUP BY u.utilisateur_id");

        /*
        * Droit htaccess_doc (accéder à la documentation).
        * Édite le fichier /home/web/zcorrecteurs.fr/.htpasswd_doc
        */
        $stmt->bindValue(':droit', 'htaccess_doc');
        $stmt->execute();
        $data_htaccess_doc = $stmt->fetchAll();

        /*
        * Droit htaccess_munin (accéder au monitoring d'Artémis).
        * Édite le fichier /var/www/munin/.htpasswd
        */
        $stmt->bindValue(':droit', 'htaccess_munin');
        $stmt->execute();
        $data_htaccess_munin = $stmt->fetchAll();

		//Ajout de DJ Fox en dur à la liste des personnes autorisées.
		$stmt2 = $dbh->prepare("SELECT DISTINCT u.utilisateur_id, u.utilisateur_pseudo, u.utilisateur_mot_de_passe
        FROM zcov2_utilisateurs u
		WHERE utilisateur_id = 14");
		$stmt2->execute();
		$djfox = $stmt->fetch();
		if ($djfox)
		{
			$data_htaccess_munin[] = $djfox;
		}

        /*
        * Droit htaccess_dev (accéder à la version de développement).
        * Édite le fichier /home/web/zcorrecteurs.fr/.htpasswd_dev
        */
        $stmt->bindValue(':droit', 'htaccess_dev');
        $stmt->execute();
        $data_htaccess_dev = $stmt->fetchAll();

        $stmt->closeCursor();

        //Écriture !
        $this->writeHtpasswd('/home/web/zcorrecteurs.fr/.htpasswd_doc', $data_htaccess_doc);
        $this->writeHtpasswd('/var/www/munin/.htpasswd', $data_htaccess_munin);
        $this->writeHtpasswd('/home/web/zcorrecteurs.fr/.htpasswd_dev', $data_htaccess_dev);
	}
	
	/**
	 * Convertit un mot de passe de notre base de données (encodé en sha1) 
	 * en un format compréhensible pour Apache.
	 *
	 * @param  string $sha1 Le mot de passe à réencoder
	 * @return string
	 */
	private function sha1ToSha1Htpasswd($sha1)
    {
    	return '{SHA}'.base64_encode(pack('H*', $sha1));
    }

    /**
     * Écrit les données extraites de la requête dans le fichier concerné.
     *
     * @param string $file Le fichier .htpasswd où écrire
     * @param array $fetchAll Le résultat de la requête
     */
    private function writeHtpasswd($file, array $fetchAll)
    {
    	$htpasswd = '';
    	foreach ($fetchAll as $valeur)
    	{
    		$htpasswd .= $valeur['utilisateur_pseudo'] . ':';
    		$htpasswd .= $this->sha1ToSha1Htpasswd($valeur['utilisateur_mot_de_passe']) . "\n";
    	}
    	file_put_contents($file, $htpasswd);
    }
}
