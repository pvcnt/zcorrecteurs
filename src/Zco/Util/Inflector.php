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

namespace Zco\Util;

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Cette classe permet de convertir des noms avec des underscores en leur
 * version en CamelCase, et vice-versa.
 *
 * @author Fabien Potencier <fabien.potencier@symfony-project.com>
 */
class Inflector
{
    /**
     * Returns a camelized string from a lower case and underscored string by replaceing slash with
     * double-colon and upper-casing each letter preceded by an underscore.
     * @param  string $lower_case_and_underscored_word String to camelize.
     * @return string Camelized string.
     */
    public static function camelize($lower_case_and_underscored_word)
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $lower_case_and_underscored_word)));
    }

    /**
     * Returns an underscore-syntaxed version or the CamelCased string.
     * @param  string $camel_cased_word String to underscore.
     * @return string Underscored string.
     */
    public static function underscore($camel_cased_word)
    {
        $tmp = $camel_cased_word;
        $tmp = str_replace('::', '/', $tmp);
        $tmp = self::pregtr($tmp, array('/([A-Z]+)([A-Z][a-z])/' => '\\1_\\2', '/([a-z\d])([A-Z])/' => '\\1_\\2'));

        return strtolower($tmp);
    }

    /**
     * Returns classname::module with classname:: stripped off.
     * @param  string $class_name_in_module Classname and module pair.
     * @return string Module name.
     */
    public static function demodulize($class_name_in_module)
    {
        return preg_replace('/^.*::/', '', $class_name_in_module);
    }

    /**
     * Returns classname in underscored form, with "_id" tacked on at the end.
     * This is for use in dealing with foreign keys in the database.
     * @param string $class_name Class name.
     * @param bool $separate_with_underscore Separate with underscore.
     * @return string Foreign key
     */
    public static function foreign_key($class_name, $separate_with_underscore = true)
    {
        return self::underscore(self::demodulize($class_name)) . ($separate_with_underscore ? '_id' : 'id');
    }

    /**
     * Returns corresponding table name for given classname.
     *
     * @param  string $class_name Name of class to get database table name for.
     * @return string Name of the databse table for given class.
     */
    public static function tableize($class_name)
    {
        return self::underscore($class_name);
    }

    /**
     * Returns model class name for given database table.
     * @param  string $table_name Table name.
     * @return string Classified table name.
     */
    public static function classify($table_name)
    {
        return self::camelize($table_name);
    }

    /**
     * Returns a human-readable string from a lower case and underscored word by replacing underscores
     * with a space, and by upper-casing the initial characters.
     *
     * @param  string $lower_case_and_underscored_word String to make more readable.
     * @return string Human-readable string.
     */
    public static function humanize($lower_case_and_underscored_word)
    {
        if (substr($lower_case_and_underscored_word, -3) === '_id') {
            $lower_case_and_underscored_word = substr($lower_case_and_underscored_word, 0, -3);
        }

        return ucfirst(str_replace('_', ' ', $lower_case_and_underscored_word));
    }

    /**
     * Returns subject replaced with regular expression matches.
     *
     * @author     Fabien Potencier, Sean Kerr
     * @param mixed $search subject to search
     * @param array $replacePairs array of search => replace pairs
     * @return string
     */
    private static function pregtr($search, $replacePairs)
    {
        return preg_replace(array_keys($replacePairs), array_values($replacePairs), $search);
    }
}
