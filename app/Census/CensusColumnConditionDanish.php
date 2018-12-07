<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Fisharebest\Webtrees\Census;

/**
 * Marital status.
 */
class CensusColumnConditionDanish extends CensusColumnConditionEnglish
{
    /* Text to display for married individuals */
    protected $husband = 'Gift';
    protected $wife    = 'Gift';

    /* Text to display for unmarried individuals */
    protected $bachelor = 'Ugift';
    protected $spinster = 'Ugift';

    /* Text to display for divorced individuals */
    protected $divorce  = 'Skilt';
    protected $divorcee = 'Skilt';

    /* Text to display for widowed individuals */
    protected $widower = 'Gift';
    protected $widow   = 'Gift';
}
