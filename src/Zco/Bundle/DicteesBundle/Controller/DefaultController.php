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

namespace Zco\Bundle\DicteesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zco\Bundle\DicteesBundle\Chart\MyStatsFrequencyChart;
use Zco\Bundle\DicteesBundle\Chart\MyStatsTemporalChart;
use Zco\Bundle\DicteesBundle\Domain\Dictation;
use Zco\Bundle\DicteesBundle\Domain\DictationDAO;
use Zco\Bundle\DicteesBundle\Domain\DictationScoreDAO;

/**
 * Contrôleur gérant les actions liées aux dictées.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class DefaultController extends Controller
{
    /**
     * Affiche l'accueil des dictées.
     */
    public function indexAction()
    {
        fil_ariane('Accueil des dictées');
        $this->get('zco_core.resource_manager')->requireResources([
            '@ZcoPagesBundle/Resources/public/css/home.css',
            '@ZcoDicteesBundle/Resources/public/css/dictees.css',
        ]);

        return $this->render('ZcoDicteesBundle::index.html.php', [
            'DicteesAccueil' => DictationDAO::DicteesAccueil(),
            'DicteeHasard' => DictationDAO::DicteeHasard(),
            'DicteesLesPlusJouees' => DictationDAO::DicteesLesPlusJouees(),
            'Statistiques' => DictationDAO::DicteesStatistiques(),
            'DicteeDifficultes' => Dictation::LEVELS,
        ]);
    }

    /**
     * Affiche la liste des dictées disponibles.
     */
    public function listAction()
    {
        fil_ariane('Liste des dictées');

        return $this->render('ZcoDicteesBundle::liste.html.php', [
            'dictations' => DictationDAO::ListerDictees(),
            'DicteeDifficultes' => Dictation::LEVELS,
        ]);
    }

    public function adminAction()
    {
        if (!verifier('dictees_publier')) {
            throw new AccessDeniedHttpException();
        }

        fil_ariane('Gestion des dictées');

        return render_to_response('ZcoDicteesBundle::admin.html.php', array(
            'Dictees' => DictationDAO::ListerDictees(false),
            'DicteeEtats' => Dictation::STATUSES,
            'DicteeDifficultes' => Dictation::LEVELS,
        ));
    }

    /**
     * Lecture d'une dictée.
     *
     * @param int $id
     * @param string $slug
     * @return Response
     */
    public function showAction($id, $slug)
    {
        $Dictee = $this->getDictation($id);
        if ($Dictee->etat != DICTEE_VALIDEE && !verifier('dictees_publier')) {
            throw new NotFoundHttpException();
        }

        //TODO zCorrecteurs::VerifierFormatageUrl($Dictee->titre, true);

        fil_ariane(htmlspecialchars($Dictee->titre));
        $this->get('zco_core.resource_manager')->requireResources([
            '@ZcoCoreBundle/Resources/public/css/zcode.css',
            '@ZcoDicteesBundle/Resources/public/css/dictees.css',
        ]);

        return render_to_response('ZcoDicteesBundle::dictee.html.php', [
            'Dictee' => $Dictee,
            'DicteeDifficultes' => Dictation::LEVELS,
            'DicteeEtats' => Dictation::STATUSES,
        ]);
    }

    /**
     * Correction d'une dictée.
     *
     * @param $id
     * @return Response
     */
    public function playAction($id)
    {
        $Dictee = $this->getDictation($id);
        if ($Dictee->etat != DICTEE_VALIDEE && !verifier('dictees_publier')) {
            throw new NotFoundHttpException();
        }

        $url = 'dictee-' . $Dictee->id . '-' . rewrite($Dictee->titre) . '.html';
        if (empty($_POST['texte'])) {
            return new RedirectResponse($url);
        }

        //On vérifie qu'il y ait un minimum de ressemblance entre les deux textes.
        //Pour cela on vérifie que le nombre de mots soumis soit au moins 60% du
        //nombre de mots du texte original.
        $nbMotsOriginal = count(explode(' ', $Dictee->texte));
        $nbMotsSoumis = count(explode(' ', $_POST['texte']));
        if ($nbMotsSoumis / $nbMotsOriginal < 0.6)
            return redirect(
                'Soit vous avez fait beaucoup trop de fautes, soit vous n\'avez pas terminé la dictée, soit vous vous êtes trompé de texte !',
                $url,
                MSG_ERROR
            );

        list($diff, $note) = DictationDAO::CorrigerDictee($Dictee, $_POST['texte']);
        $fautes = $diff->fautes();

        \Page::$titre = 'Correction de la dictée';
        fil_ariane(array(
            htmlspecialchars($Dictee->titre) => $url,
            'Correction'
        ));

        $this->get('zco_core.resource_manager')->requireResources([
            '@ZcoCoreBundle/Resources/public/css/zcode.css',
            '@ZcoDicteesBundle/Resources/public/css/dictees.css',
        ]);

        return $this->render('ZcoDicteesBundle::corriger.html.php', [
            'Dictee' => $Dictee,
            'note' => $note,
            'diff' => $diff,
            'DicteeEtats' => Dictation::STATUSES,
            'DicteeDifficultes' => Dictation::LEVELS,
        ]);
    }

    /**
     * Statistiques sur un membre.
     *
     * @param Request $request
     * @param int $count
     * @return Response
     */
    public function myStatsAction(Request $request)
    {
        if (!verifier('connecte')) {
            throw new NotFoundHttpException();
        }
        \Page::$titre = 'Mes statistiques';
        $count = $request->get('count', 20);

        return $this->render('ZcoDicteesBundle::statistiques.html.php', [
            'participations' => $count,
            'DernieresNotes' => DictationScoreDAO::DernieresNotes($count),
            'MesStatistiques' => DictationScoreDAO::MesStatistiques(),
            'DicteeCouleurs' => Dictation::COLORS,
            'DicteeDifficultes' => Dictation::LEVELS,
        ]);
    }

    const GRAPHIQUE_FREQUENCE = 1;
    const GRAPHIQUE_EVOLUTION = 2;

    /**
     * Graphiques de la progression d'un membre sur les dictées.
     *
     * @param Request $request
     * @return Response
     */
    public function myStatsChartAction(Request $request)
    {
        if (!verifier('connecte')) {
            throw new NotFoundHttpException();
        }
        $type = (int) $request->get('type', self::GRAPHIQUE_FREQUENCE);
        if ($type == self::GRAPHIQUE_FREQUENCE) {
            $chart = new MyStatsFrequencyChart(DictationScoreDAO::FrequenceNotes());
            return $chart->getResponse();
        } elseif ($type == self::GRAPHIQUE_EVOLUTION) {
            $count = $request->get('count', 20);
            $count < 5 && $count = 5;
            $count > 50 && $count = 50;
            $chart = new MyStatsTemporalChart(DictationScoreDAO::DernieresNotes($count, 0));
            return $chart->getResponse();
        }
        throw new NotFoundHttpException();
    }

    /**
     * Ajout d'une dictée.
     */
    public function newAction()
    {
        if (!verifier('dictees_publier')) {
            throw new AccessDeniedHttpException();
        }

        include_once(__DIR__ . '/../forms/AjouterForm.class.php');
        $Form = new \AjouterForm();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $Form->bind($_POST);
            if ($Form->isValid()) {
                $r = DictationDAO::AjouterDictee($Form);
                if (!$r)
                    return redirect('Une erreur est survenue lors de l\'envoi du fichier audio.', '', MSG_ERROR);
                elseif ($r instanceof Response)
                    return $r;
                return redirect('La dictée a été ajoutée.', 'index.html');
            }
        }
        fil_ariane('Ajouter une dictée');

        return $this->render('ZcoDicteesBundle::new.html.php', ['Form' => $Form]);
    }

    /**
     * Modification d'une dictée.
     *
     * @param int $id
     * @return Response
     */
    public function editAction($id)
    {
        if (!verifier('dictees_publier')) {
            throw new AccessDeniedHttpException();
        }

        $Dictee = $this->getDictation($id);

        if (isset($_SESSION['dictee_data'])) {
            $_POST = $_SESSION['dictee_data'];
            unset($_SESSION['dictee_data']);
        }

        \Page::$titre = 'Modifier une dictée';

        include(__DIR__ . '/../forms/AjouterForm.class.php');
        $Form = new \AjouterForm();

        $url = '-' . $Dictee->id . '-' . rewrite($Dictee->titre) . '.html';

        $data = $Dictee->toArray();
        $data['publique'] = $data['etat'] == DICTEE_VALIDEE;
        $Form->setDefaults($data);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $Form->bind($_POST);
            if ($Form->isValid()) {
                $r = DictationDAO::EditerDictee($Dictee, $Form);
                if (!$r) {
                    $_SESSION['dictee_data'] = $_POST;
                    return redirect('Une erreur est survenue lors de l\'envoi du fichier audio.', 'editer' . $url, MSG_ERROR);
                } elseif ($r instanceof Response)
                    return $r;
                return redirect('La dictée a été modifiée.', 'dictee' . $url);
            }
            $Form->setDefaults($_POST);
        }

        fil_ariane(array(
            htmlspecialchars($Dictee->titre) => 'dictee' . $url,
            'Editer'
        ));

        return render_to_response('ZcoDicteesBundle::edit.html.php', compact('Dictee', 'Form'));
    }

    /**
     * Suppression d'une dictée.
     *
     * @param int $id
     * @return Response
     */
    public function deleteAction($id)
    {
        if (!verifier('dictees_publier')) {
            throw new AccessDeniedHttpException();
        }

        $Dictee = $this->getDictation($id);
        \Page::$titre = 'Supprimer une dictée';

        $url = 'dictee-' . $Dictee->id . '-' . rewrite($Dictee->titre) . '.html';

        // Suppression / Annulation
        if (isset($_POST['confirmer'])) {
            DictationDAO::SupprimerDictee($Dictee);
            return redirect('La dictée a été supprimée.', 'index.html');
        }
        if (isset($_POST['annuler'])) {
            return new RedirectResponse($url);
        }

        fil_ariane(array(
            htmlspecialchars($Dictee->titre) => $url,
            'Supprimer'
        ));

        return $this->render('ZcoDicteesBundle::delete.html.php', compact('Dictee', 'url'));
    }

    /**
     * Passage d'une dictée en/hors ligne.
     *
     * @param int $id
     * @param bool $status
     * @return Response
     */
    public function publishAction($id, $status)
    {
        if (!verifier('dictees_publier')) {
            throw new AccessDeniedHttpException();
        }
        $Dictee = $this->getDictation($id);
        DictationDAO::ValiderDictee($Dictee, $status);

        return redirect($status ? 'La dictée a bien été validée.' : 'La dictée a bien été refusée.',
            'dictee-' . $Dictee->id . '-' . rewrite($Dictee->titre) . '.html');
    }

    private function getDictation($id)
    {
        $dictation = DictationDAO::Dictee($id);
        if (!$dictation) {
            throw new NotFoundHttpException();
        }

        return $dictation;
    }
}