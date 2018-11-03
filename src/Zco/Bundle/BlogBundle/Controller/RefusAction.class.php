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

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Zco\Bundle\BlogBundle\Domain\BlogDAO;

/**
 * Contrôleur gérant l'affichage des billets refusés.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class RefusAction extends BlogActions
{
	public function execute()
	{
        if (!verifier('blog_voir_refus')) {
            throw new AccessDeniedHttpException();
        }
		Page::$titre = 'Liste des billets refusés';

		list($ListerBillets, $Auteurs) = BlogDAO::ListerBillets(array(
			'etat' => BLOG_REFUSE,
			'lecteurs' => false
		));

		return render_to_response(array(
			'ListerBillets' => $ListerBillets,
			'Auteurs' => $Auteurs
		));
	}
}
