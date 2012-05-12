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

/**
 * Contrôleur pour l'affichage des statistiques d'inscription.
 *
 * @author Ziame <ziame@zcorrecteurs.fr>
 */
class InscriptionAction
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl();
		Page::$titre = 'Statistiques d\'inscription du site';

		//Inclusion du modèle
		include(__DIR__.'/../modeles/statistiques.php');

		//Arrays de conversion
		$convertisseurMois = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
		$convertisseurJourNom = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');

		//Réglages des statistiques
		$type = (!empty($_GET['type']) && (intval($_GET['type'] - 10) >= 1 && intval($_GET['type'] - 10) <= 3)) ? (int) ($_GET['type'] - 10) : 1;
		$annee = (!empty($_GET['annee']) && (intval($_GET['annee']) >= 1950 && intval($_GET['annee']) <= date('Y', time()))) ? intval($_GET['annee']) : (int) date('Y');
		$mois = (!empty($_GET['mois']) && (intval($_GET['mois']) >= 1 && intval($_GET['mois']) <= 12)) ? intval($_GET['mois']-1) : 50;
		$jour = (!empty($_GET['jour']) && (intval($_GET['jour']) >= 1 && intval($_GET['jour']) <= 31)) ? intval($_GET['jour']-1) : 50;
		$moisDepartDeUn = $mois + 1;
		$jourDepartDeUn = $jour + 1;
		if ($mois == 50 && $jour == 50) {$classementFils = 'Mois'; $classementPere = 'Année';  $classementSql = 'MONTH';}
		else if ($jour == 50) {$classementFils = 'Jour'; $classementPere = 'Mois'; $classementSql = 'DAY';}
		else {$classementFils = 'Heure'; $classementPere = 'Jour'; $classementSql = 'HOUR';}

		if ($type === 2)
		{$classementSql = 'WEEKDAY';}
		elseif ($type === 3)
		{$classementSql = 'HOUR';}

		//On récupère les stats.
		$RecupStatistiquesInscription = RecupStatistiquesInscription($classementFils, $classementSql, $annee, $moisDepartDeUn, $jourDepartDeUn);

		//On améliore en ajoutant quelques calculs statistiques.
		$somme = array('somme_inscriptions' => 0, 'somme_ppd' => 0, 'somme_ppt' => 0);
		$moyenne = array('moyenne_inscriptions' => 0, 'moyenne_ppd' => 0, 'moyenne_ppt' => 0);
		$minimum = array('minimum_inscriptions' => NULL, 'minimum_ppd' => NULL, 'minimum_ppt' => NULL);
		$maximum = array('maximum_inscriptions' => 0, 'maximum_ppd' => 0, 'maximum_ppt' => 0);
		$nombreEntrees = 0;
		foreach($RecupStatistiquesInscription AS $elementStats)
		{
			$nombreEntrees++;

			//On fait les maxima
			$maximum['maximum_inscriptions'] = ($elementStats['nombre_inscriptions'] >= $maximum['maximum_inscriptions']) ? $elementStats['nombre_inscriptions'] : $maximum['maximum_inscriptions'];
			$maximum['maximum_ppd'] = ($elementStats['pourcentage_pour_division'] >= $maximum['maximum_ppd']) ? $elementStats['pourcentage_pour_division'] : $maximum['maximum_ppd'];
			$maximum['maximum_ppt'] = ($elementStats['pourcentage_pour_total'] >= $maximum['maximum_ppt']) ? $elementStats['pourcentage_pour_total'] : $maximum['maximum_ppt'];

			//On fait les minima
			$minimum['minimum_inscriptions'] = ($elementStats['nombre_inscriptions'] <= $minimum['minimum_inscriptions'] || $minimum['minimum_inscriptions'] === NULL) ? $elementStats['nombre_inscriptions'] : $minimum['minimum_inscriptions'];
			$minimum['minimum_ppd'] = ($elementStats['pourcentage_pour_division'] <= $minimum['minimum_ppd'] || $minimum['minimum_ppd'] === NULL) ? $elementStats['pourcentage_pour_division'] : $minimum['minimum_ppd'];
			$minimum['minimum_ppt'] = ($elementStats['pourcentage_pour_total'] <= $minimum['minimum_ppt'] || $minimum['minimum_ppt'] === NULL) ? $elementStats['pourcentage_pour_total'] : $minimum['minimum_ppt'];

			//On fait les sommes
			$somme['somme_inscriptions'] += $elementStats['nombre_inscriptions'];
			$somme['somme_ppd'] += $elementStats['pourcentage_pour_division'];
			$somme['somme_ppt'] += $elementStats['pourcentage_pour_total'];


		}
		//On fait les moyennes
		$moyenne['moyenne_inscriptions'] = round($somme['somme_inscriptions'] / $nombreEntrees, 1);
		$moyenne['moyenne_ppd'] = round($somme['somme_ppd'] / $nombreEntrees, 1);
		$moyenne['moyenne_ppt'] = round($somme['somme_ppt'] / $nombreEntrees, 1);


		//On les envoie par Session à Artichow (pour le graphe)
		$_SESSION['graphe_inscription'] = $RecupStatistiquesInscription;
		$_SESSION['graphe_classementSql'] = $classementSql;

		//Inclusion de la vue
		fil_ariane('Statistiques d\'inscription');
		
		return render_to_response(get_defined_vars());
	}
}
