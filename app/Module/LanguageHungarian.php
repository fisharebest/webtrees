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

use Fisharebest\Localization\Locale\LocaleHu;
use Fisharebest\Localization\Locale\LocaleInterface;
use Illuminate\Database\Query\Builder;

use function mb_substr;
use function str_starts_with;

/**
 * Class LanguageHungarian.
 */
class LanguageHungarian extends AbstractModule implements ModuleLanguageInterface
{
    use ModuleLanguageTrait;

    /**
     * Phone-book ordering of letters.
     *
     * @return array<int,string>
     */
    public function alphabet(): array
    {
        return ['A', 'Á', 'B', 'C', 'CS', 'D', 'DZ', 'DZS', 'E', 'É', 'F', 'G', 'GY', 'H', 'I', 'Í', 'J', 'K', 'L', 'LY', 'M', 'N', 'NY', 'O', 'Ó', 'Ö', 'Ő', 'P', 'Q', 'R', 'S', 'SZ', 'T', 'TY', 'U', 'Ú', 'Ü', 'Ű', 'V', 'W', 'X', 'Y', 'Z', 'ZS'];
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
     * Some languages use digraphs and trigraphs.
     *
     * @param string $string
     *
     * @return string
     */
    public function initialLetter(string $string): string
    {
        foreach (['DZS', 'DZ', 'GY', 'LY', 'NY', 'SZ', 'TY', 'ZS'] as $digraph) {
            if (str_starts_with($string, $digraph)) {
                return $digraph;
            }
        }

        return mb_substr($string, 0, 1);
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
        $query->where($column . ' /*! COLLATE utf8_hungarian_ci */', 'LIKE', '\\' . $letter . '%');
    }

    /**
     * @return LocaleInterface
     */
    public function locale(): LocaleInterface
    {
        return new LocaleHu();
    }
}
