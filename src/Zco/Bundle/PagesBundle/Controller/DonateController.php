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
 * Controller for donation pages.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class DonateController extends Controller
{
	public function indexAction()
	{
        \Page::$titre = 'Faire un don';
        \Page::$description = 'Découvrez comment faire un don au site et consultez la liste de ceux qui nous ont déjà aidé !';

        return render_to_response('ZcoPagesBundle:Donate:index.html.php');
	}

    public function otherWaysAction()
    {
        \Page::$titre = 'Faire un don par chèque ou virement';

        return render_to_response('ZcoPagesBundle:Donate:otherWays.html.php');
    }

    public function fiscalDeductionAction()
    {
        \Page::$titre = 'Déduction fiscale des dons';

        return render_to_response('ZcoPagesBundle:Donate:fiscalDeduction.html.php');
    }

    public function thanksAction()
    {
        \Page::$titre = 'Merci votre soutien';

        return render_to_response('ZcoPagesBundle:Donate:thanks.html.php');
    }
}