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

namespace Zco\Bundle\PubliciteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Publicite;
use PubliciteCampagne;
use PublicitePays;
use Doctrine_Core;
use Page;

/**
 * 
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class DefaultController extends Controller
{
    /**
     * Affiche une vue d'ensemble de l'ensemble des campagnes.
     * 
     * @param  Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $all      = $request->query->has('all') && verifier('publicite_voir');
        $userId   = $all ? null : $_SESSION['id'];
        $statuses = $request->query->get('etat') ? : array(PubliciteCampagne::RUNNING, PubliciteCampagne::PAUSED, PubliciteCampagne::COMPLETED);
        $campaigns = Doctrine_Core::getTable('PubliciteCampagne')->listAll($userId, $statuses);

        $displaysCount = 0;
        $clicksCount   = 0;
        foreach ($campaigns as $campaign) {
            $displaysCount += $campaign['nb_affichages'];
            $clicksCount   += $campaign['nb_clics'];
        }

        $queryParameters = array();
        foreach ($statuses as $status) {
            if (!isset($queryParameters['etat'])) {
                $queryParameters['etat'] = array();
            }
            $queryParameters['etat'][] = $status;
        }
        if ($all) {
            $queryParameters['all'] = 1;
        }

        fil_ariane('Publicité');

        return render_to_response('ZcoPubliciteBundle::index.html.php', array(
                'all'          => $all,
                'statuses'     => $statuses,
                'campagnes'    => $campaigns,
                'total_aff'    => $displaysCount,
                'total_clic'   => $clicksCount,
                'total_taux'   => $displaysCount > 0 ? $clicksCount * 100 / $displaysCount : 0,
                'queryParameters' => $queryParameters,
                'couleurs'     => array('rouge', 'vertf', 'bleu', 'noir', 'orange', 'violet', 'gris'),
            ));
    }

    public function newAction(Request $request, $id = null)
    {
        $campagne = false;
        if (null !== $id) {
            try {
                $campagne = $this->getCampaign($id);
            } catch (Exception $e) {
            }
        }

        if ($request->getMethod() === 'POST') {
            foreach (array('titre', 'emplacement', 'url_cible', 'contenu', 'nom') as $champ) {
                $_POST[$champ] = isset($_POST[$champ]) ? trim($_POST[$champ]) : null;
                if (empty($_POST[$champ]))
                    return redirect(17, '', MSG_ERROR);
            }
            if ($campagne == false) {
                $campagne                   = new PubliciteCampagne();
                $campagne['utilisateur_id'] = $_SESSION['id'];
                $campagne['nom']            = $_POST['nom'];
                $campagne['etat']           = PubliciteCampagne::RUNNING;
                $campagne['date_debut']     = $_POST['prog'] == 'periode' ? $_POST['date_debut'] : new Doctrine_Expression('NOW()');
                $campagne['date_fin']       = $_POST['prog'] == 'periode' && !empty($_POST['date_fin']) ? $_POST['date_fin'] : null;
                $campagne->save();
            }

            $publicite                     = new Publicite();
            $publicite['campagne_id']      = $campagne['id'];
            $publicite['titre']            = $_POST['titre'];
            $publicite['emplacement']      = $_POST['emplacement'];
            $publicite['url_cible']        = $_POST['url_cible'];
            $publicite['contenu']          = $_POST['contenu'];
            $publicite['contenu_js']       = verifier('publicite_js') && isset($_POST['contenu_js']);
            $publicite['actif']            = isset($_POST['actif']) && (verifier('publicite_changer_etat_siens') || verifier('publicite_changer_etat'));
            $publicite['approuve']         = $publicite['actif'] ? 'approuve' : 'attente';
            $publicite->save();
            
            $this->applyTargeting($publicite, $request);
            $publicite->save();
            
            if ($publicite->estAffichable()) {
                $this->get('zco_core.cache')->delete('partenaires_' . $publicite['emplacement']);
            }

            return redirect(
                $publicite['actif'] ? 
                    'La publicité a bien été ajoutée.' : 
                    'Votre offre de partenariat a bien été proposée. Elle va être examinée par les administrateurs. Merci de votre confiance !',
                $this->generateUrl('zco_ads_campaign', array('id' => $campagne['id']))
            );
        }

        if (false !== $campagne) {
            Page::$titre = 'Nouvelle publicité - '.$campagne['nom'];
            fil_ariane(array(
                'Publicité'                                   => $this->generateUrl('zco_ads_index'),
                htmlspecialchars($publicite->Campagne['nom']) => $this->generateUrl('zco_ads_campaign', array('id' => $publicite['campagne_id'])),
                'Nouvelle publicité',
            ));
        } else {
            Page::$titre = 'Nouvelle publicité';
            fil_ariane(array(
                'Publicité' => $this->generateUrl('zco_ads_index'),
                'Nouvelle publicité',
            ));
        }

        return render_to_response('ZcoPubliciteBundle::new.html.php', array(
                'campagne'       => $campagne,
                'nb_membres_age' => Doctrine_Core::getTable('Utilisateur')->compterMembresAge(),
                'pays'           => Doctrine_Core::getTable('Pays')->findAll(),
            ));
    }

    public function advertismentAction(Request $request, $id)
    {
        $publicite = $this->getAdvertisment($id);
        if (!verifier('publicite_voir') && ($publicite->Campagne['utilisateur_id'] != $_SESSION['id'])) {
            throw new AccessDeniedHttpException();
        }

        if ($request->query->has('week')) {
            list($jour, $mois, $annee) = explode('-', $request->query->get('week'));
        } else {
            list($jour, $mois, $annee) = explode('-', date('d-m-Y', date('N') == 1 ? time() : strtotime('previous monday')));
        }
        $stats = Doctrine_Core::getTable('PubliciteStat')->getForWeek($publicite['id'], $jour, $mois, $annee);

        //Inclusion de la vue
        Page::$titre = htmlspecialchars($publicite['titre'] . ' - ' . $publicite->Campagne['nom']);
        fil_ariane(array(
            'Publicité'                                   => $this->generateUrl('zco_ads_index'),
            htmlspecialchars($publicite->Campagne['nom']) => $this->generateUrl('zco_ads_campaign', array('id' => $publicite['campagne_id'])),
            htmlspecialchars($publicite['titre']),
        ));

        return render_to_response('ZcoPubliciteBundle::advertisment.html.php', array(
                'annee'     => $annee,
                'mois'      => $mois,
                'jour'      => $jour,
                'publicite' => $publicite,
                'stats'     => $stats,
                'weeks'     => Doctrine_Core::getTable('PubliciteStat')->getWeeks(),
                'week'      => $annee . '-' . $mois . '-' . $jour,
            ));
    }

    /**
     * Affiche les informations sur une campagne, dont les publicités 
     * rattachées à celle-ci et les statistiques de la campagne.
     * 
     * @param  integer $id L'id de la campagne
     * @return Response
     */
    public function campaignAction($id)
    {
        $campagne = $this->getCampaign($id);
        
        Page::$titre = htmlspecialchars($campagne['nom']);
        fil_ariane(array(
            'Publicité' => $this->generateUrl('zco_ads_index'),
            htmlspecialchars($campagne['nom']),
        ));

        return render_to_response('ZcoPubliciteBundle::campaign.html.php', array(
            'campagne'   => $campagne,
            'publicites' => Doctrine_Core::getTable('Publicite')->findByCampagneId($campagne['id']),
        ));
    }

    /**
     * Formulaire pour modifier l'apparence d'une publicite.
     * 
     * @param  $request
     * @param  integer $id L'id de la publicité
     * @return Response
     */
    public function editAppearanceAction(Request $request, $id)
    {
        $publicite = $this->getAdvertisment($id, 'editer');

        if ('POST' === $request->getMethod()) {
            $publicite['titre']      = $request->request->get('titre');
            $publicite['url_cible']  = $request->request->get('url_cible');
            $publicite['contenu']    = $request->request->get('contenu');
            $publicite['contenu_js'] = verifier('publicite_js') && $request->request->has('contenu_js');
            $publicite->save();
            
            $this->get('zco_core.cache')->delete('partenaires_' . $publicite['emplacement']);
            
            return redirect(
                'L\'apparence a bien été modifiée. Elle prend effet immédiatement.', 
                $this->generateUrl('zco_ads_advertisment', array('id' => $publicite['id']))
            );
        }

        //Inclusion de la vue
        Page::$titre = htmlspecialchars($publicite['titre'] . ' - ' . $publicite->Campagne['nom']);
        fil_ariane(array(
            'Publicité'                                   => $this->generateUrl('zco_ads_index'),
            htmlspecialchars($publicite->Campagne['nom']) => $this->generateUrl('zco_ads_campaign', array('id'                                  => $publicite['campagne_id'])),
            htmlspecialchars($publicite['titre'])         => $this->generateUrl('zco_ads_advertisment', array('id' => $publicite['id'])),
            'Apparence',
        ));

        return render_to_response('ZcoPubliciteBundle::editAppearance.html.php', array(
            'publicite' => $publicite,
        ));
    }

    public function editOwnerAction(Request $request, $id)
    {
        $campagne = $this->getCampaign($id);

        if (!verifier('publicite_editer_createur') && (!verifier('publicite_editer_createur_siens') || $campagne['utilisateur_id'] != $_SESSION['id'])) {
            throw new AccessDeniedHttpException();
        }
        
        if ('POST' === $request->getMethod()) {
            $utilisateur = Doctrine_Core::getTable('Utilisateur')->getOneByPseudo($request->request->get('pseudo'));
            if (false === $utilisateur) {
                return redirect(
                    'L\'utilisateur indiqué n\'existe pas.', 
                    $this->generateUrl('zco_ads_owner', array('id' => $campagne['id'])), 
                    MSG_ERROR
                );
            }
            
            $campagne['utilisateur_id'] = $utilisateur['id'];
            $campagne->save();
            
            return redirect(
                'Le propriétaire de la campagne a bien été modifié.', 
                $this->generateUrl('zco_ads_campaign', array('id' => $campagne['id']))
            );
        }

        Page::$titre = htmlspecialchars($campagne['nom']);
        fil_ariane(array(
            'Publicité'                        => $this->generateUrl('zco_ads_index'),
            htmlspecialchars($campagne['nom']) => $this->generateUrl('zco_ads_campaign', array('id' => $campagne['id'])),
            'Propriétaire',
        ));

        return render_to_response('ZcoPubliciteBundle::editOwner.html.php', array(
            'campagne' => $campagne,
        ));
    }

    public function editTargetingAction(Request $request, $id)
    {
        $publicite = $this->getAdvertisment($id, 'editer_ciblage');
        
        $pays = mpull($publicite->Pays->toArray(), 'getId');
        
        if ('POST' === $request->getMethod()) {
            $this->applyTargeting($publicite, $request, $pays);
            $publicite->save();
            $publicite->mettreEnCache();
            
            return redirect(
                'Le ciblage a bien été modifié. Il prend effet immédiatement.', 
                $this->generateUrl('zco_ads_advertisment', array('id' => $publicite['id']))
            );
        }

        //Inclusion de la vue
        Page::$titre = htmlspecialchars($publicite['titre'] . ' - ' . $publicite->Campagne['nom']);
        fil_ariane(array(
            'Publicité'                                   => $this->generateUrl('zco_ads_index'),
            htmlspecialchars($publicite->Campagne['nom']) => $this->generateUrl('zco_ads_campaign', array('id'                                  => $publicite['campagne_id'])),
            htmlspecialchars($publicite['titre']) => $this->generateUrl('zco_ads_advertisment', array('id' => $publicite['id'])),
            'Ciblage',
        ));

        return render_to_response('ZcoPubliciteBundle::editTargeting.html.php', array(
                'publicite'         => $publicite,
                'categories'        => Doctrine_Core::getTable('Categorie')->getCategoriesCiblables(),
                'pays'              => Doctrine_Core::getTable('Pays')->findAll(),
                'nb_membres_age'    => Doctrine_Core::getTable('Utilisateur')->compterMembresAge(),
                'attr_pays'         => $pays,
                'cibler_pays'       => count($publicite->Pays) > 0,
                'cibler_age'        => !empty($publicite['age_min']) || !empty($publicite['age_max']),
                'cibler_age_min'    => !empty($publicite['age_min']),
                'cibler_age_max'    => !empty($publicite['age_max']),
            ));
    }

    public function deleteAction(Request $request, $id)
    {
        $campaign = $this->getCampaign($id);

        if ('POST' === $request->getMethod()) {
            $campaign->delete();
            $this->get('zco_core.cache')->delete('partenaires_*');

            return redirect(
                'La campagne a bien été supprimée.', 
                $this->generateUrl('zco_ads_index')
            );
        }

        Page::$titre = htmlspecialchars($campaign['nom']);
        fil_ariane(array(
            'Publicité'                        => $this->generateUrl('zco_ads_index'),
            htmlspecialchars($campaign['nom']) => $this->generateUrl('zco_ads_campaign', array('id' => $campaign['id'])),
            'Supprimer définitivement',
        ));
        
        return render_to_response('ZcoPubliciteBundle::delete.html.php', array('campagne' => $campaign));
    }

    public function resetClicksAction($id, $date, $token)
    {
        if ($token !== $_SESSION['token']) {
            throw new AccessDeniedHttpException();
        }

        $publicite = $this->getAdvertisment($id);
        $publicite->razClics($date);

        return redirect(
            'Les clics ont bien été remis à zéro.', 
            $this->generateUrl('zco_ads_advertisment', array('id' => $id))
        );
    }

    public function resetDisplaysAction($id, $date, $token)
    {
        if ($token !== $_SESSION['token']) {
            throw new AccessDeniedHttpException();
        }

        $publicite = $this->getAdvertisment($id);
        $publicite->razAffichages($date);

        return redirect(
            'Les affichages ont bien été remis à zéro.', 
            $this->generateUrl('zco_ads_advertisment', array('id' => $id))
        );
    }

    protected function getAdvertisment($id, $credential = null) {
        $advertisment = Doctrine_Core::getTable('Publicite')->findOneById($id);
        if (false === $advertisment) {
            throw new NotFoundHttpException();
        }

        if (null !== $credential) {
            if (!verifier('publicite_'.$credential) && ($advertisment->Campagne['utilisateur_id'] != $_SESSION['id'] || !verifier('publicite_'.$credential.'_siens'))) {
                throw new AccessDeniedHttpException();
            }
        }
        
        return $advertisment;
    }
    
    protected function getCampaign($id) {
        $campaign = Doctrine_Core::getTable('PubliciteCampagne')->find($id);
        if (false === $campaign) {
            throw new NotFoundHttpException();
        }

        if (!verifier('publicite_voir') && $campaign['utilisateur_id'] != $_SESSION['id']) {
            throw new AccessDeniedHttpException();
        }
        
        return $campaign;
    }
    
    protected function applyTargeting(Publicite $publicite, Request $request, array $pays = array()) {
        //Ciblage par âge.
        if (!$request->request->has('cibler_age')) {
            $publicite['age_min']         = ($request->request->has('aucun_age_min') || $request->request->get('age_min', '-') === '-') ? null : $request->request->get('age_min');
            $publicite['age_max']         = ($request->request->has('aucun_age_max') || $request->request->get('age_max', '-') === '-') ? null‡ : $request->request->get('age_max');
            $publicite['aff_age_inconnu'] = $request->request->has('age_inconnu');
        } else {
            $publicite['age_min']         = null;
            $publicite['age_max']         = null;
            $publicite['aff_age_inconnu'] = true;
        }

        //Ciblage par pays.
        if (!$request->request->has('cibler_pays')) {
            $publicite['aff_pays_inconnu'] = $request->request->has('pays_inconnu');
            foreach ($request->request->get('pays', array()) as $p) {
                if (!in_array($p, $pays)) {
                    $adCountry                 = new PublicitePays();
                    $adCountry['publicite_id'] = $publicite['id'];
                    $adCountry['pays_id']      = $p;
                    $adCountry->save();
                } else {
                    unset($pays[$p]);
                }
            }
            if (count($pays)) {
                Doctrine_Query::create()
                    ->delete('PublicitePays')
                    ->where('publicite_id = ?', $publicite['id'])
                    ->andWhereIn('pays_id', $pays)
                    ->execute();
            }
        } else {
            $publicite['aff_pays_inconnu'] = true;
        }

        //Ciblage par catégorie.
        $publicite['aff_accueil'] = $request->request->has('aff_accueil');
    }
}