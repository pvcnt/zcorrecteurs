<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2020 Corrigraphie
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

namespace Zco\Bundle\CaptchaBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Zco\Bundle\CaptchaBundle\Captcha\Captcha;

/**
 * Génération et affichage
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class IndexController
{
    public function defaultAction()
    {
        $config = [
            'fond' => [
                'rouge' => 255,
                'vert' => 255,
                'bleu' => 255,
                'transparent' => true,
            ],
            'caracteres' => [
                'rouge' => 0,
                'vert' => 0,
                'bleu' => 0,
                'aleatoire' => true,        // Couleur choisie aléatoirement
                'luminosite' => 1,          // Luminosité des caractères de 1 (sombre) à 4 (clair)
                'transparence' => 10,       // Transparence des caractères de 0 (opaque) à 127 (invisible)
                'liste' => 'ABCDEFGHKLMNPRTWXYZ',
                'nombre' => 4,              // Nombre de caractères
                'espacement' => 20,         // Espacement des caractères
                'taille' => ['min' => 14, 'max' => 16],
                'polices' => ['luggerbu.ttf'],
                'anglemax' => 25,
            ],
            'brouillage' => [
                'flouGaussien' => false,    // Appliquer un flou gaussien
                'niveauxGris' => false,     // Image en noir et blanc (niveaux de gris)
                'bruit' => [
                    'pixels' => ['min' => 10, 'max' => 50],
                    'lignes' => ['min' => 1, 'max' => 5],
                    'cercles' => ['min' => 1, 'max' => 5],
                    'type' => 'dessous',     // Le bruit est-il par-dessus ou par-dessous les caractères ?
                    'epaisseur' => ['min' => 1, 'max' => 4],
                ],
            ],

            'format' => 'png',
            'intervalle' => 2,     // Temps d'attente entre deux générations d'image par le même client (s)
            'largeur' => 130,
            'hauteur' => 40,
            'cadre' => true,
        ];
        $captcha = new Captcha($config);
        $captcha->afficher();

        $response = new Response();
        $response->headers->set('Content-Type', 'image/png');
        return $response;
    }
}
