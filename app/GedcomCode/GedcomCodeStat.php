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

namespace Fisharebest\Webtrees\GedcomCode;

use Fisharebest\Webtrees\I18N;
use InvalidArgumentException;

/**
 * Class GedcomCodeStat - Functions and logic for GEDCOM "STAT" codes
 */
class GedcomCodeStat
{
    /**
     * Get a list of status codes that can be used on a given LDS tag
     *
     * @param string $tag
     *
     * @return array<string>
     */
    public static function statusCodes(string $tag): array
    {
        switch ($tag) {
            case 'BAPL':
            case 'CONL':
                // LDS_BAPTISM_DATE_STATUS
                return [
                    'CHILD',
                    'COMPLETED',
                    'EXCLUDED',
                    'INFANT',
                    'PRE-1970',
                    'STILLBORN',
                    'SUBMITTED',
                    'UNCLEARED',
                ];
            case 'ENDL':
                // LDS_ENDOWMENT_DATE_STATUS
                return [
                    'CHILD',
                    'COMPLETED',
                    'EXCLUDED',
                    'INFANT',
                    'PRE-1970',
                    'STILLBORN',
                    'SUBMITTED',
                    'UNCLEARED',
                ];
            case 'SLGC':
                // LDS_CHILD_SEALING_DATE_STATUS
                return [
                    'BIC',
                    'COMPLETED',
                    'EXCLUDED',
                    'PRE-1970',
                    'STILLBORN',
                    'SUBMITTED',
                    'UNCLEARED',
                ];
            case 'SLGS':
                // LDS_SPOUSE_SEALING_DATE_STATUS
                return [
                    'CANCELED',
                    'COMPLETED',
                    'DNS',
                    'DNS/CAN',
                    'EXCLUDED',
                    'PRE-1970',
                    'SUBMITTED',
                    'UNCLEARED',
                ];
            default:
                throw new InvalidArgumentException('Internal error - bad argument to GedcomCodeStat::statusCodes("' . $tag . '")');
        }
    }

    /**
     * Get the localized name for a status code
     *
     * @param string $status_code
     *
     * @return string
     */
    public static function statusName(string $status_code): string
    {
        switch ($status_code) {
            case 'BIC':
                /* I18N: LDS sealing status; see http://en.wikipedia.org/wiki/Sealing_(Latter_Day_Saints) */
                return I18N::translate('Born in the covenant');
            case 'CANCELED':
                /* I18N: LDS sealing status; see http://en.wikipedia.org/wiki/Sealing_(Latter_Day_Saints) */
                return I18N::translate('Sealing canceled (divorce)');
            case 'CHILD':
                /* I18N: LDS sealing status; see http://en.wikipedia.org/wiki/Sealing_(Latter_Day_Saints) */
                return I18N::translate('Died as a child: exempt');
            case 'CLEARED':
                // This status appears in PhpGedView, but not in the GEDCOM 5.5.1 specification.
                /* I18N: LDS sealing status; see http://en.wikipedia.org/wiki/Sealing_(Latter_Day_Saints) */
                return I18N::translate('Cleared but not yet completed');
            case 'COMPLETED':
                /* I18N: LDS sealing status; see http://en.wikipedia.org/wiki/Sealing_(Latter_Day_Saints) */
                return I18N::translate('Completed; date unknown');
            case 'DNS':
                /* I18N: LDS sealing status; see http://en.wikipedia.org/wiki/Sealing_(Latter_Day_Saints) */
                return I18N::translate('Do not seal: unauthorized');
            case 'DNS/CAN':
                /* I18N: LDS sealing status; see http://en.wikipedia.org/wiki/Sealing_(Latter_Day_Saints) */
                return I18N::translate('Do not seal, previous sealing canceled');
            case 'EXCLUDED':
                /* I18N: LDS sealing status; see http://en.wikipedia.org/wiki/Sealing_(Latter_Day_Saints) */
                return I18N::translate('Excluded from this submission');
            case 'INFANT':
                /* I18N: LDS sealing status; see http://en.wikipedia.org/wiki/Sealing_(Latter_Day_Saints) */
                return I18N::translate('Died as an infant: exempt');
            case 'PRE-1970':
                /* I18N: LDS sealing status; see http://en.wikipedia.org/wiki/Sealing_(Latter_Day_Saints) */
                return I18N::translate('Completed before 1970; date not available');
            case 'STILLBORN':
                /* I18N: LDS sealing status; see http://en.wikipedia.org/wiki/Sealing_(Latter_Day_Saints) */
                return I18N::translate('Stillborn: exempt');
            case 'SUBMITTED':
                /* I18N: LDS sealing status; see http://en.wikipedia.org/wiki/Sealing_(Latter_Day_Saints) */
                return I18N::translate('Submitted but not yet cleared');
            case 'UNCLEARED':
                /* I18N: LDS sealing status; see http://en.wikipedia.org/wiki/Sealing_(Latter_Day_Saints) */
                return I18N::translate('Uncleared: insufficient data');
            default:
                return $status_code;
        }
    }

    /**
     * A sorted list of all status names, for a given GEDCOM tag
     *
     * @param string $tag
     *
     * @return array<string>
     */
    public static function statusNames(string $tag): array
    {
        $status_names = [];
        foreach (self::statusCodes($tag) as $status_code) {
            $status_names[$status_code] = self::statusName($status_code);
        }
        uasort($status_names, '\Fisharebest\Webtrees\I18N::strcasecmp');

        return $status_names;
    }
}
