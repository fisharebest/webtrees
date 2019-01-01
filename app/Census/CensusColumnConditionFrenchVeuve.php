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
declare(strict_types=1);

namespace Fisharebest\Webtrees\Census;

/**
 * Marital status.
 */
class CensusColumnConditionFrenchVeuve extends AbstractCensusColumnCondition
{
    /** @var string Text to display for married males */
    protected $husband = '';

    /** @var string Text to display for married females */
    protected $wife    = '';

    /** @var string Text to display for unmarried males */
    protected $bachelor = '';

    /** @var string Text to display for unmarried females */
    protected $spinster = '';

    /** @var string Text to display for male children */
    protected $boy  = '';

    /** @var string Text to display for female children */
    protected $girl = '';

    /** @var string Text to display for divorced males */
    protected $divorce  = '';

    /** @var string Text to display for divorced females */
    protected $divorcee = '';

    /** @var string Text to display for widowed males */
    protected $widower = '';

    /** @var string Text to display for widowed females */
    protected $widow   = '1';
}
