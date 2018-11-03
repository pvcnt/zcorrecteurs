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

namespace Zco\Bundle\CoreBundle\Parser;

interface ParserFeature
{
    /**
     * Hook appelé en premier permettant de manipuler le texte sous
     * sa forme de chaîne de caractères au tout début du processus.
     *
     * @param string $content
     * @param array $options
     * @return string
     */
    public function preProcessText(string $content, array $options): string;

    /**
     * Hook appelé lors de la transformation de la chaîne de caractères
     * en code XML analysable par DOM.
     *
     * @param string $content
     * @param array $options
     * @return string
     */
    public function prepareXml(string $content, array $options): string;

    /**
     * Hook principal permettant de manipuler l'arbre DOM et de procéder
     * au parsage complet du document.
     *
     * @param \DOMDocument $doc
     * @param array $options
     * @return \DOMDocument
     */
    public function processDom(\DOMDocument $doc, array $options): \DOMDocument;

    /**
     * Hook appelé en dernier permettant de manipuler le texte sous
     * sa forme de chaîne de caractères en toute fin du processus.
     *
     * @param string $content
     * @param array $options
     * @return string
     */
    public function postProcessText(string $content, array $options): string;
}