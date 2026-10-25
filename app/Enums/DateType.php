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

namespace Fisharebest\Webtrees\Enums;

/**
 * GEDCOM date types/keywords
 */
enum DateType: string
{
    case Exact       = '';
    case About       = 'ABT';
    case Calculated  = 'CAL';
    case Estimated   = 'EST';
    case Interpreted = 'INT';
    case Before      = 'BEF';
    case After       = 'AFT';
    case From        = 'FROM';
    case To          = 'TO';
    case Between     = 'BETAND';
    case FromTo      = 'FROMTO';
}
