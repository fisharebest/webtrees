<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Functions;

use Fisharebest\Webtrees\I18N;
use LogicException;

use function trigger_error;

use const E_USER_DEPRECATED;

/**
 * Class FunctionsDate - common functions
 *
 * @deprecated since 2.0.4.  Will be removed in 2.1.0
 */
class FunctionsDate
{
    /**
     * Convert a GEDCOM age string to localized text.
     *
     * @param string $age_string
     *
     * @return string
     *
     * @deprecated since 2.0.4.  Will be removed in 2.1.0
     */
    public static function getAgeAtEvent(string $age_string): string
    {
        trigger_error('FunctionsDate::getAgeAtEvent() is deprecated. Use FunctionsPrint::formatGedcomAge() instead', E_USER_DEPRECATED);

        switch (strtoupper($age_string)) {
            case 'CHILD':
                return I18N::translate('Child');
            case 'INFANT':
                return I18N::translate('Infant');
            case 'STILLBORN':
                return I18N::translate('Stillborn');
            default:
                return (string) preg_replace_callback(
                    [
                        '/(\d+)([ymwd])/',
                    ],
                    static function (array $match): string {
                        $num = (int) $match[1];

                        switch ($match[2]) {
                            case 'y':
                                return I18N::plural('%s year', '%s years', $num, I18N::number($num));
                            case 'm':
                                return I18N::plural('%s month', '%s months', $num, I18N::number($num));
                            case 'w':
                                return I18N::plural('%s week', '%s weeks', $num, I18N::number($num));
                            case 'd':
                                return I18N::plural('%s day', '%s days', $num, I18N::number($num));
                            default:
                                throw new LogicException('Should never get here');
                        }
                    },
                    $age_string
                );
        }
    }
}
