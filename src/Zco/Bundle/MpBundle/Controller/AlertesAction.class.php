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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Zco\Bundle\ForumBundle\Domain\AlertDAO;

/**
 * Contrôleur gérant l'affichage de toutes les alertes.
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class AlertesAction extends Controller
{
    public function execute()
    {
        if (!verifier('mp_alertes')) {
            throw new AccessDeniedHttpException();
        }
        include(__DIR__ . '/../modeles/alertes.php');
        include(__DIR__ . '/../modeles/lire.php');

        //On compte le nombre d'alertes à afficher.
        if (isset($_GET['solved']) AND $_GET['solved']) {
            $statut = 1;
            $ajout_url = '?solved=1';
        } elseif (isset($_GET['solved']) AND !$_GET['solved']) {
            $statut = 0;
            $ajout_url = '?solved=0';
        } elseif (!isset($_GET['solved'])) {
            $statut = -1;
            $ajout_url = '';
        }
        $CompterAlertes = AlertDAO::CompterAlertes($statut);

        if (!empty($_GET['id']) AND is_numeric($_GET['id'])) {
            $page = TrouverLaPageDeCetteAlerte($_GET['id'], $CompterAlertes);
            return new RedirectResponse('alertes-p' . $page . '.html#a' . $_GET['id']);
        }

        //Si on veut marquer en résolu une alerte
        if (!empty($_GET['resolu']) && is_numeric($_GET['resolu'])) {
            AlertDAO::AlerteResolue($_GET['resolu'], $_SESSION['id']);

            return redirect('L\'alerte a bien été résolue.', 'alertes.html');
        }

        Page::$titre .= ' - Voir les alertes';

        //Système de pagination
        $nbAlertesParPage = 20;
        $NombreDePages = ceil($CompterAlertes / $nbAlertesParPage); //On en déduit le nombre de pages à créer
        $page = !empty($_GET['p']) && is_numeric($_GET['p']) ? $_GET['p'] : $NombreDePages;
        $debut = ($NombreDePages - $page) * $nbAlertesParPage; //On détermine la première alerte à lister
        $ListerAlertes = ListerAlertes($debut, $nbAlertesParPage); //On liste les alertes

        $ListePages = liste_pages($page, $NombreDePages, $CompterAlertes, $nbAlertesParPage, 'alertes-p%s.html' . $ajout_url);

        //Inclusion de la vue
        fil_ariane('Voir les alertes sur les messages privés');

        return $this->render('ZcoMpBundle::alertes.html.php', array(
            'CompterAlertes' => $CompterAlertes,
            'ListerAlertes' => $ListerAlertes,
            'ListePages' => $ListePages,
        ));
    }
}
