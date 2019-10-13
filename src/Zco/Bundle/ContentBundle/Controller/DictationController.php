<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2019 Corrigraphie
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

namespace Zco\Bundle\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Zco\Bundle\ContentBundle\Chart\MyDictationStatsFrequencyChart;
use Zco\Bundle\ContentBundle\Chart\MyDictationStatsTemporalChart;
use Zco\Bundle\ContentBundle\Domain\Dictation;
use Zco\Bundle\ContentBundle\Domain\DictationDAO;
use Zco\Bundle\ContentBundle\Domain\DictationScoreDAO;
use Zco\Bundle\ContentBundle\Form\DictationType;

/**
 * Contrôleur gérant les actions liées aux dictées.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
final class DictationController extends Controller
{
    /**
     * Affiche la liste des dictées disponibles.
     *
     * @Route(name="zco_dictation_index", path="/dictees")
     */
    public function indexAction()
    {
        fil_ariane('Dictées');

        return $this->render('ZcoContentBundle:Dictation:index.html.php', [
            'dictations' => DictationDAO::ListerDictees(),
            'DicteeDifficultes' => Dictation::LEVELS,
        ]);
    }

    /**
     * @Route(name="zco_dictation_admin", path="/admin/dictees")
     * @return Response
     */
    public function adminAction()
    {
        if (!verifier('dictees_publier')) {
            throw new AccessDeniedHttpException();
        }

        fil_ariane([
            'Dictées' => $this->generateUrl('zco_dictation_index'),
            'Gestion des dictées',
        ]);

        return $this->render('ZcoContentBundle:Dictation:admin.html.php', array(
            'Dictees' => DictationDAO::ListerDictees(false),
            'DicteeEtats' => Dictation::STATUSES,
            'DicteeDifficultes' => Dictation::LEVELS,
        ));
    }

    /**
     * Lecture d'une dictée.
     *
     * @Route(name="zco_dictation_show", path="/dictees/{id}/{slug}", requirements={"id":"\d+"})
     * @param int $id
     * @param string $slug
     * @return Response
     */
    public function showAction($id, $slug = null)
    {
        $Dictee = $this->getDictation($id);
        if ($Dictee->etat != DICTEE_VALIDEE && !verifier('dictees_publier')) {
            throw new NotFoundHttpException();
        }
        if ($slug !== rewrite($Dictee->titre)) {
            // Redirect for SEO if slug is wrong.
            return new RedirectResponse($this->generateUrl('zco_dictation_show', ['id' => $Dictee->id, 'slug' => rewrite($Dictee->titre)]), 301);
        }

        fil_ariane([
            'Dictées' => $this->generateUrl('zco_dictation_index'),
            htmlspecialchars($Dictee->titre),
        ]);
        $this->get('zco_core.resource_manager')->requireResources([
            '@ZcoCoreBundle/Resources/public/css/zcode.css',
            '@ZcoContentBundle/Resources/public/css/dictees.css',
        ]);

        return $this->render('ZcoContentBundle:Dictation:dictee.html.php', [
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

        \Zco\Page::$titre = 'Correction de la dictée';
        fil_ariane(array(
            htmlspecialchars($Dictee->titre) => $url,
            'Correction'
        ));

        $this->get('zco_core.resource_manager')->requireResources([
            '@ZcoCoreBundle/Resources/public/css/zcode.css',
            '@ZcoContentBundle/Resources/public/css/dictees.css',
        ]);

        return $this->render('ZcoContentBundle:Dictation:corriger.html.php', [
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
     * @Route(name="zco_dictation_myStats", path="/dictees/mes-statistiques")
     * @param Request $request
     * @return Response
     */
    public function myStatsAction(Request $request)
    {
        if (!verifier('connecte')) {
            throw new NotFoundHttpException();
        }
        $count = $request->get('count', 20);
        fil_ariane([
            'Dictées' => $this->generateUrl('zco_dictation_index'),
            'Mes statistiques',
        ]);;

        return $this->render('ZcoContentBundle:Dictation:statistiques.html.php', [
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
     * @Route(name="zco_dictation_myStatsChart", path="/dictees/mes-statistiques.png")
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
            $chart = new MyDictationStatsFrequencyChart(DictationScoreDAO::FrequenceNotes());
            return $chart->getResponse();
        } elseif ($type == self::GRAPHIQUE_EVOLUTION) {
            $count = $request->get('count', 20);
            $count < 5 && $count = 5;
            $count > 50 && $count = 50;
            $chart = new MyDictationStatsTemporalChart(DictationScoreDAO::DernieresNotes($count, 0));
            return $chart->getResponse();
        }
        throw new NotFoundHttpException();
    }

    /**
     * Ajout d'une dictée.
     *
     * @Route(name="zco_dictation_new", path="/dictees/ajouter")
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        if (!verifier('dictees_publier')) {
            throw new AccessDeniedHttpException();
        }

        $form = $this->createForm(DictationType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            DictationDAO::AjouterDictee($form->getData());

            return redirect('La dictée a été ajoutée.', $this->generateUrl('zco_dictation_admin'));
        }

        fil_ariane([
            'Dictées' => $this->generateUrl('zco_dictation_index'),
            'Nouvelle dictée',
        ]);

        return $this->render('ZcoContentBundle:Dictation:new.html.php', ['form' => $form->createView()]);
    }

    /**
     * Modification d'une dictée.
     *
     * @Route(name="zco_dictation_edit", path="/dictees/modifier/{id}", requirements={"id":"\d+"})
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function editAction($id, Request $request)
    {
        if (!verifier('dictees_publier')) {
            throw new AccessDeniedHttpException();
        }

        $Dictee = $this->getDictation($id);
        $form = $this->createForm(DictationType::class, [
            'title' => $Dictee->titre,
            'level' => $Dictee->difficulte,
            'estimated_time' => $Dictee->temps_estime,
            'author_first_name' => $Dictee->auteur_prenom,
            'author_last_name' => $Dictee->auteur_nom,
            'source' => $Dictee->source,
            'description' => $Dictee->description,
            'indications' => $Dictee->indications,
            'comments' => $Dictee->commentaires,
            'text' => $Dictee->texte,
            'publish' => $Dictee->etat == DICTEE_VALIDEE,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            DictationDAO::EditerDictee($Dictee, $form->getData());

            return redirect(
                'La dictée a été modifiée.',
                $this->generateUrl('zco_dictation_show', ['id' => $Dictee->id, 'slug' => rewrite($Dictee->titre)])
            );
        }

        fil_ariane([
            'Dictées' => $this->generateUrl('zco_dictation_index'),
            htmlspecialchars($Dictee->titre) => $this->generateUrl('zco_dictation_show', ['id' => $Dictee->id, 'slug' => rewrite($Dictee->titre)]),
            'Modifier'
        ]);

        return $this->render('ZcoContentBundle:Dictation:edit.html.php', [
            'Dictee' => $Dictee,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Suppression d'une dictée.
     *
     * @Route(name="zco_dictation_delete", path="/dictees/supprimer/{id}", requirements={"id":"\d+"})
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function deleteAction($id, Request $request)
    {
        if (!verifier('dictees_publier')) {
            throw new AccessDeniedHttpException();
        }

        $Dictee = $this->getDictation($id);
        $url = $this->generateUrl('zco_dictation_show', ['id' => $Dictee->id, 'slug' => rewrite($Dictee->titre)]);

        if ($request->isMethod('POST')) {
            DictationDAO::SupprimerDictee($Dictee);
            return redirect('La dictée a été supprimée.', $this->generateUrl('zco_dictation_admin'));
        }

        fil_ariane([
            'Dictées' => $this->generateUrl('zco_dictation_index'),
            htmlspecialchars($Dictee->titre) => $url,
            'Supprimer'
        ]);

        return $this->render('ZcoContentBundle:Dictation:delete.html.php', compact('Dictee', 'url'));
    }

    /**
     * Passage d'une dictée en/hors ligne.
     *
     * @Route(name="zco_dictation_publish", path="/dictees/publier/{id}", requirements={"id":"\d+"})
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function publishAction($id, Request $request)
    {
        if (!verifier('dictees_publier')) {
            throw new AccessDeniedHttpException();
        }
        $Dictee = $this->getDictation($id);
        $status = (boolean)$request->get('status', false);
        DictationDAO::ValiderDictee($Dictee, $status);

        return redirect($status ? 'La dictée a bien été validée.' : 'La dictée a bien été refusée.',
            'dictee-' . $Dictee->id . '-' . rewrite($Dictee->titre) . '.html');
    }

    /**
     * @param int $id
     * @return \Dictee
     */
    private function getDictation($id)
    {
        $dictation = DictationDAO::Dictee($id);
        if (!$dictation) {
            throw new NotFoundHttpException();
        }

        return $dictation;
    }
}