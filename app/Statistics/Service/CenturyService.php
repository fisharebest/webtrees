<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Statistics\Service;

use Fisharebest\Webtrees\I18N;

/**
 * Functions for managing centuries.
 */
class CenturyService
{
    /**
     * Century name, English => 21st, Polish => XXI, etc.
     *
     * @param int $century
     *
     * @return string
     */
    public function centuryName(int $century): string
    {
        if ($century < 0) {
            return I18N::translate('%s BCE', $this->centuryName(-$century));
        }

        // The current chart engine (Google charts) can't handle <sup></sup> markup
        switch ($century) {
            case 21:
                return strip_tags(I18N::translateContext('CENTURY', '21st'));
            case 20:
                return strip_tags(I18N::translateContext('CENTURY', '20th'));
            case 19:
                return strip_tags(I18N::translateContext('CENTURY', '19th'));
            case 18:
                return strip_tags(I18N::translateContext('CENTURY', '18th'));
            case 17:
                return strip_tags(I18N::translateContext('CENTURY', '17th'));
            case 16:
                return strip_tags(I18N::translateContext('CENTURY', '16th'));
            case 15:
                return strip_tags(I18N::translateContext('CENTURY', '15th'));
            case 14:
                return strip_tags(I18N::translateContext('CENTURY', '14th'));
            case 13:
                return strip_tags(I18N::translateContext('CENTURY', '13th'));
            case 12:
                return strip_tags(I18N::translateContext('CENTURY', '12th'));
            case 11:
                return strip_tags(I18N::translateContext('CENTURY', '11th'));
            case 10:
                return strip_tags(I18N::translateContext('CENTURY', '10th'));
            case 9:
                return strip_tags(I18N::translateContext('CENTURY', '9th'));
            case 8:
                return strip_tags(I18N::translateContext('CENTURY', '8th'));
            case 7:
                return strip_tags(I18N::translateContext('CENTURY', '7th'));
            case 6:
                return strip_tags(I18N::translateContext('CENTURY', '6th'));
            case 5:
                return strip_tags(I18N::translateContext('CENTURY', '5th'));
            case 4:
                return strip_tags(I18N::translateContext('CENTURY', '4th'));
            case 3:
                return strip_tags(I18N::translateContext('CENTURY', '3rd'));
            case 2:
                return strip_tags(I18N::translateContext('CENTURY', '2nd'));
            case 1:
                return strip_tags(I18N::translateContext('CENTURY', '1st'));
            default:
                return ($century - 1) . '01-' . $century . '00';
        }
    }
}
