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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Contrôleur gérant la suppression d'un dossier de MP.
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class SupprimerDossierAction extends Controller
{
    public function execute()
    {
        if (!verifier('connecte')) {
            throw new AccessDeniedHttpException();
        }
        if (isset($_POST['annuler'])) {
            return new RedirectResponse('index.html');
        }
        include(BASEPATH . '/src/Zco/Bundle/MpBundle/modeles/dossiers.php');

        if (!empty($_GET['id']) AND is_numeric($_GET['id'])) {
            $DossierExiste = DossierExiste();
            if ($DossierExiste) {
                if (!isset($_POST['confirmation'])) {
                    //Inclusion de la vue
                    fil_ariane('Supprimer un dossier');
                    Page::$titre = $DossierExiste['mp_dossier_titre'] . ' - Suppression du dossier - ' . Page::$titre;

                    return $this->render('ZcoMpBundle::supprimerDossier.html.php', array(
                        'DossierExiste' => $DossierExiste,
                    ));
                } else {
                    SupprimerDossier();
                    return redirect('Le dossier a bien été supprimé.', 'index.html');
                }
            } else {
                throw new NotFoundHttpException();
            }
        } else {
            throw new NotFoundHttpException();
        }
    }
}
