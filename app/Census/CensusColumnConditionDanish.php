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

namespace Fisharebest\Webtrees\Census;

/**
 * Marital status.
 */
class CensusColumnConditionDanish extends CensusColumnConditionEnglish
{
    // Text to display for married males
    protected const HUSBAND = 'Gift';

    // Text to display for married females
    protected const WIFE = 'Gift';

    // Text to display for married unmarried males
    protected const BACHELOR = 'Ugift';

    // Text to display for married unmarried females
    protected const SPINSTER = 'Ugift';

    // Text to display for divorced males
    protected const DIVORCE = 'Skilt';

    // Text to display for divorced females
    protected const DIVORCEE = 'Skilt';

    // Text to display for widowed males
    protected const WIDOWER = 'Gift';

    // Text to display for widowed females
    protected const WIDOW = 'Gift';
}
