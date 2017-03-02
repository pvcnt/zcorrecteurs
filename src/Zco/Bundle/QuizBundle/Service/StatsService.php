<?php

namespace Zco\Bundle\QuizBundle\Service;

class StatsService
{
    /**
     * Construit un tableau formaté pour l'affichage des données dans un
     * tableau à partir de données brutes issues de la requête.
     *
     * @param array $rows Données brutes.
     * @param string $key Clé du futur tableau.
     * @param integer $min_key Clé numérique de départ du tableau.
     * @param integer $max_key Clé numérique de fin du tableau.
     * @return array            Données formatées.
     */
    public function construireTableauDonnees(array $rows, $key, $min_key = null, $max_key = null)
    {
        $ret = array(
            'lignes' => [],
            'totaux' => array(
                'validations_totales' => 0,
                'validations_membres' => 0,
                'validations_visiteurs' => 0,
                'note_moyenne' => 0,
            ));

        //Construction des lignes par défaut si demandé.
        if (isset($min_key) && isset($max_key)) {
            for ($i = $min_key; $i <= $max_key; $i++) {
                $ret['lignes'][$i] = array(
                    'validations_totales' => 0,
                    'validations_membres' => 0,
                    'validations_visiteurs' => 0,
                    'note_moyenne' => 0,
                );
            }
        }

        //Remplissage avec les données issues de la base de données.
        foreach ($rows as $i => $row) {
            if ($row['validations_totales'] > 0) {
                $ret['totaux']['note_moyenne'] = ($ret['totaux']['note_moyenne'] * $ret['totaux']['validations_totales'] + $row['note_moyenne'] * $row['validations_totales']) / ($ret['totaux']['validations_totales'] + $row['validations_totales']);
            }
            $ret['lignes'][$row[$key]] = $row;
            $ret['totaux']['validations_totales'] += $row['validations_totales'];
            $ret['totaux']['validations_membres'] += $row['validations_membres'];
            $ret['totaux']['validations_visiteurs'] += $row['validations_visiteurs'];
        }

        return $ret;
    }

    /**
     * Construit un tableau formaté pour le tracé d'un graphique d'utilisation
     * du quiz à partir de données brutes issues de la requête.
     *
     * @param array $rows Données brutes.
     * @param string $key Clé du futur tableau.
     * @param integer $min_key Clé numérique de départ du tableau.
     * @param integer $max_key Clé numérique de fin du tableau.
     * @return array            Données formatées.
     */
    public function construireTableauGraphique(array $rows, $key, $min_key = null, $max_key = null)
    {
        $ret = array(
            'validations_totales' => [],
            'validations_membres' => [],
            'validations_visiteurs' => [],
            'note_moyenne' => [],
        );

        //Construction des lignes par défaut si demandé.
        if (isset($min_key) && isset($max_key)) {
            for ($i = $min_key; $i <= $max_key; $i++) {
                $ret['validations_totales'][$i] = 0;
                $ret['validations_membres'][$i] = 0;
                $ret['validations_visiteurs'][$i] = 0;
                $ret['note_moyenne'][$i] = 0;
            }
        }

        //Remplissage avec les données issues de la base de données.
        foreach ($rows as $row) {
            $ret['validations_totales'][$row[$key]] = $row['validations_totales'];
            $ret['validations_membres'][$row[$key]] = $row['validations_membres'];
            $ret['validations_visiteurs'][$row[$key]] = $row['validations_visiteurs'];
            $ret['note_moyenne'][$row[$key]] = $row['note_moyenne'];
        }

        return $ret;
    }

    /**
     * Construit un tableau formaté pour le tracé d'un graphique d'utilisation
     * du quiz, dans le cas particulier des statistiques globales sans limite de
     *  temps à partir de données brutes issues de la requête.
     *
     * @param array $rows Données brutes.
     * @param string $key Clé du futur tableau.
     * @param string $debut Date de début, sous la forme annee-mois.
     * @return array            Données formatées.
     */
    public function construireTableauGraphiqueGlobal(array $rows, $key, $debut = null)
    {
        $ret = array(
            'validations_totales' => [],
            'validations_membres' => [],
            'validations_visiteurs' => [],
            'note_moyenne' => [],
        );

        //Construction des lignes par défaut si demandé.
        if (isset($debut)) {
            list($annee_debut, $mois_debut) = explode('-', $debut);
            $mois_debut--;

            $cetteAnnee = date('Y');
            for ($i = $annee_debut; $i <= $cetteAnnee; $i++) {
                $min = ($i == $annee_debut) ? $mois_debut : 0;
                $max = ($i == $cetteAnnee) ? date('m') - 1 : 11;

                for ($j = $min; $j <= $max; $j++) {
                    $ret['validations_totales'][$i . '-' . $j] = 0;
                    $ret['validations_membres'][$i . '-' . $j] = 0;
                    $ret['validations_visiteurs'][$i . '-' . $j] = 0;
                    $ret['note_moyenne'][$i . '-' . $j] = 0;
                }
            }
        }
        //Remplissage avec les données issues de la base de données.
        foreach ($rows as $row) {
            $ret['validations_totales'][$row[$key]] = $row['validations_totales'];
            $ret['validations_membres'][$row[$key]] = $row['validations_membres'];
            $ret['validations_visiteurs'][$row[$key]] = $row['validations_visiteurs'];
            $ret['note_moyenne'][$row[$key]] = $row['note_moyenne'];
        }

        return $ret;
    }
}