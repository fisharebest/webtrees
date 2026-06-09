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

namespace Fisharebest\Webtrees\Census;

final readonly class CensusColumnConditionFrenchGarcon extends AbstractCensusColumnCondition
{
    protected const string HUSBAND = '';
    protected const string WIFE = '';
    protected const string BACHELOR = '1';
    protected const string SPINSTER = '';
    protected const string BOY = '1';
    protected const string GIRL = '';
    protected const string DIVORCE = '';
    protected const string DIVORCEE = '';
    protected const string WIDOWER = '';
    protected const string WIDOW = '';
}
