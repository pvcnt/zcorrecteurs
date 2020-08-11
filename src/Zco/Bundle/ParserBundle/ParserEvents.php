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

namespace Zco\Bundle\ParserBundle;

/**
 * Événéments utilisés par le parseur.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
final class ParserEvents
{
    /**
     * Événement appelé en premier permettant de manipuler le texte sous 
     * sa forme de chaîne de caractères au tout début du processus.
     *
     * @var string
     */
    const PRE_PROCESS_TEXT = 'zco_parser.pre_process_text';
    
    /**
     * Événement appelé lors de la transformation de la chaîne de caractères 
     * en code XML analysable par DOM.
     *
     * @var string
     */
    const PREPARE_XML = 'zco_parser.prepare_xml';
    
    /**
     * Événement principal permettant de manipuler l'arbre DOM et de procéder 
     * au parsage complet du document.
     *
     * @var string
     */
    const PROCESS_DOM = 'zco_parser.process_dom';
    
    /**
     * Événement appelé en dernier permettant de manipuler le texte sous 
     * sa forme de chaîne de caractères en toute fin du processus.
     *
     * @var string
     */
    const POST_PROCESS_TEXT = 'zco_parser.post_process_text';
}