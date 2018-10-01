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
declare(strict_types=1);


namespace Fisharebest\Webtrees\Census;

/**
 * Marital status.
 */
class CensusColumnConditionDanish extends CensusColumnConditionEnglish
{
    /** @var string Text to display for married males */
    protected $husband = 'Gift';

    /** @var string Text to display for married females */
    protected $wife    = 'Gift';

    /** @var string Text to display for unmarried males */
    protected $bachelor = 'Ugift';

    /** @var string Text to display for unmarried females */
    protected $spinster = 'Ugift';

    /** @var string Text to display for divorced males */
    protected $divorce  = 'Skilt';

    /** @var string Text to display for divorced females */
    protected $divorcee = 'Skilt';

    /** @var string Text to display for widowed males */
    protected $widower = 'Gift';

    /** @var string Text to display for widowed females */
    protected $widow   = 'Gift';
}
