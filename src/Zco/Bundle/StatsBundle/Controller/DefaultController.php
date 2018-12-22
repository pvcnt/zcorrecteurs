<?php
/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2018 Corrigraphie
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

namespace Zco\Bundle\StatsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Zco\Bundle\StatsBundle\Service\AlexaChartService;
use Zco\Bundle\StatsBundle\Service\AlexaStatsService;
use Zco\Bundle\StatsBundle\Service\RegistrationChartService;
use Zco\Bundle\StatsBundle\Service\UserStatsService;

class DefaultController extends Controller
{
    public function alexaAction()
    {
        if (!verifier('voir_stats_generales')) {
            throw new AccessDeniedHttpException();
        }
        \Page::$titre = 'Classement Alexa';
        fil_ariane(['Administration' => $this->generateUrl('zco_admin_index'), 'Classement Alexa']);

        /** @var AlexaStatsService $statsService */
        $statsService = $this->get('zco_stats.alexa_stats');
        $year = $this->getYearOrCurrent();
        $month = $this->getMonth();

        return $this->render('ZcoStatsBundle::alexa.html.php', array(
            'Rangs' => $statsService->find($year, $month),
            'Mois' => $month,
            'Annee' => $year,
        ));
    }

    public function alexaChartAction()
    {
        if (!verifier('voir_stats_generales')) {
            throw new AccessDeniedHttpException();
        }
        /** @var AlexaChartService $chartService */
        $chartService = $this->get('zco_stats.alexa_chart');
        $year = $this->getYearOrCurrent();
        $month = $this->getMonth();

        return $this->createChartResponse($chartService->draw($year, $month));
    }

    public function registrationAction()
    {
        if (!verifier('voir_stats_generales')) {
            throw new AccessDeniedHttpException();
        }
        \Page::$titre = 'Statistiques d\'inscription du site';
        fil_ariane(['Administration' => $this->generateUrl('zco_admin_index'), 'Statistiques d\'inscription du site']);

        /** @var UserStatsService $statsService */
        $statsService = $this->get('zco_stats.user_stats');

        //Arrays de conversion
        $convertisseurMois = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
        $convertisseurJourNom = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');

        //Réglages des statistiques
        $type = (!empty($_GET['type']) && (intval($_GET['type'] - 10) >= 1 && intval($_GET['type'] - 10) <= 3)) ? (int)($_GET['type'] - 10) : 1;
        $annee = $this->getYearOrCurrent();
        $mois = $this->getMonth();
        $jour = $this->getDay();
        if ($mois === null && $jour === null) {
            $classementFils = 'Mois';
            $classementPere = 'Année';
            $classementSql = 'MONTH';
        } else if ($jour === null) {
            $classementFils = 'Jour';
            $classementPere = 'Mois';
            $classementSql = 'DAY';
        } else {
            $classementFils = 'Heure';
            $classementPere = 'Jour';
            $classementSql = 'HOUR';
        }
        if ($type === 2) {
            $classementSql = 'WEEKDAY';
        } elseif ($type === 3) {
            $classementSql = 'HOUR';
        }

        //On récupère les stats.
        $RecupStatistiquesInscription = $statsService->getRegistrationStats($classementFils, $classementSql, $annee, $mois, $jour);

        //On améliore en ajoutant quelques calculs statistiques.
        $somme = array('somme_inscriptions' => 0, 'somme_ppd' => 0, 'somme_ppt' => 0);
        $moyenne = array('moyenne_inscriptions' => 0, 'moyenne_ppd' => 0, 'moyenne_ppt' => 0);
        $minimum = array('minimum_inscriptions' => NULL, 'minimum_ppd' => NULL, 'minimum_ppt' => NULL);
        $maximum = array('maximum_inscriptions' => 0, 'maximum_ppd' => 0, 'maximum_ppt' => 0);
        $nombreEntrees = 0;
        foreach ($RecupStatistiquesInscription AS $elementStats) {
            $nombreEntrees++;

            $maximum['maximum_inscriptions'] = ($elementStats['nombre_inscriptions'] >= $maximum['maximum_inscriptions']) ? $elementStats['nombre_inscriptions'] : $maximum['maximum_inscriptions'];
            $maximum['maximum_ppd'] = ($elementStats['pourcentage_pour_division'] >= $maximum['maximum_ppd']) ? $elementStats['pourcentage_pour_division'] : $maximum['maximum_ppd'];
            $maximum['maximum_ppt'] = ($elementStats['pourcentage_pour_total'] >= $maximum['maximum_ppt']) ? $elementStats['pourcentage_pour_total'] : $maximum['maximum_ppt'];

            $minimum['minimum_inscriptions'] = ($elementStats['nombre_inscriptions'] <= $minimum['minimum_inscriptions'] || $minimum['minimum_inscriptions'] === NULL) ? $elementStats['nombre_inscriptions'] : $minimum['minimum_inscriptions'];
            $minimum['minimum_ppd'] = ($elementStats['pourcentage_pour_division'] <= $minimum['minimum_ppd'] || $minimum['minimum_ppd'] === NULL) ? $elementStats['pourcentage_pour_division'] : $minimum['minimum_ppd'];
            $minimum['minimum_ppt'] = ($elementStats['pourcentage_pour_total'] <= $minimum['minimum_ppt'] || $minimum['minimum_ppt'] === NULL) ? $elementStats['pourcentage_pour_total'] : $minimum['minimum_ppt'];

            $somme['somme_inscriptions'] += $elementStats['nombre_inscriptions'];
            $somme['somme_ppd'] += $elementStats['pourcentage_pour_division'];
            $somme['somme_ppt'] += $elementStats['pourcentage_pour_total'];
        }
        $moyenne['moyenne_inscriptions'] = round($somme['somme_inscriptions'] / $nombreEntrees, 1);
        $moyenne['moyenne_ppd'] = round($somme['somme_ppd'] / $nombreEntrees, 1);
        $moyenne['moyenne_ppt'] = round($somme['somme_ppt'] / $nombreEntrees, 1);

        return $this->render('ZcoStatsBundle::registration.html.php', get_defined_vars());
    }

    public function registrationChartAction()
    {
        if (!verifier('voir_stats_generales')) {
            throw new AccessDeniedHttpException();
        }
        $chartService = $this->get(RegistrationChartService::class);
        $type = (!empty($_GET['type']) && (intval($_GET['type'] - 10) >= 1 && intval($_GET['type'] - 10) <= 3)) ? (int)($_GET['type'] - 10) : 1;
        $annee = $this->getYearOrCurrent();
        $mois = $this->getMonth();
        $jour = $this->getDay();
        if ($mois === null && $jour === null) {
            $classementFils = 'Mois';
            $classementSql = 'MONTH';
        } else if ($jour === null) {
            $classementFils = 'Jour';
            $classementSql = 'DAY';
        } else {
            $classementFils = 'Heure';
            $classementSql = 'HOUR';
        }
        if ($type === 2) {
            $classementSql = 'WEEKDAY';
        } elseif ($type === 3) {
            $classementSql = 'HOUR';
        }

        return $this->createChartResponse($chartService->draw($classementFils, $classementSql, $annee, $mois, $jour));
    }

    private function createChartResponse($data)
    {
        $r = new Response($data);
        $r->headers->set('Content-type', 'image/png');

        return $r;
    }

    private function getYearOrCurrent()
    {
        $currentYear = (int)date('Y');
        if (isset($_GET['annee']) && $_GET['annee'] >= 2000 && $_GET['annee'] <= $currentYear) {
            return (int)$_GET['annee'];
        } else {
            return $currentYear;
        }
    }

    private function getMonth()
    {
        if (isset($_GET['mois']) && $_GET['mois'] >= 1 && $_GET['mois'] <= 12) {
            return (int)$_GET['mois'];
        } else {
            return null;
        }
    }

    private function getDay()
    {
        if (isset($_GET['jour']) && $_GET['jour'] >= 1 && $_GET['jour'] <= 31) {
            return intval($_GET['jour']);
        } else {
            return null;
        }
    }
}
