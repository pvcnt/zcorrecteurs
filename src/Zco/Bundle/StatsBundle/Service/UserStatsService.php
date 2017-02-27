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

namespace Zco\Bundle\StatsBundle\Service;

class UserStatsService
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

    public function getLocationStats($threshold = 0)
    {
        $nbUsers = $this->conn->fetchColumn("SELECT COUNT(*) " .
            "FROM zcov2_utilisateurs " .
            "WHERE utilisateur_localisation NOT IN('-', '0', 'Inconnu', '')");
        $data = $this->conn->fetchAll("SELECT COUNT(*) AS nb, utilisateur_localisation " .
            "FROM zcov2_utilisateurs " .
            "WHERE utilisateur_localisation NOT IN('-', '0', 'Inconnu', '') " .
            "GROUP BY utilisateur_localisation " .
            "ORDER BY COUNT(*) DESC");

        $table = [];
        foreach ($data as $row) {
            $pourcent = round(100 * $row['nb'] / $nbUsers, 1);
            if ($pourcent >= $threshold) {
                $table[$row['utilisateur_localisation']] = $pourcent;
            } else {
                if (!isset($table['Autres'])) {
                    $table['Autres'] = $pourcent;
                } else {
                    $table['Autres'] += $pourcent;
                }
            }
        }

        return array($table, $nbUsers);
    }

    public function getAgeStats($groupe = null)
    {
        $stmt = $this->conn->prepare('SELECT COUNT(utilisateur_id) AS nombre, '
            . 'CASE utilisateur_date_naissance '
            . 'WHEN NULL THEN 0 '
            . 'ELSE DATEDIFF(NOW(), utilisateur_date_naissance) DIV 365 '
            . 'END AS age '
            . 'FROM zcov2_utilisateurs '
            . 'WHERE utilisateur_date_naissance IS NOT NULL '
            . 'AND utilisateur_date_naissance <> \'0000-00-00\''
            . ($groupe !== null ? 'AND utilisateur_id_groupe = :g ' : '')
            . 'GROUP BY age ORDER BY age');
        if ($groupe !== null) {
            $stmt->bindParam('g', $groupe);
        }
        $stmt->execute();
        $r2 = $stmt->fetchAll();
        $stmt->closeCursor();

        if (!$r2) {
            return array();
        }

        // Réindexer avec l'âge comme clé
        $r = array();
        foreach ($r2 as $a) {
            $r[$a['age']] = $a['nombre'];
        }

        $ageMin = $r2[0]['age'];
        $ageMax = $r2[count($r2) - 1]['age'];

        $tailleTranche = 6;  // Taille d'une tranche
        $tranchesMax = 15; // Nombre de tranches tenant sur le graphique
        $ages = array();

        // Découper les âges en tranches
        $nombreTranches = $cle_old = 0;
        $plafondAges = $ageMax;
        for ($i = $ageMin; $i <= $ageMax; $i++) {
            $tranche = floor($i / $tailleTranche) * $tailleTranche;
            $cle = $tranche . ' - ' . ($tranche + $tailleTranche - 1);
            if ($cle_old !== $cle) {
                $cle_old = $cle;
                $nombreTranches++;
                if ($nombreTranches >= $tranchesMax && $ageMax > ($tranche + $tailleTranche - 1)) {
                    $plafondAges = $tranche - 1;
                    break;
                }
            }

            if (!isset($ages[$cle])) {
                $ages[$cle] = 0;
            }
            $ages[$cle] += isset($r[$i]) ? $r[$i] : 0;
        }

        // Ceux qui sont trop vieux pour être affichés
        if ($ageMax > $plafondAges) {
            $ages['> ' . $plafondAges] = 0;
            for ($i = $plafondAges + 1; $i <= $ageMax; $i++) {
                $ages['> ' . $plafondAges] += isset($r[$i]) ? $r[$i] : 0;
            }
        }

        return $ages;
    }

    /**
     * Récupère les statistiques d'inscription.
     * @author Ziame
     * @param string $classementFils Le type de période.
     * @param string $classementSql Son équivalent en SQL.
     * @param integer $annee L'année sur laquelle on fait les stats.
     * @param integer $mois Un mois précis sur lequel grouper les stats (facultatif).
     * @param integer $jour Un jour précis sur lequel grouper les stats (facultatif).
     * @return array
     */
    public function getRegistrationStats($classementFils = 'Mois', $classementSql = 'MONTH', $annee, $mois, $jour)
    {
        if ($classementFils === "Heure") {
            $condition = 'YEAR(utilisateur_date_inscription) = ' . $annee . ' AND MONTH(utilisateur_date_inscription) = ' . $mois . ' AND DAY(utilisateur_date_inscription) = ' . $jour;
            if ($classementSql === "WEEKDAY") {
                $depart = 0;
            } else {
                $depart = 1;
            }
        } else if ($classementFils === "Jour") {
            $condition = 'YEAR(utilisateur_date_inscription) = ' . $annee . ' AND MONTH(utilisateur_date_inscription) = ' . $mois;
            if ($classementSql === "WEEKDAY") {
                $depart = 0;
            } else {
                $depart = 1;
            }
        } else {
            $condition = 'YEAR(utilisateur_date_inscription) = ' . $annee;
            if ($classementSql === "WEEKDAY") {
                $depart = 0;
            } else {
                $depart = 1;
            }
        }

        //Calcul du nombre d'inscriptions
        $stmt = $this->conn->prepare('
	SELECT
	' . $classementSql . '(utilisateur_date_inscription) - ' . $depart . ' AS subdivision,
	COUNT(*) AS nombre_inscriptions,
	ROUND(COUNT(*)/(SELECT COUNT(*) FROM zcov2_utilisateurs WHERE ' . $condition . ')*100, 1) AS pourcentage_pour_division,
	ROUND(COUNT(*)/(SELECT COUNT(*) FROM zcov2_utilisateurs)*100, 1) AS pourcentage_pour_total
	FROM zcov2_utilisateurs WHERE ' . $condition . ' AND utilisateur_valide=1 GROUP BY ' . $classementSql . '(utilisateur_date_inscription)
	');
        $stmt->execute();
        $retourNonTraite = $stmt->fetchAll(\Doctrine_Core::FETCH_ASSOC);
        $stmt->closeCursor();

        $convertisseurMois = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
        $mois--;
        $jour--;
        $retour = [];

        //On comble les trous (si pour un mois, une journée... il n'y a pas d'inscrit, ça serait bien que la valeur soit quand même présente)
        if ($classementSql === "HOUR") {
            //On supprime la fin du jour si ça n'est pas encore passé
            if (($jour + 1) . ' ' . ($mois + 1) . ' ' . $annee === date('j n Y', time())) {
                $clauseRepetition = date('G', time()) - 1;
            } else {
                $clauseRepetition = 23;
            }

            for ($compteur = 0; $compteur <= $clauseRepetition; $compteur++) {
                $retour[$compteur]['subdivision'] = $compteur;
                if (!empty($retourNonTraite)) {
                    foreach ($retourNonTraite AS $elementNonTraite) {
                        if ($elementNonTraite['subdivision'] == $retour[$compteur]['subdivision']) {
                            $retour[$compteur]['nombre_inscriptions'] = $elementNonTraite['nombre_inscriptions'];
                            $retour[$compteur]['pourcentage_pour_division'] = $elementNonTraite['pourcentage_pour_division'];
                            $retour[$compteur]['pourcentage_pour_total'] = $elementNonTraite['pourcentage_pour_total'];
                        }
                    }
                    foreach ($retour AS $elementTraite) {
                        if (empty($retour[$compteur]['nombre_inscriptions']) || empty($retour[$compteur]['pourcentage_pour_division']) || empty($retour[$compteur]['pourcentage_pour_total'])) {
                            $retour[$compteur]['nombre_inscriptions'] = 0;
                            $retour[$compteur]['pourcentage_pour_division'] = 0;
                            $retour[$compteur]['pourcentage_pour_total'] = 0;
                        }
                    }
                } else {
                    $retour[$compteur]['nombre_inscriptions'] = 0;
                    $retour[$compteur]['pourcentage_pour_division'] = 0;
                    $retour[$compteur]['pourcentage_pour_total'] = 0;
                }
            }
        } else if ($classementSql === "DAY") {
            //On supprime la fin du mois si ça n'est pas encore passé
            if (($mois + 1) . ' ' . $annee === date("n Y", time())) {
                $clauseRepetition = date('d', time()) - 1;
            } else {
                $clauseRepetition = date('t', strtotime($convertisseurMois[$mois] . ' ' . $annee)) - 1;
            }

            for ($compteur = 0; $compteur <= $clauseRepetition; $compteur++) {
                $retour[$compteur]['subdivision'] = $compteur;
                if (!empty($retourNonTraite)) {
                    foreach ($retourNonTraite AS $elementNonTraite) {
                        if ($elementNonTraite['subdivision'] == $retour[$compteur]['subdivision']) {
                            $retour[$compteur]['nombre_inscriptions'] = $elementNonTraite['nombre_inscriptions'];
                            $retour[$compteur]['pourcentage_pour_division'] = $elementNonTraite['pourcentage_pour_division'];
                            $retour[$compteur]['pourcentage_pour_total'] = $elementNonTraite['pourcentage_pour_total'];
                        }
                    }
                    foreach ($retour AS $elementTraite) {
                        if (empty($retour[$compteur]['nombre_inscriptions']) || empty($retour[$compteur]['pourcentage_pour_division']) || empty($retour[$compteur]['pourcentage_pour_total'])) {
                            $retour[$compteur]['nombre_inscriptions'] = 0;
                            $retour[$compteur]['pourcentage_pour_division'] = 0;
                            $retour[$compteur]['pourcentage_pour_total'] = 0;
                        }
                    }
                } else {
                    $retour[$compteur]['nombre_inscriptions'] = 0;
                    $retour[$compteur]['pourcentage_pour_division'] = 0;
                    $retour[$compteur]['pourcentage_pour_total'] = 0;
                }
            }
        } else if ($classementSql === "WEEKDAY") {
            for ($compteur = 0; $compteur <= 6; $compteur++) {
                $retour[$compteur]['subdivision'] = $compteur;
                if (!empty($retourNonTraite)) {
                    foreach ($retourNonTraite AS $elementNonTraite) {
                        if ($elementNonTraite['subdivision'] == $retour[$compteur]['subdivision']) {
                            $retour[$compteur]['nombre_inscriptions'] = $elementNonTraite['nombre_inscriptions'];
                            $retour[$compteur]['pourcentage_pour_division'] = $elementNonTraite['pourcentage_pour_division'];
                            $retour[$compteur]['pourcentage_pour_total'] = $elementNonTraite['pourcentage_pour_total'];
                        }
                    }
                    foreach ($retour AS $elementTraite) {
                        if (empty($retour[$compteur]['nombre_inscriptions']) || empty($retour[$compteur]['pourcentage_pour_division']) || empty($retour[$compteur]['pourcentage_pour_total'])) {
                            $retour[$compteur]['nombre_inscriptions'] = 0;
                            $retour[$compteur]['pourcentage_pour_division'] = 0;
                            $retour[$compteur]['pourcentage_pour_total'] = 0;
                        }
                    }
                } else {
                    $retour[$compteur]['nombre_inscriptions'] = 0;
                    $retour[$compteur]['pourcentage_pour_division'] = 0;
                    $retour[$compteur]['pourcentage_pour_total'] = 0;
                }
            }
        } else {
            //On supprime la fin de l'année si ça n'est pas encore passé
            if ($annee === (int)date('Y', time())) {
                $clauseRepetition = date('n', time()) - 1;
            } else {
                $clauseRepetition = 11;
            }
            for ($compteur = 0; $compteur <= $clauseRepetition; $compteur++) {
                $retour[$compteur]['subdivision'] = $compteur;
                if (!empty($retourNonTraite)) {
                    foreach ($retourNonTraite AS $elementNonTraite) {
                        if ($elementNonTraite['subdivision'] == $retour[$compteur]['subdivision']) {
                            $retour[$compteur]['nombre_inscriptions'] = $elementNonTraite['nombre_inscriptions'];
                            $retour[$compteur]['pourcentage_pour_division'] = $elementNonTraite['pourcentage_pour_division'];
                            $retour[$compteur]['pourcentage_pour_total'] = $elementNonTraite['pourcentage_pour_total'];
                        }
                    }
                    foreach ($retour AS $elementTraite) {
                        if (empty($retour[$compteur]['nombre_inscriptions']) || empty($retour[$compteur]['pourcentage_pour_division']) || empty($retour[$compteur]['pourcentage_pour_total'])) {
                            $retour[$compteur]['nombre_inscriptions'] = 0;
                            $retour[$compteur]['pourcentage_pour_division'] = 0;
                            $retour[$compteur]['pourcentage_pour_total'] = 0;
                        }
                    }
                } else {
                    $retour[$compteur]['nombre_inscriptions'] = 0;
                    $retour[$compteur]['pourcentage_pour_division'] = 0;
                    $retour[$compteur]['pourcentage_pour_total'] = 0;
                }
            }
        }
        return $retour;
    }
}