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

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Contrôleur gérant la suppression d'un participant à un MP.
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class SupprimerParticipantAction extends Controller
{
    public function execute()
    {
        if (!verifier('connecte')) {
            throw new AccessDeniedHttpException();
        }
        if (isset($_POST['annuler'])) {
            return new RedirectResponse('Location: lire-' . $_GET['id'] . '.html');
        }
        include(BASEPATH . '/src/Zco/Bundle/MpBundle/modeles/lire.php');
        include(BASEPATH . '/src/Zco/Bundle/MpBundle/modeles/participants.php');

        if (!empty($_GET['id']) AND is_numeric($_GET['id']) AND !empty($_GET['id2']) AND is_numeric($_GET['id2'])) {
            $InfoMP = InfoMP();

            if (isset($InfoMP['mp_id']) AND !empty($InfoMP['mp_id'])) {
                $autoriser_ecrire = true;
                if (empty($InfoMP['mp_participant_mp_id']) AND verifier('mp_espionner')) {
                    $autoriser_ecrire = false;
                }
                if ($autoriser_ecrire) {
                    //Vérification du nombre de participants (2 minimum)
                    $ListerParticipants = ListerParticipants($_GET['id']);
                    $NombreParticipants = 0;
                    foreach ($ListerParticipants as $valeur) {
                        if ($valeur['mp_participant_statut'] != MP_STATUT_SUPPRIME) {
                            $NombreParticipants++;
                        }
                    }
                    if ($NombreParticipants <= 2) {
                        return redirect(
                            'Vous ne pouvez pas supprimer de participant : vous êtes deux.',
                            'lire-' . $_GET['id'] . '.html',
                            MSG_ERROR
                        );
                    }
                    //Fin vérification

                    //Vérification : le participant à enlever existe-t-il ?
                    $InfoParticipant = InfoParticipant();
                    if (empty($InfoParticipant['mp_participant_id']) OR $InfoParticipant['mp_participant_statut'] == -1) {
                        return redirect(
                            'Le participant n\'existe pas, il ne participe pas à ce MP ou il a été supprimé.',
                            'lire-' . $_GET['id'] . '.html',
                            MSG_ERROR
                        );
                    }
                    //Vérification : a-t-on le droit de supprimer ce membre du MP ?
                    if ($_SESSION['id'] != $_GET['id2'] AND ($InfoMP['mp_participant_statut'] == MP_STATUT_OWNER OR verifier('mp_tous_droits_participants')) OR ($InfoMP['mp_participant_statut'] == MP_STATUT_MASTER AND $InfoParticipant['mp_participant_statut'] < MP_STATUT_MASTER)) {
                        if (!isset($_POST['confirmation'])) {
                            fil_ariane(array(
                                htmlspecialchars($InfoMP['mp_titre']) => 'lire-' . $_GET['id'] . '.html',
                                'Supprimer un participant du message'
                            ));
                            $InfoParticipant = InfoParticipant();
                            return render_to_response(array(
                                'InfoParticipant' => $InfoParticipant,
                                'InfoMP' => $InfoMP,
                            ));
                        } else {
                            SupprimerParticipant();
                            return redirect(
                                'Le participant a été éjecté avec succès de la conversation.',
                                'lire-' . $_GET['id'] . '.html'
                            );
                        }
                    } else {
                        throw new AccessDeniedHttpException();
                    }
                } else {
                    throw new AccessDeniedHttpException();
                }
            } else {
                throw new NotFoundHttpException();
            }
        } else {
            throw new NotFoundHttpException();
        }
    }
}
