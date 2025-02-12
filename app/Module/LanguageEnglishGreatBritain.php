<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Localization\Locale\LocaleEnGb;
use Fisharebest\Localization\Locale\LocaleInterface;

class LanguageEnglishGreatBritain extends LanguageEnglishUnitedStates
{
    // British English changes "three-times" to "thrice"
    protected const REMOVED = [
        '',
        ' once removed',
        ' twice removed',
        ' thrice removed',
        ' four times removed',
        ' five times removed',
        ' six times removed',
        ' seven times removed',
        ' eight times removed',
        ' nine times removed',
        ' ten times removed',
        ' eleven removed',
        ' twelve removed',
        ' thirteen removed',
        ' fourteen times removed',
        ' fifteen times removed',
        ' sixteen times removed',
        ' seventeen times removed',
        ' eighteen times removed',
        ' nineteen times removed',
        ' twenty times removed',
        ' twenty-one times removed',
        ' twenty-two times removed',
        ' twenty-three times removed',
        ' twenty-four times removed',
        ' twenty-five times removed',
        ' twenty-six times removed',
        ' twenty-seven times removed',
        ' twenty-eight times removed',
        ' twenty-nine times removed',
    ];

    public function dateOrder(): string
    {
        return 'DMY';
    }

    public function locale(): LocaleInterface
    {
        return new LocaleEnGb();
    }
}
