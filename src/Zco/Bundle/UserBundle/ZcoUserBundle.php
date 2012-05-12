<?php

/**
 * Copyright 2012 Corrigraphie
 * 
 * This file is part of zCorrecteurs.fr.
 *
 * zCorrecteurs.fr is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * zCorrecteurs.fr is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with zCorrecteurs.fr. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Zco\Bundle\UserBundle;

use Zco\Component\HttpKernel\Bundle\AbstractBundle;

/**
 * Fournit toutes les fonctions liées à la gestion et l'administration des 
 * membres ainsi qu'à la gestion des sessions (connexion automatique, bannissement, 
 * mise à jour des connectés, etc.).
 * 
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class ZcoUserBundle extends AbstractBundle
{
	public function preload()
	{
		//Enregistrement du compteur de tâches admin.
		$this->container->get('zco_admin.manager')->register('changementsPseudo', 'membres_valider_ch_pseudos');
		
		//Inclusion du modèle de base.
		include_once(__DIR__.'/modeles/utilisateurs.php');
	}
}