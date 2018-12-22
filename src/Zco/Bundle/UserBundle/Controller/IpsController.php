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

namespace Zco\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

        return $this->render('ZcoUserBundle:Ips:index.html.php', [
            'ListerIPs' => $ipList,
        ]);
    }

    /**
     * Tente de géolocaliser une adresse IP.
     *
     * @param Request $request
     * @return Response
     */
    public function locateAction(Request $request)
    {
        if (!verifier('ips_analyser')) {
            throw new AccessDeniedHttpException();
        }
        $ip = $request->get('ip');
        if (!$ip) {
            throw new NotFoundHttpException();
        }
        $manager = $this->get('zco_user.manager.ip');
        if ($manager->isLocal($ip)) {
            return redirect(
                'L\'adresse IP est une adresse privée (de type locale).',
                $request->headers->get('referer'),
                MSG_ERROR);
        }
        $location = $manager->Geolocaliser($ip);
        if (empty($location)) {
            return redirect(
                'L\'adresse IP n\'a pas pu être localisée.',
                $request->headers->get('referer'),
                MSG_ERROR);
        }
        $info = implode(', ', array_filter($location['city'] ?? null, $location['country'] ?? null));
        \Page::$titre = 'Géolocaliser une adresse IP';

        return $this->render('ZcoUserBundle:Ips:locate.html.php', array(
            'info' => $info,
            'ip' => $ip,
            'longitude' => str_replace(',', '.', $location['longitude']),
            'latitude' => str_replace(',', '.', $location['latitude']),
        ));
    }

    /**
     * Affiche le formulaire permettant de bannir une adresse IP.
     *
     * @param Request $request
     * @return Response
     */
    public function banAction(Request $request)
    {
        if (!verifier('ips_bannir')) {
            throw new AccessDeniedHttpException();
        }
        $ip = $request->get('ip');
        if ($ip && $request->isMethod('POST')) {
            //Si on a posté une nouvelle IP à bannir.
            $res = $this->get('zco_user.manager.ip')->BannirIp($ip, $_POST['raison'], $_POST['texte'], $_POST['duree']);
            if (!$res) {
                return redirect(
                    'L\'adresse IP spécifiée n\'est pas valide.',
                    $this->generateUrl('zco_user_ips_index'),
                    MSG_ERROR
                );
            }

            return redirect(
                'L\'adresse IP a bien été bannie.',
                $this->generateUrl('zco_user_ips_index')
            );
        }
        \Page::$titre = 'Bannir une adresse IP';

        return $this->render('ZcoUserBundle:Ips:ban.html.php', [
            'ip' => $ip,
        ]);
    }
}
