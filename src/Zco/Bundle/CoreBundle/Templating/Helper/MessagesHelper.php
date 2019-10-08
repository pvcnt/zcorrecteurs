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

namespace Zco\Bundle\CoreBundle\Templating\Helper;

use Gaufrette\Extras\Resolvable\ResolvableFilesystem;
use Symfony\Component\Templating\Helper\Helper;
use Zco\Bundle\CoreBundle\Parser\ParserInterface;
use Zco\Bundle\CoreBundle\Javelin\ResourceManager;

/**
 * Ensemble de fonctions aidant à l'affichage des messages.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class MessagesHelper extends Helper
{
    private $parser;
    private $resourceManager;
    private $filesystem;

    /**
     * Constructeur.
     *
     * @param ParserInterface $parser
     * @param ResourceManager $resourceManager
     * @param ResolvableFilesystem $filesystem
     */
    public function __construct(ParserInterface $parser, ResourceManager $resourceManager, ResolvableFilesystem $filesystem)
    {
        $this->parser = $parser;
        $this->resourceManager = $resourceManager;
        $this->filesystem = $filesystem;
    }

    /**
     * Retourne un avatar prêt à l'affichage.
     *
     * @param  string $arr Tableau des informations utilisateur.
     * @param  string $key Colonne contenant l'avatar de l'utilisateur.
     * @return string
     */
    public function afficherAvatar($arr, $key = null)
    {
        return '<img src="' . $this->avatarUrl($arr, $key) . '" alt="Avatar" class="avatar" />';
    }

    public function avatarUrl($arr, $key = null)
    {
        if (is_array($arr)) {
            $key = $key ?? 'utilisateur_avatar';
            $filename = isset($arr[$key]) ? $arr[$key] : null;
        } else {
            $key = $key ?? 'avatar';
            $filename = isset($arr->$key) ? $arr->$key : null;
        }
        if ($filename) {
            return $this->filesystem->resolve('avatars/' . $filename);
        }

        return '/img/anonymous-80.png';
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
        if (isset($u[$sx]) && $u[$sx] == SEXE_FEMININ)
            $gl .= '_feminin';

        return empty($u[$gl]) ? htmlspecialchars($u[$gn]) :
            '<img src="' . $u[$gl] . '" alt="Groupe : ' . htmlspecialchars($u[$gn]) . '"/>';
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
        $this->resourceManager->requireResource('@ZcoCoreBundle/Resources/public/css/zcode.css');
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
