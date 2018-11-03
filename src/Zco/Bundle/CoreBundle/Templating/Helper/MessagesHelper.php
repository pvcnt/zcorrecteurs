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

namespace Zco\Bundle\CoreBundle\Templating\Helper;

use Zco\Bundle\CoreBundle\Parser\ParserInterface;
use Zco\Bundle\VitesseBundle\Javelin\ResourceManager;
use Symfony\Component\Templating\Helper\Helper;

/**
 * Ensemble de fonctions aidant à l'affichage des messages.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class MessagesHelper extends Helper
{
    private $parser;
    private $resourceManager;
	
	/**
	 * Constructeur.
	 *
	 * @param ParserInterface $parser
     * @param ResourceManager $resourceManager
	 */
	public function __construct(ParserInterface $parser, ResourceManager $resourceManager)
	{
	    $this->parser = $parser;
	    $this->resourceManager = $resourceManager;
    }

	/**
	 * Retourne un pseudo coloré.
	 *
	 * @param  string $u Tableau des informations utilisateur.
	 * @param  string $id Colonne contenant l'id de l'utilisateur.
	 * @param  string $psd Colonne contenant le pseudo de l'utilisateur.
	 * @param  string $col Colonne contenant le couleur du groupe.
	 * @return string
	 */
	public function colorerPseudo($u,
                                      $id = 'utilisateur_id',
                                      $psd = 'utilisateur_pseudo',
                                      $col = 'groupe_class')
	{
		return '<a href="/membres/profil-'.$u[$id]
		      .'-'.rewrite($u[$psd]).'.html" style="color: '.htmlspecialchars($u[$col]).'">'
		      .htmlspecialchars($u[$psd]).'</a>';
	}
	
	/**
	 * Retourne un pseudo coloré (version Doctrine).
	 *
	 * @param  string $u L'utilisateur
	 * @return string
	 */
	public function pseudo($u)
	{
		return '<a href="/membres/profil-'.$u['id']
		      .'-'.rewrite($u['pseudo']).'.html" style="color: '.htmlspecialchars($u['Groupe']['class']).'">'
		      .htmlspecialchars($u['pseudo']).'</a>';
	}

	/**
	 * Retourne un avatar prêt à l'affichage.
	 *
	 * @param  string $u  Tableau des informations utilisateur.
	 * @param  string $av Colonne contenant l'avatar de l'utilisateur.
	 * @return string
	 */
	public function afficherAvatar($u, $av = 'utilisateur_avatar')
	{
		return empty($u[$av]) ? null :
			'<img src="/uploads/avatars/'.htmlspecialchars($u[$av]).'" '
			.'alt="Avatar" class="avatar" />';
	}
	
	/**
	 * Retourne un avatar prêt à l'affichage (version Doctrine).
	 *
	 * @param  string $u L'utilisateur
	 * @return string
	 */
	public function avatar($u)
	{
		return !strlen($u['avatar']) ? null :
			'<img src="/uploads/avatars/'.htmlspecialchars($u['avatar']).'" '
			.'alt="Avatar" class="avatar" />';
	}

	/**
	 * Logo du groupe, ou nom si aucun.
	 *
	 * @param  string $u Tableau des informations utilisateur.
	 * @param  string $gn Colonne contenant l'avatar de l'utilisateur.
	 * @param  string $gl Colonne contenant l'url du logo du groupe.
	 * @param  string $sx Colonne contenant le sexe de l'utilisateur.
	 * @return string
	 */
	public function afficherGroupe($u, $gn = 'groupe_nom', $gl = 'groupe_logo', $sx = 'utilisateur_sexe')
	{
		if(isset($u[$sx]) && $u[$sx] == SEXE_FEMININ)
			$gl .= '_feminin';

		return empty($u[$gl]) ? htmlspecialchars($u[$gn]) :
			'<img src="'.$u[$gl].'" '
			.'alt="Groupe : '.htmlspecialchars($u[$gn]).'"/>';
	}
	
	/**
	 * Logo du groupe, ou nom si aucun (version Doctrine).
	 *
	 * @param  string $u L'utilisateur
	 * @return string
	 */
	public function userGroup($u)
	{
		$col = 'logo'.($u->getGender() == SEXE_FEMININ ? '_feminin' : '');

		return empty($u->Groupe[$col]) ? htmlspecialchars($u->Groupe['nom']) :
			'<img src="'.htmlspecialchars($u->Groupe[$col]).'" '
			.'alt="Groupe : '.htmlspecialchars($u->Groupe['nom']).'"/>';
	}
	
	/**
	 * Parse un message écrit dans notre zCode pour l'affichage.
	 *
	 * @param  string $texte Le texte à parser
	 * @param  string|false $prefix Un préfixe à utiliser devant les ancres
	 * @return string Code HTML prêt à l'affichage
	 */
	public function parse($texte, $prefix = false)
	{
	    $this->resourceManager->requireResource(
	        '@ZcoCoreBundle/Resources/public/css/zcode.css'
	    );
	    $options = is_array($prefix) ? $prefix : array('core.anchor_prefix' => $prefix);
	    
		return $this->parser->parse($texte, $options);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'messages';
	}
}
