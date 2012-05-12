<?php

/**
 * Copyright 2012 Corrigraphie
 * 
 * This file is part of zCorrecteurs.fr.
 *
 * zCorrecteurs.fr is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * zCorrecteurs.fr is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with zCorrecteurs.fr. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Zco\Bundle\CoreBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * Cron quotidien.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class CronCommand extends ContainerAwareCommand
{
	/**
	 * {@inheritdoc}
	 */
	protected function configure()
	{
		$this
			->setName('cron:quotidien')
			->setDescription('Launched every day');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$dbh   = \Doctrine_Manager::connection()->getDbh();
		$cache = $this->getContainer()->get('zco_core.cache');

		//---Suppression des sauvegardes vieilles de plus d'un jour
		$stmt = $dbh->prepare("DELETE FROM zcov2_sauvegardes_zform
		WHERE sauvegarde_date <= NOW() - INTERVAL 1 DAY");
		$stmt->execute();
		$stmt->closeCursor();

		//---Suppression des comptes non-validés de plus d'un jour
		$stmt = $dbh->prepare("DELETE FROM zcov2_utilisateurs
		WHERE utilisateur_date_inscription <= NOW() - INTERVAL 1 DAY AND utilisateur_valide = 0");
		$stmt->execute();
		$stmt->closeCursor();

		//---Mise à jour des sanctions
		$stmt = $dbh->prepare("UPDATE zcov2_sanctions
		SET sanction_duree_restante = sanction_duree_restante - 1
		WHERE sanction_finie = 0 AND sanction_duree_restante > 0 AND (DATE(sanction_date) + INTERVAL (sanction_duree - sanction_duree_restante) DAY < DATE(NOW()))");
		$stmt->execute();
		$stmt->closeCursor();

		//Arrêt des sanctions trop vieilles
		$stmt = $dbh->prepare("SELECT sanction_id, sanction_id_utilisateur, sanction_id_groupe_origine
		FROM zcov2_sanctions
		WHERE sanction_duree > 0 AND sanction_duree_restante = 0");
		$stmt->execute();
		$sanctions = $stmt->fetchAll();
		$stmt->closeCursor();
		foreach($sanctions as $s)
		{
			// On remet le membre dans son groupe
			$stmt = $dbh->prepare("UPDATE zcov2_utilisateurs
			SET utilisateur_id_groupe = :groupe
			WHERE utilisateur_id = :id");
			$stmt->bindParam(':groupe', $s['sanction_id_groupe_origine']);
			$stmt->bindParam(':id', $s['sanction_id_utilisateur']);
			$stmt->execute();
			$stmt->closeCursor();

			// On la marque comme finie
			$stmt = $dbh->prepare("UPDATE zcov2_sanctions
			SET sanction_finie = 1, sanction_duree_restante = 0
			WHERE sanction_id = :id");
			$stmt->bindParam(':id', $s['sanction_id']);
			$stmt->execute();
			$stmt->closeCursor();
		}

		//---Mise à jour des bans IP
		$stmt = $dbh->prepare("UPDATE zcov2_ips_bannies
		SET ip_duree_restante = ip_duree_restante - 1
		WHERE ip_fini = 0 AND ip_duree_restante > 0 AND (ip_date + INTERVAL (ip_duree - ip_duree_restante) DAY < NOW())");
		$stmt->execute();
		$stmt->closeCursor();

		$stmt = $dbh->prepare("UPDATE zcov2_ips_bannies
		SET ip_fini = 1
		WHERE ip_duree > 0 AND ip_duree_restante = 0");
		$stmt->execute();
		$stmt->closeCursor();

		$cache->delete('ips_bannies');

		//---Désactivation des absences dont la date de fin est passée
		$stmt = $dbh->prepare("
		UPDATE zcov2_utilisateurs SET utilisateur_absent = 0, utilisateur_motif_absence = '', utilisateur_fin_absence = null, utilisateur_debut_absence = null
		WHERE utilisateur_absent = 1 AND utilisateur_fin_absence IS NOT NULL AND utilisateur_fin_absence < NOW()
		");
		$stmt->execute();

		//---Activation des absences dont la date de début est passé
		$stmt = $dbh->prepare("
		UPDATE zcov2_utilisateurs SET utilisateur_absent = 1
		WHERE utilisateur_debut_absence IS NOT NULL AND utilisateur_debut_absence < NOW() AND utilisateur_absent = 0
		");
		$stmt->execute();

		/*---------------- Informations mises en cache pour les modules de l'accueil -------------------------*/
		//Mise en cache des statistiques de zCorrection
		$cache->delete('statistiques_zcorrection');
		include(BASEPATH.'/src/Zco/Bundle/StatistiquesBundle/modeles/statistiques.php');
		RecupStatistiques();

		//Mise en cache des quiz les plus fréquentés
		$cache->delete('quiz_liste_frequentes');

		/* Suppression des blocages de comptes suite à trop de tentatives ratées */
		\Doctrine_Query::create()
			->delete('Tentative')
			->addWhere('blocage = 0')
			->execute();

		/* Mentions Twitter */
		\Doctrine_Core::getTable('TwitterMention')->retrieveByAccount();
		
		/* Stats Alexa */
		include(BASEPATH.'/src/Zco/Bundle/StatistiquesBundle/modeles/alexa.php');
		SaveAlexaRanks();

		//--- Suppression de l'historique des adresses IP de plus d'un an.
		//Ne surtout pas supprimer (déclaration CNIL, toussa).
		$stmt = $dbh->prepare("DELETE FROM zcov2_utilisateurs_ips
		WHERE ip_date_last <= NOW() - INTERVAL 1 YEAR");
		$stmt->execute();
		$stmt->closeCursor();
	}
}
