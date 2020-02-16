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

namespace Zco\Bundle\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BaseController extends Controller
{
    protected $_vars = array();
    
    public function getVars()
	{
		return $this->_vars;
	}

	protected function setRef($var, &$value)
	{
		$this->_vars[$var] = &$value;
	}

	public function __set($var, $value)
	{
		$this->_vars[$var] = $value;
	}

	public function __get($var)
	{
		return isset($this->_vars[$var]) ? $this->_vars[$var] : null;
	}

	public function __isset($var)
	{
		return isset($this->_vars[$var]);
	}

	public function __unset($var)
	{
		unset($this->_vars[$var]);
	}
	
	protected function initBillet()
	{
		//--- On récupère les infos sur le billet et les auteurs ---
		if(!isset($this->InfosBillet) || !isset($this->Auteurs))
		{
			$Auteurs = InfosBillet($_GET['id']);
			if(empty($Auteurs))
				return redirect(210, '/blog/', MSG_ERROR);
			$this->Auteurs = $Auteurs;
			$this->InfosBillet = $Auteurs[0];
		}
		$this->InfosCategorie = InfosCategorie($this->InfosBillet['blog_id_categorie']);

		//--- Définition du statut par rapport au billet ---
		$this->autorise = false;
		$this->createur = false;
		$this->redacteur = false;
		foreach($this->Auteurs as $a)
		{
			if($a['utilisateur_id'] == $_SESSION['id'])
			{
				$this->autorise = true;
				if($a['auteur_statut'] == 3)
					$this->createur = true;
				if($a['auteur_statut'] > 1)
					$this->redacteur = true;
			}
		}

		//--- On regarde si le visiteur peut éditer le billet ---
		$this->verifier_editer = false;
		if(
			(
				in_array($this->InfosBillet['blog_etat'], array(BLOG_BROUILLON, BLOG_REFUSE))
				&&
				($this->redacteur == true || verifier('blog_editer_brouillons'))
			)
			||
			($this->InfosBillet['blog_etat'] == BLOG_PREPARATION && verifier('blog_editer_preparation'))
			||
			($this->InfosBillet['blog_etat'] == BLOG_VALIDE && verifier('blog_editer_valide'))
		)
			$this->verifier_editer = true;

		//--- On regarde si le visiteur peut voir le billet ---
		$this->verifier_voir = false;
		if(
			//-> Billet en ligne
			($this->InfosBillet['blog_etat'] == BLOG_VALIDE &&  strtotime($this->InfosBillet['blog_date_publication']) <= time() && verifier('blog_voir', $this->InfosBillet['blog_id_categorie']))
			||
			//-> Billet programmé
			($this->InfosBillet['blog_etat'] == BLOG_VALIDE && strtotime($this->InfosBillet['blog_date_publication']) >= time() && verifier('blog_valider', $this->InfosBillet['blog_id_categorie']))
			||
			//-> Billet proposé ou en préparation par l'équipe
			(in_array($this->InfosBillet['blog_etat'], array(BLOG_PROPOSE, BLOG_PREPARATION)) && verifier('blog_voir_billets_proposes'))
			||
			//-> Billet en rédaction ou bien refusé
			(in_array($this->InfosBillet['blog_etat'], array(BLOG_BROUILLON, BLOG_REFUSE)) && verifier('blog_voir_billets_redaction'))
			||
			//-> Ou bien si le membre est un rédacteur, il peut toujours voir le billet.
			$this->autorise == true
		)
			$this->verifier_voir = true;

		//--- On regarde si le visiteur peut voir l'admin du billet ---
		$this->verifier_admin_billet = false;
		if($this->autorise == true || $this->verifier_editer || ($this->verifier_voir && $this->InfosBillet['blog_etat'] != BLOG_VALIDE))
			$this->verifier_admin_billet = true;

		//--- On regarde si le visiteur peut dévalider le billet ---
		$this->verifier_devalider = false;
		if(
			verifier('blog_devalider')
			&&
			in_array($this->InfosBillet['blog_etat'], array(BLOG_VALIDE, BLOG_PREPARATION))
		)
			$this->verifier_devalider = true;

		//--- On regarde si le visiteur peut supprimer le billet ---
		$this->verifier_supprimer = false;
		if(
			verifier('blog_supprimer') ||
			(
				in_array($this->InfosBillet['blog_etat'], array(BLOG_BROUILLON, BLOG_REFUSE))
				&&
				$this->createur == true
			)
		)
			$this->verifier_supprimer = true;

		//--- Modification des balises meta ---
		\Page::$titre = htmlspecialchars($this->InfosBillet['version_titre']);
        \Page::$description = htmlspecialchars(strip_tags($this->InfosBillet['version_intro']));

        return null;
	}
}
