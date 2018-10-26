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

namespace Zco\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Actions gérant les actions liées à l'analyse et au bannissement des
 * adresses IP.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class IpsController extends Controller
{
    /**
     * Affiche la liste des adresses IP bannies.
     *
     * @param Request $request HTTP request.
     * @return Response
     */
    public function indexAction(Request $request)
    {
        if (!verifier('ips_bannir')) {
            throw new AccessDeniedHttpException();
        }
        $manager = $this->get('zco_user.manager.ip');
        if ($request->query->has('cancel') && $request->query->get('tk') === $_SESSION['token'] && verifier('ips_bannir')) {
            //Si on veut débannir une IP
            $manager->debannirIP($request->query->get('cancel'));

            return redirect(
                'L\'adresse IP a bien été débannie.',
                $this->generateUrl('zco_user_ips_index')
            );
        }
        if ($request->query->has('delete') && $request->query->get('tk') === $_SESSION['token'] && verifier('ips_bannir')) {
            //Si on veut supprimer une IP
            $manager->supprimerIP($request->query->get('delete'));

            return redirect(
                'L\'adresse IP a bien été supprimée de l\'historique.',
                $this->generateUrl('zco_user_ips_index')
            );
        }

        $ipList = $manager->listerIPsBannies(
            (isset($_GET['fini']) && is_numeric($_GET['fini'])) ? $_GET['fini'] : null,
            !empty($_GET['ip']) ? $_GET['ip'] : null
        );
        \Page::$titre = 'Liste des adresses IP bannies';

        return render_to_response('ZcoUserBundle:Ips:index.html.php', [
            'ListerIPs' => $ipList,
        ]);
    }

    /**
     * Analyse une adresse IP en trouvant toutes ses occurences dans la BDD.
     */
    public function analyzeAction($ip, Request $request)
    {
        if (!verifier('ips_analyser')) {
            throw new AccessDeniedHttpException();
        }
        if (!$ip && $request->query->has('ip')) {
            $ip = $request->query->get('ip');
        }
        if ($ip) {
            $utilisateurs = \Doctrine_Core::getTable('UtilisateurIp')->findByIP($ip);
            $location = $this->get('zco_user.manager.ip')->Geolocaliser($ip);
        } else {
            $utilisateurs = [];
            $location = [];
        }
        \Page::$titre = 'Analyser une adresse IP';

        return render_to_response('ZcoUserBundle:Ips:analyze.html.php', array(
            'utilisateurs' => $utilisateurs,
            'nombre' => count($utilisateurs),
            'ip' => $ip,
            'pays' => $location['country'] ?? 'Inconnu',
        ));
    }

    /**
     * Tente de géolocaliser une adresse IP.
     */
    public function locateAction($ip, Request $request)
    {
        if (!verifier('ips_analyser')) {
            throw new AccessDeniedHttpException();
        }
        if (!$ip && $request->query->has('ip')) {
            $ip = $request->query->get('ip');
        }
        $manager = $this->get('zco_user.manager.ip');
        if ($manager->isLocal($ip)) {
            return redirect(
                'L\'adresse IP est une adresse privée (de type locale).',
                $this->generateUrl('zco_user_ips_analyze', ['ip' => $ip]),
                MSG_ERROR);
        }
        $location = $manager->Geolocaliser($ip);
        if (empty($location)) {
            return redirect(
                'L\'adresse IP n\'a pas pu être localisée.',
                $this->generateUrl('zco_user_ips_analyze', ['ip' => $ip]),
                MSG_ERROR);
        }
        $info = implode(', ', array_filter($location['city'] ?? null, $location['country'] ?? null));
        \Page::$titre = 'Géolocaliser une adresse IP';

        return render_to_response('ZcoUserBundle:Ips:locate.html.php', array(
            'info' => $info,
            'ip' => $ip,
            'longitude' => str_replace(',', '.', $location['longitude']),
            'latitude' => str_replace(',', '.', $location['latitude']),
        ));
    }

    /**
     * Affiche le formulaire permettant de bannir une adresse IP.
     */
    public function banAction($ip)
    {
        if (!verifier('ips_bannir')) {
            throw new AccessDeniedHttpException();
        }
        if (!empty($_POST['ip']) && is_numeric($_POST['duree'])) {
            //Si on a posté une nouvelle IP à bannir.
            $res = $this->get('zco_user.manager.ip')->BannirIp($_POST['ip'], $_POST['raison'], $_POST['texte'], $_POST['duree']);
            if ($res) {
                return redirect(
                    'L\'adresse IP a bien été bannie.',
                    $this->generateUrl('zco_user_ips_index')
                );
            } else {
                return redirect(
                    'L\'adresse IP spécifiée n\'est pas valide.',
                    $this->generateUrl('zco_user_ips_index'),
                    MSG_ERROR
                );
            }
        }
        \Page::$titre = 'Bannir une adresse IP';

        return render_to_response('ZcoUserBundle:Ips:ban.html.php', [
            'ip' => $ip,
        ]);
    }

    /**
     * Affiches les doublons d'IP
     *
     * @author Skydreamer
     */
    public function duplicatesAction()
    {
        if (!verifier('ips_analyser')) {
            throw new AccessDeniedHttpException();
        }
        \Page::$titre = 'Rechercher les doublons d\'adresses IP';
        $duplicates = $this->get('zco_user.manager.ip')->getDoublons();

        return render_to_response('ZcoUserBundle:Ips:duplicates.html.php', array(
            'doublons' => $duplicates,
        ));
    }
}
