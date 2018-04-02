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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Contrôleur gérant l'affichage d'une candidature et de toutes les informations
 * lui étant associées.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
include(__DIR__ . '/../modeles/quiz.php');

class CandidatureAction extends Controller
{
    public function execute()
    {
        if (!verifier('recrutements_voir_candidatures')) {
            throw new AccessDeniedHttpException();
        }
        //Si on a bien envoyé une candidature
        if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
            if (verifier('recrutements_avis')) {
                if (isset($_POST['type'])) {
                    if ($_POST['act'] == 'insert') {
                        $avis = new RecrutementAvis;
                        $avis['utilisateur_id'] = $_SESSION['id'];
                        $avis['candidature_id'] = $_GET['id'];
                        $avis['type'] = $_POST['type'];
                        $avis->save();
                    } elseif ($_POST['act'] == 'update') {
                        $avis = Doctrine_Core::getTable('RecrutementAvis')->findOneByUtilisateurIdAndCandidatureId($_SESSION['id'], $_GET['id']);
                        $avis['type'] = $_POST['type'];
                        $avis->save();
                    }
                }
            }

            $InfosCandidature = InfosCandidature($_GET['id']);
            if (empty($InfosCandidature))
                throw new NotFoundHttpException();

            Page::$titre = 'Candidature de ' . htmlspecialchars($InfosCandidature['utilisateur_pseudo']);

            if (($InfosCandidature['recrutement_etat'] != RECRUTEMENT_FINI &&
                    verifier('recrutements_voir_shoutbox'))
                || ($InfosCandidature['recrutement_etat'] == RECRUTEMENT_FINI &&
                    verifier('recrutements_termines_voir_shoutbox'))) {
                include(dirname(__FILE__) . '/../modeles/commentaires.php');


                //Si on veut voir un commentaire en particulier
                if (!empty($_GET['id2']) && is_numeric($_GET['id2'])) {
                    $page = TrouverPageCommentaire($_GET['id2'], $_GET['id']);
                    if ($page !== false) {
                        $page = ($page > 1) ? '-p' . $page : '';
                        return new RedirectResponse('candidature-' . $_GET['id'] . $page . '.html#c' . $_GET['id2']);
                    } else
                        throw new NotFoundHttpException();
                }


                $NombreDeCommentaires = CompterCommentairesShoutbox($_GET['id']);
                $Page = (!empty($_GET['p']) && is_numeric($_GET['p'])) ? $_GET['p'] : 1;
                $NombreDePage = ceil($NombreDeCommentaires / 15);

                $ListerCommentaires = ListerCommentairesShoutbox($_GET['id'], $Page);
                $ListePage = liste_pages($Page, $NombreDePage, $NombreDeCommentaires, 15, 'candidature-' . $_GET['id'] . '-p%s.html');
                //On marque les commentaires comme lus s'il y en a
                if ($NombreDeCommentaires > 0 && verifier('connecte'))
                    MarquerCommentairesLus($InfosCandidature, $Page, $ListerCommentaires);
            } else {
                $NombreDeCommentaires = 0;
                $NombreDePage = 0;
                $ListerCommentaires = null;
                $ListePage = null;
            }

            $CandidaturePrecedente = RecupererIdCandidaturePrecedente($_GET['id'], $InfosCandidature['recrutement_id']);
            $CandidatureSuivante = RecupererIdCandidatureSuivante($_GET['id'], $InfosCandidature['recrutement_id']);

            $avis = array();
            $resultat = Doctrine_Query::create()
                ->select('COUNT(*) AS votes, type')
                ->from('RecrutementAvis')
                ->where('candidature_id = ?', $_GET['id'])
                ->groupBy('type')
                ->execute();
            foreach ($resultat as $donnees) {
                $avis[$donnees['type']] = $donnees['votes'];
            }

            $resultat = Doctrine_Query::create()
                ->select('type')
                ->from('RecrutementAvis')
                ->where('candidature_id = ?', $_GET['id'])
                ->andWhere('utilisateur_id = ?', $_SESSION['id'])
                ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

            //Inclusion de la vue
            fil_ariane(array(
                htmlspecialchars($InfosCandidature['recrutement_nom']) => 'recrutement-' . $InfosCandidature['recrutement_id'] . '.html',
                'Candidature de ' . htmlspecialchars($InfosCandidature['utilisateur_pseudo'])
            ));
            $this->get('zco_vitesse.resource_manager')->requireResources(array(
                '@ZcoQuizBundle/Resources/public/css/quiz.css',
                '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css',
            ));

            $questions = RecupererReponses($InfosCandidature['recrutement_id'], $InfosCandidature['utilisateur_id']);
            return render_to_response(array(
                'InfosCandidature' => $InfosCandidature,
                'CandidaturePrecedente' => $CandidaturePrecedente,
                'CandidatureSuivante' => $CandidatureSuivante,
                'avis' => $avis,
                'resultat' => $resultat,
                'NombreDeCommentaires' => $NombreDeCommentaires,
                'NombreDePage' => $NombreDePage,
                'ListerCommentaires' => $ListerCommentaires,
                'ListePage' => $ListePage,
                'questions' => $questions
            ));
        } else
            throw new NotFoundHttpException();
    }
}
