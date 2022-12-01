<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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

use Fisharebest\Localization\Locale\LocaleInterface;
use Fisharebest\Localization\Locale\LocaleLt;
use Illuminate\Database\Query\Builder;

/**
 * Class LanguageLithuanian.
 */
class LanguageLithuanian extends AbstractModule implements ModuleLanguageInterface
{
    use ModuleLanguageTrait;

    /**
     * Phone-book ordering of letters.
     *
     * @return array<int,string>
     */
    public function alphabet(): array
    {
        return ['A', 'Ą', 'B', 'C', 'Č', 'D', 'E', 'Ę', 'Ė', 'F', 'G', 'H', 'I', 'Y', 'Į', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'Š', 'T', 'U', 'Ų', 'Ū', 'V', 'Z', 'Ž'];
    }

    /**
     * One of: 'DMY', 'MDY', 'YMD'.
     *
     * @return string
     */
    public function dateOrder(): string
    {
        return 'YMD';
    }

    /**
     * @param string  $column
     * @param string  $letter
     * @param Builder $query
     *
     * @return void
     */
    public function initialLetterSQL(string $column, string $letter, Builder $query): void
    {
        $query->where($column . ' /*! COLLATE utf8_lithuanian_ci */', 'LIKE', '\\' . $letter . '%');
    }

    /**
     * @return LocaleInterface
     */
    public function locale(): LocaleInterface
    {
        return new LocaleLt();
    }
}
