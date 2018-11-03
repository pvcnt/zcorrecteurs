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

namespace Zco\Bundle\PagesBundle\Controller;

use Zco\Bundle\PagesBundle\Form\Type\ContactType;
use Zco\Bundle\PagesBundle\Entity\Contact;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Contrôleur gérant des pages d'information à propos du site et de 
 * l'association.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class AboutController extends Controller
{
	/**
	 * Présentation du site, ses objectifs, son histoire.
	 */
	public function indexAction()
	{
		\Page::$titre = 'À propos des zCorrecteurs';
		\Page::$description = 'Apprenez-en plus sur le site et son histoire et '
			.'découvrez quelques chiffres marquants.';
		fil_ariane();
		
		return render_to_response('ZcoPagesBundle:About:index.html.php');
	}
	
	/**
	 * Liste des bannières pouvant être réutilisées pour nous soutenir.
	 */
	public function bannersAction()
	{
		\Page::$titre = 'Aider et promouvoir le site';
		\Page::$description = 'Découvrez une série de bannières et images que nous mettons à votre disposition si vous souhaitez faire la promotion du site.';
		
		return render_to_response('ZcoPagesBundle:About:banners.html.php', array(
			'bannieres' => array(
				'ext/signature.png' => array('dimensions' => '460x52'),
				'ext/banner.png'	=> array('dimensions' => '468x60'),
				'ext/banner2.png'   => array('dimensions' => '468x60'),
				'ext/banner.gif'	=> array('dimensions' => '468x60'),
				'ext/banner2.gif'   => array('dimensions' => '468x60'),
				'ext/userbar.png'   => array('dimensions' => '350x19'),
				'ext/box.gif'	   => array('dimensions' => '300x250'),
			),
		));
	}
	
	/**
	 * Liste des membres de l'équipe et des anciens.
	 */
	public function teamAction()
	{
		\Page::$titre = 'Notre équipe';
		\Page::$description = 'Ceux qui font vivre le site jour après jour, en '
			.'corrigeant vos écrits, nourissant le contenu ou maintenant le site '
			.'en état de marche.';
		
		return render_to_response('ZcoPagesBundle:About:team.html.php', array(
			'equipe' => \Doctrine_Core::getTable('Utilisateur')->listerEquipe(),
			'anciens' => \Doctrine_Core::getTable('Utilisateur')->listerAnciens(),
		));
	}
	
	/**
	 * Informations à propos de l'association Corrigraphie.
	 */
	public function corrigraphieAction()
	{
		\Page::$titre = 'L\'association Corrigraphie';
		\Page::$description = 'Venez découvrir l\'association qui se cache derrière '
			.'le site, Corrigraphie, son rôle et ses activités.';
		
		return render_to_response('ZcoPagesBundle:About:corrigraphie.html.php');
	}

	/**
	 * Informations à propos des logiciels libres utilisés et des codes que
	 * nous avons placés sous licence open source.
	 */
	public function openSourceAction()
	{
		\Page::$titre = 'Logiciel libre';
		\Page::$description = 'zCorrecteurs.fr utilise de nombreux logiciels '
			.'libres et en retour met à disposition son code source sous licence AGPL.';
		
		return render_to_response('ZcoPagesBundle:About:openSource.html.php');
	}
	
	/**
	 * Formulaire de contact de l'équipe administrative du site.
	 *
	 * @param Request $request
	 */
	public function contactAction(Request $request)
	{
		\Page::$titre = 'Demande de contact';
		
		$contact = new Contact(!empty($_GET['objet']) ? $_GET['objet'] : null);
		$form = $this->createForm(ContactType::class, $contact);
		
		if ($contact->raison)
		{
			\Page::$titre .= ' - '.$contact->raison;
			\Page::$description = 'Si vous avez une question ou une demande '
				.'ayant pour objet « '.$contact->raison.' », vous pouvez joindre '
				.'l\'équipe du site zCorrecteurs.fr de manière personnalisée.';
		}
		else
		{
			\Page::$description = 'Si vous avez une question ou une demande particulière, '
				.'vous pouvez joindre l\'équipe du site zCorrecteurs.fr de manière personnalisée.';
		}

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $contact->pseudo = verifier('connecte') ? $_SESSION['pseudo'] : null;
            $contact->id = verifier('connecte') ? $_SESSION['id'] : null;

            $message = $this->renderView('ZcoPagesBundle:Mail:contact.html.php', array(
                'contact'	=> $contact,
                'ip'		=> $request->getClientIp(),
            ));

            send_mail(
                $contact->courriel,
                $contact->nom ?: $contact->pseudo,
                '[Contact - '.$contact->raison.'] '.$contact->sujet,
                $message
            );

            return redirect(
                'L\'équipe administrative du site a bien été contactée. Elle vous répondra à l\'adresse mail indiquée.',
                $this->generateUrl('zco_about_contact')
            );
        }
		
		return render_to_response(
			'ZcoPagesBundle:About:contact.html.php',
			array('form' => $form->createView())
		);
	}
}