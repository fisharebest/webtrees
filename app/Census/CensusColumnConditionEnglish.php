<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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
class CensusColumnConditionEnglish extends AbstractCensusColumnCondition
{
    // Text to display for married males
    protected const HUSBAND = 'Mar';

    // Text to display for married females
    protected const WIFE = 'Mar';

    // Text to display for married unmarried males
    protected const BACHELOR = 'Unm';

    // Text to display for married unmarried females
    protected const SPINSTER = 'Unm';

    // Text to display for divorced males
    protected const DIVORCE = 'Div';

    // Text to display for divorced females
    protected const DIVORCEE = 'Div';

    // Text to display for widowed males
    protected const WIDOWER = 'Wid';

    // Text to display for widowed females
    protected const WIDOW = 'Wid';
}
