<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012 Corrigraphie
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

namespace Zco\Bundle\UserBundle\Domain;

final class EmailAddress
{
    public static function isValid($value)
    {
        return preg_match('`^[a-z0-9+._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$`', $value);
    }

    public static function isAllowed($value)
    {
        static $banList = [
            '^binkmail\.',
            '^brefemail\.',
            '^bugmenot\.',
            '^cool\.fr\.nf',
            '^courriel\.fr\.nf$',
            '^dodgit\.',
            '^ephemail\.',
            '^haltospam\.',
            '^jetable\.',
            '^kasmail\.',
            '^link2mail\.',
            '^mailinator\.',
            '^mailinator2\.',
            '^mailincubator\.',
            '^mega\.zik\.dj$',
            '^moncourrier\.fr\.nf$',
            '^monemail\.fr\.nf$',
            '^monmail\.fr\.nf$',
            '^mytrashmail\.',
            '^nice-4u\.',
            '^nomail\.xl\.cx$',
            '^nospam\.ze\.tc$',
            '^pookmail\.',
            '^safetymail\.info$',
            '^sogetthis\.',
            '^spambox\.',
            '^spamgourmet\.',
            '^spamherelots\.',
            '^spamsphere\.',
            '^speed\.1s\.fr$',
            '^suremail\.info$',
            '^tempomail\.',
            '^thisisnotmyrealemail\.',
            '^tradermail\.',
            '^trash2009\.',
            '^trashmail\.',
            '^yopmail\.',
            '^youpymail\.',
            '^zippymail\.',
        ];

        $pos = strpos($value, '@');
        if ($pos === -1) {
            return false;
        }

        $domain = substr($value, $pos + 1);
        foreach ($banList as $pattern) {
            if (preg_match('/' . $pattern . '/', $domain)) {
                return false;
            }
        }

        return true;
    }
}