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

use Zco\Bundle\BlogBundle\Domain\BlogDAO;
use Zco\Bundle\CategoriesBundle\Domain\CategoryDAO;

/**
 * Contrôleur se chargeant de divers paramétrages communs à toutes les pages
 * concernant un billet particulier. Pour être opérationnel, l'id du billet
 * doit avoir été passé dans $_GET['id'].
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */

//--- On récupère les infos sur le billet et les auteurs ---
if(!isset($InfosBillet) || !isset($Auteurs))
{
	$Auteurs = BlogDAO::InfosBillet($_GET['id']);
	if(empty($Auteurs))
		return redirect('Ce billet n\'existe pas.', '/blog/', MSG_ERROR);
	$InfosBillet = $Auteurs[0];
}
$InfosCategorie = CategoryDAO::InfosCategorie($InfosBillet['blog_id_categorie']);

//--- Définition du statut par rapport au billet ---
$autorise = false;
$createur = false;
$redacteur = false;
foreach($Auteurs as $a)
{
	if($a['utilisateur_id'] == $_SESSION['id'])
	{
		$autorise = true;
		if($a['auteur_statut'] == 3)
			$createur = true;
		if($a['auteur_statut'] > 1)
			$redacteur = true;
	}
}

//--- On regarde si le visiteur peut éditer le billet ---
$verifier_editer = false;
if(
	(
		in_array($InfosBillet['blog_etat'], array(BLOG_BROUILLON, BLOG_REFUSE))
		&&
		($redacteur == true || verifier('blog_editer_brouillons'))
	)
	||
	($InfosBillet['blog_etat'] == BLOG_PREPARATION && verifier('blog_editer_preparation'))
	||
	($InfosBillet['blog_etat'] == BLOG_VALIDE && verifier('blog_editer_valide'))
)
	$verifier_editer = true;

//--- On regarde si le visiteur peut voir le billet ---
$verifier_voir = false;
if(
	//-> Billet en ligne
	($InfosBillet['blog_etat'] == BLOG_VALIDE &&  strtotime($InfosBillet['blog_date_publication']) <= time())
	||
	//-> Billet programmé
	($InfosBillet['blog_etat'] == BLOG_VALIDE && strtotime($InfosBillet['blog_date_publication']) >= time() && verifier('blog_valider', $InfosBillet['blog_id_categorie']))
	||
	//-> Billet proposé ou en préparation par l'équipe
	(in_array($InfosBillet['blog_etat'], array(BLOG_PROPOSE, BLOG_PREPARATION)) && verifier('blog_voir_billets_proposes'))
	||
	//-> Billet en rédaction ou bien refusé
	(in_array($InfosBillet['blog_etat'], array(BLOG_BROUILLON, BLOG_REFUSE)) && verifier('blog_voir_billets_redaction'))
	||
	//-> Ou bien si le membre est un rédacteur, il peut toujours voir le billet.
	$autorise == true
)
	$verifier_voir = true;

//--- On regarde si le visiteur peut voir l'admin du billet ---
$verifier_admin_billet = false;
if($autorise == true || $verifier_editer || ($verifier_voir && $InfosBillet['blog_etat'] != BLOG_VALIDE))
	$verifier_admin_billet = true;

//--- On regarde si le visiteur peut dévalider le billet ---
$verifier_devalider = false;
if(
	verifier('blog_devalider')
	&&
	in_array($InfosBillet['blog_etat'], array(BLOG_VALIDE, BLOG_PREPARATION))
)
	$verifier_devalider = true;

//--- On regarde si le visiteur peut supprimer le billet ---
$verifier_supprimer = false;
if(
	verifier('blog_supprimer') ||
	(
		in_array($InfosBillet['blog_etat'], array(BLOG_BROUILLON, BLOG_REFUSE))
		&&
		$createur == true
	)
)
	$verifier_supprimer = true;

//--- Modification des balises meta ---
Page::$titre = htmlspecialchars($InfosBillet['version_titre']);
Page::$description = htmlspecialchars(strip_tags($InfosBillet['version_intro']));