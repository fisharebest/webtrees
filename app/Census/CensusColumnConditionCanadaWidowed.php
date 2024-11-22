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
class CensusColumnConditionCanadaWidowed extends AbstractCensusColumnCondition
{
    // Text to display for married males
    protected const string HUSBAND = '';

    // Text to display for married females
    protected const string WIFE = '';

    // Text to display for married unmarried males
    protected const string BACHELOR = '';

    // Text to display for married unmarried females
    protected const string SPINSTER = '';

    // Text to display for male children
    protected const string BOY = '';

    // Text to display for female children
    protected const string GIRL = '';

    // Text to display for divorced individuals
    protected const string DIVORCE = '';

    // Text to display for divorced females
    protected const string DIVORCEE = '';

    // Text to display for widowed males
    protected const string WIDOWER = 'W';

    // Text to display for widowed females
    protected const string WIDOW = 'W';
}
