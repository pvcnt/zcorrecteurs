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

/*
 * Copyright 2012 Facebook, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Zco\Bundle\VitesseBundle\Annotation;

/**
 * Parse a docblock comment from source code into raw text documentation and
 * metadata (like "@author" and "@return").
 */
class DocblockParser
{
    public function extractDocblocks($text)
    {
        $blocks = array();

        $matches = null;
        $match = preg_match_all(
            '@(/\*\*.*?\*/)@s',
            $text,
            $matches,
            PREG_OFFSET_CAPTURE | PREG_PATTERN_ORDER);

        if (!$match)
        {
            return $blocks;
        }

        // Build a map of character offset -> line number.
        $map = array();
        $lines = explode("\n", $text);
        $num = 1;
        foreach ($lines as $line)
        {
            $len = strlen($line) + 1;
            for ($jj = 0; $jj < $len; $jj++)
            {
                $map[] = $num;
            }
            ++$num;
        }

        foreach ($matches[0] as $match)
        {
            list($data, $offset) = $match;
            $blocks[] = array($data, $map[$offset]);
        }

        return $blocks;
    }

    public function parse($docblock)
    {
        // Strip off comments.
        $docblock = trim($docblock);
        $docblock = preg_replace('@^/\*\*@', '', $docblock);
        $docblock = preg_replace('@\*/$@', '', $docblock);
        $docblock = preg_replace('@^\s*\*@m', '', $docblock);

        // Normalize multi-line @specials.
        $lines = explode("\n", $docblock);
        $last = false;
        foreach ($lines as $k => $line)
        {
            if (preg_match('/^\s?@\w/i', $line))
            {
                $last = $k;
            }
            else if (preg_match('/^\s*$/', $line))
            {
                $last = false;
            }
            else if ($last !== false)
            {
                $lines[$last] = rtrim($lines[$last]).' '.trim($line);
                unset($lines[$k]);
            }
        }
        
        $docblock = implode("\n", $lines);

        $special = array();

        // Parse @specials.
        $matches = null;
        $have_specials = preg_match_all(
            '/^\s?@([\w-]+)[ \t]*([^\n]*)/m',
            $docblock,
            $matches,
            PREG_SET_ORDER);

        if ($have_specials)
        {
            $docblock = preg_replace(
                '/^\s?@([\w-]+)[ \t]*([^\n]*)?\n*/m',
                '',
                $docblock);
            foreach ($matches as $match)
            {
                list($_, $type, $data) = $match;
                $data = trim($data);
                if (isset($special[$type]))
                {
                    $special[$type] = $special[$type]."\n".$data;
                }
                else 
                {
                    $special[$type] = $data;
                }
            }
        }

        // For flags like "@stable" which don't have any string data, set the value
        // to true.
        foreach ($special as $type => $data)
        {
            if (!strlen(trim($data)))
            {
                $special[$type] = true;
            }
        }

        $docblock = str_replace("\t", '    ', $docblock);

        // Smush the whole docblock to the left edge.
        $min_indent = 80;
        $indent = 0;
        foreach (array_filter(explode("\n", $docblock)) as $line)
        {
            for ($ii = 0; $ii < strlen($line); $ii++)
            {
                if ($line[$ii] != ' ')
                {
                    break;
                }
                $indent++;
            }
            $min_indent = min($indent, $min_indent);
        }

        $docblock = preg_replace(
            '/^'.str_repeat(' ', $min_indent).'/m',
            '',
            $docblock);
        $docblock = rtrim($docblock);
        // Trim any empty lines off the front, but leave the indent level if there
        // is one.
        $docblock = preg_replace('/^\s*\n/', '', $docblock);

        return array($docblock, $special);
    }
}
