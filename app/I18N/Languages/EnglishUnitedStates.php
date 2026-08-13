<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\I18N\Languages;

use Fisharebest\Webtrees\Enums\Weekday;
use Fisharebest\Webtrees\Report\PaperSize;

final readonly class EnglishUnitedStates extends AbstractEnglish
{
    protected const string    ENDONYM            = 'American English';
    protected const PaperSize PAPER_SIZE         = PaperSize::USLetter;
    protected const string    LANGUAGE_TAG       = 'en-US';
    protected const string    LOCALE_CODE        = 'en_US@collation=phonebook';
    protected const Weekday   FIRST_DAY          = Weekday::Sunday;
    protected const string    LIST_SEPARATOR_AND = ', and ';
    protected const string    LIST_SEPARATOR_OR  = ', or ';

    protected function assembleDate(string $day, string $month, string $year): string
    {
        $parts = [];

        if ($month !== '') {
            $parts[] = $month;
        }

        if ($day !== '') {
            if ($month !== '' && $year !== '') {
                $parts[] = $day . ',';
            } else {
                $parts[] = $day;
            }
        }

        if ($year !== '') {
            $parts[] = $year;
        }

        return implode(' ', $parts);
    }

    public function dateOrder(): string
    {
        return 'MDY';
    }
}
