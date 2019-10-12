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

namespace Zco\Bundle\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Zco\Bundle\CoreBundle\Entity\Contact;
use Zco\Bundle\CoreBundle\Form\Type\ContactType;

/**
 * Contrôleur gérant des pages d'information statiques.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class StaticController extends Controller
{
    /**
     * Présentation du site, ses objectifs, son histoire.
     *
     * @Route(name="zco_about_index", path="/apropos")
     */
    public function indexAction()
    {
        fil_ariane(['À propos des zCorrecteurs']);
        \Page::$description = 'Apprenez-en plus sur le site et son histoire.';

        return $this->render('ZcoCoreBundle:Static:about.html.php');
    }

    /**
     * Liste des bannières pouvant être réutilisées pour nous soutenir.
     *
     * @Route(name="zco_about_banners", path="/apropos/bannieres")
     */
    public function bannersAction()
    {
        fil_ariane(['Aidez-nous à promouvoir le site']);
        \Page::$description = 'Découvrez une série de bannières et images que nous mettons à votre '
            . 'disposition si vous souhaitez faire la promotion du site.';

        return $this->render('ZcoCoreBundle:Static:banners.html.php', array(
            'bannieres' => array(
                'ext/signature.png' => array('dimensions' => '460x52'),
                'ext/banner.png' => array('dimensions' => '468x60'),
                'ext/banner2.png' => array('dimensions' => '468x60'),
                'ext/banner.gif' => array('dimensions' => '468x60'),
                'ext/banner2.gif' => array('dimensions' => '468x60'),
                'ext/userbar.png' => array('dimensions' => '350x19'),
                'ext/box.gif' => array('dimensions' => '300x250'),
            ),
        ));
    }

    /**
     * Liste des membres de l'équipe et des anciens.
     *
     * @Route(name="zco_about_team", path="/apropos/equipe")
     */
    public function teamAction()
    {
        fil_ariane(['Notre équipe']);
        \Page::$description = 'Ceux qui font vivre le site jour après jour, en '
            . 'corrigeant vos écrits, nourissant le contenu ou maintenant le site '
            . 'en état de marche.';

        return $this->render('ZcoCoreBundle:Static:team.html.php', array(
            'equipe' => \Doctrine_Core::getTable('Utilisateur')->listerEquipe(),
            'anciens' => \Doctrine_Core::getTable('Utilisateur')->listerAnciens(),
        ));
    }

    /**
     * Informations à propos de l'association Corrigraphie.
     *
     * @Route(name="zco_about_corrigraphie", path="/apropos/association-corrigraphie")
     */
    public function corrigraphieAction()
    {
        fil_ariane(['L\'association Corrigraphie']);
        \Page::$description = 'Venez découvrir l\'association qui se cache derrière '
            . 'le site, Corrigraphie, son rôle et ses activités.';

        return $this->render('ZcoCoreBundle:Static:corrigraphie.html.php');
    }

    /**
     * Informations à propos des logiciels libres utilisés et des codes que
     * nous avons placés sous licence open source.
     *
     * @Route(name="zco_about_opensource", path="/apropos/logiciel-libre")
     */
    public function openSourceAction()
    {
        fil_ariane(['Logiciel libre']);
        \Page::$description = 'zCorrecteurs.fr publie son code source sous licence libre.';

        return $this->render('ZcoCoreBundle:Static:openSource.html.php');
    }

    /**
     * Formulaire de contact de l'équipe administrative du site.
     *
     * @Route(name="zco_about_contact", path="/apropos/contact")
     * @param Request $request
     * @return Response
     */
    public function contactAction(Request $request)
    {
        fil_ariane(['Demande de contact']);

        $contact = new Contact(!empty($_GET['objet']) ? $_GET['objet'] : null);
        $form = $this->createForm(ContactType::class, $contact);

        if ($contact->raison) {
            \Page::$titre .= ' - ' . $contact->raison;
            \Page::$description = 'Si vous avez une question ou une demande '
                . 'ayant pour objet « ' . $contact->raison . ' », vous pouvez joindre '
                . 'l\'équipe du site zCorrecteurs.fr de manière personnalisée.';
        } else {
            \Page::$description = 'Si vous avez une question ou une demande particulière, '
                . 'vous pouvez joindre l\'équipe du site zCorrecteurs.fr de manière personnalisée.';
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contact->pseudo = verifier('connecte') ? $_SESSION['pseudo'] : null;
            $contact->id = verifier('connecte') ? $_SESSION['id'] : null;

            $message = $this->renderView('ZcoCoreBundle:Mail:contact.html.php', array(
                'contact' => $contact,
                'ip' => $request->getClientIp(),
            ));

            send_mail(
                $contact->courriel,
                $contact->nom ?: $contact->pseudo,
                '[Contact - ' . $contact->raison . '] ' . $contact->sujet,
                $message
            );

            return redirect(
                'L\'équipe administrative du site a bien été contactée. Elle vous répondra à l\'adresse mail indiquée.',
                $this->generateUrl('zco_about_contact')
            );
        }

        return $this->render(
            'ZcoCoreBundle:Static:contact.html.php',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route(name="zco_donate_index", path="/dons")
     * @return Response
     */
    public function donateAction()
    {
        fil_ariane(['Faire un don']);
        \Page::$description = 'Découvrez comment faire un don au site et consultez la liste de ceux qui nous ont déjà aidé !';

        return $this->render('ZcoCoreBundle:Donate:index.html.php');
    }

    /**
     * @Route(name="zco_donate_otherWays", path="/dons/cheque-ou-virement")
     * @return Response
     */
    public function donateOtherWaysAction()
    {
        fil_ariane([
            'Faire un don' => $this->generateUrl('zco_donate_index'),
            'Donner par chèque ou virement',
        ]);

        return $this->render('ZcoCoreBundle:Donate:otherWays.html.php');
    }

    /**
     * @Route(name="zco_donate_fiscalDeduction", path="/dons/deduction-fiscale")
     * @return Response
     */
    public function donateFiscalDeductionAction()
    {
        fil_ariane([
            'Faire un don' => $this->generateUrl('zco_donate_index'),
            'Déduction fiscale',
        ]);

        return $this->render('ZcoCoreBundle:Donate:fiscalDeduction.html.php');
    }

    /**
     * @Route(name="zco_donate_thanks", path="/dons/merci")
     * @return Response
     */
    public function donateThanksAction()
    {
        fil_ariane([
            'Faire un don' => $this->generateUrl('zco_donate_index'),
            'Merci pour votre soutien',
        ]);

        return $this->render('ZcoCoreBundle:Donate:thanks.html.php');
    }

    /**
     * @Route(name="zco_legal_mentions", path="/legal/mentions")
     * @return Response
     */
    public function mentionsAction()
    {
        fil_ariane(['Mentions légales']);

        return $this->render('ZcoCoreBundle:Static:mentions.html.php');
    }

    /**
     * @Route(name="zco_legal_privacy", path="/legal/confidentialite")
     * @return Response
     */
    public function privacyAction()
    {
        fil_ariane(['Politique de confidentialité']);

        return $this->render('ZcoCoreBundle:Static:privacy.html.php');
    }

    /**
     * @Route(name="zco_legal_rules", path="/legal/reglement")
     * @return Response
     */
    public function rulesAction()
    {
        fil_ariane(['Règlement']);

        return $this->render('ZcoCoreBundle:Static:rules.html.php');
    }

    /**
     * @Route(path="/_erreur/{code}", requirements={"code": "\d+"})
     * @return Response
     */
    public function errorAction($code)
    {
        return $this->render('TwigBundle:Exception:error' . $code . '.html.twig');
    }
}