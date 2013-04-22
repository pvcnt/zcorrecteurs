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

namespace Zco\Bundle\AdBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine_Core;
use Doctrine_Query;

/**
 * Contrôleur gérant les opérations API.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class ApiController extends Controller
{
    /**
     * Modifie le nom d'une campagne de publicité.
     * 
     * @param Request $request
     * @param integer $id L'id de la publicité
     * @return Response
     */
    public function editCampaignNameAction(Request $request, $id)
    {
        var_dump('coucou');
        $campagne = Doctrine_Core::getTable('PubliciteCampagne')->find($id);
        if (!verifier('publicite_voir') && $campagne['utilisateur_id'] != $_SESSION['id']) {
            return new Response('ERREUR');
        }
        
        $campagne['nom'] = $request->request->get('nom');
        $campagne->save();

        return new Response(htmlspecialchars($campagne['nom']));
    }

    /**
     * Modifie l'état d'une campagne de publicité.
     * 
     * @param Request $request
     * @param integer $id L'id de la publicité
     * @return Response
     */
    public function editCampaignStatusAction(Request $request, $id)
    {
        $campagne = Doctrine_Core::getTable('PubliciteCampagne')->find($id);
        if (!verifier('publicite_editer_etat') && ($campagne['utilisateur_id'] != $_SESSION['id'] || !verifier('publicite_editer_etat_siens'))) {
            return new Response('ERREUR');
        }
        
        $campagne['etat'] = $request->request->get('etat');
        $campagne->save();
        $this->get('zco_core.cache')->delete('pub-*');

        return new Response($campagne->getEtatFormat());
    }

    /**
     * Modifie les dates d'une campagne de publicité.
     * 
     * @param Request $request
     * @param integer $id L'id de la publicité
     * @return Response
     */
    public function editCampaignDatesAction(Request $request, $id)
    {
        $campagne = Doctrine_Core::getTable('PubliciteCampagne')->find($id);
        if (!verifier('publicite_editer_etat') && ($campagne['utilisateur_id'] != $_SESSION['id'] || !verifier('publicite_editer_etat_siens'))) {
            return new Response('ERREUR');
        }
        
        $campagne['date_debut'] = $request->request->get('date_debut');
        $campagne['date_fin'] = $request->request->get('date_fin') ?: null;
        $campagne->save();
        $this->get('zco_core.cache')->delete('pub-*');

        return new Response(dateformat($campagne['date_debut']) . ' - ' . dateformat($campagne['date_fin']));
    }

    /**
     * Modifie l'état d'une publicité.
     * 
     * @param Request $request
     * @param integer $id L'id de la publicité
     * @return Response
     */
    public function editAdvertismentStatusAction(Request $request, $id)
    {
        $publicite = Doctrine_Core::getTable('Publicite')->find($id);
        if (!verifier('publicite_activer') && ($publicite->Campagne['utilisateur_id'] != $_SESSION['id'] || !verifier('publicite_activer_siens'))) {
            return new Response('ERREUR');
        }
        
        $publicite['actif'] = ($request->request->get('etat') === 'oui');
        $publicite->save();
        $this->get('zco_core.cache')->delete('pub-' . $publicite['emplacement']);
        return new Response($publicite->getEtatFormat());
    }

    /**
     * Enregistre un clic sur une publicité. Met à jour les compteurs de clics
     * et d'affichage de la publicité et de la campagne associée.
     *
     * @param Request $request
     * @param integer $id L'id de la publicité cliquée
     * @return Response
     */
    public function saveClickAction(Request $request, $id)
    {
        //Vérification de l'anti-flood sur les publicités.
        //Maximum : un clic par heure par publicité par session.
        if (!empty($_SESSION['pub_clic'][$id]) && $_SESSION['pub_clic'][$id] + 3600 > time()) {
            return new Response('OK');
        }

        $advertisment = Doctrine_Core::getTable('Publicite')->find($id);
        if ($advertisment === false) {
            return new Response('ERREUR');
        }
        
        $cache = $this->get('zco_core.cache');
        $campaign = $advertisment->Campagne;
        $nbv = $cache->get('pub_nbv-' . $advertisment['id'], 0);

        $clic = new \PubliciteClic();
        $clic['publicite_id'] = $advertisment['id'];
        $clic['categorie_id'] = $request->request->get('cat');
        $clic['pays'] = isset($_SESSION['pays']) ? $_SESSION['pays'] : 'Inconnu';
        $clic['age'] = isset($_SESSION['age']) ? $_SESSION['age'] : 0;
        $clic['ip'] = ip2long($request->getClientIp(true));
        $clic['date'] = date('Y-m-d');
        $clic->save();

        $result = Doctrine_Query::create()
                ->update('PubliciteStat')
                ->set('nb_clics', 'nb_clics + 1')
                ->set('nb_affichages', 'nb_affichages + ?', $nbv)
                ->where('publicite_id = ?', $advertisment['id'])
                ->andWhere('date = ?', date('Y-m-d'))
                ->execute();

        if (!$result) {
            $stat = new \PubliciteStat();
            $stat['publicite_id'] = $advertisment['id'];
            $stat['date'] = date('Y-m-d');
            $stat['nb_clics'] = 1;
            $stat['nb_affichages'] = $nbv;
            $stat->save();
        }

        $advertisment['nb_clics'] = $advertisment['nb_clics'] + 1;
        $advertisment['nb_affichages'] = $advertisment['nb_affichages'] + $nbv;
        $advertisment->save();

        $campaign['nb_clics'] = $campaign['nb_clics'] + 1;
        $campaign['nb_affichages'] = $campaign['nb_affichages'] + $nbv;
        $campaign->save();

        $cache->delete('pub_nbv-' . $advertisment['id']);
        $_SESSION['pub_clic'][$id] = time();

        return new Response('OK');
    }

}
