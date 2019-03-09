<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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
class CensusColumnConditionFrenchVeuve extends AbstractCensusColumnCondition
{
    /* Text to display for married individuals */
    protected $husband = '';
    protected $wife    = '';

    /* Text to display for unmarried individuals */
    protected $bachelor = '';
    protected $spinster = '';

    /* Text to display for children */
    protected $boy  = '';
    protected $girl = '';

    /* Text to display for divorced individuals */
    protected $divorce  = '';
    protected $divorcee = '';

    /* Text to display for widowed individuals (not yet implemented) */
    protected $widower = '';
    protected $widow   = '1';
}
