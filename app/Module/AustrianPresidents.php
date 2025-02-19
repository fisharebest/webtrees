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

use Illuminate\Support\Collection;

class AustrianPresidents extends AbstractModule implements ModuleHistoricEventsInterface
{
    use ModuleHistoricEventsTrait;

    public function title(): string
    {
        return 'Bundespräsidenten Österreichs 🇦🇹';
    }

    public function isEnabledByDefault(): bool
    {
        return false;
    }

    /**
     * @return Collection<int,string>
     */
    public function historicEventsAll(string $language_tag): Collection
    {
        switch ($language_tag) {
            case 'de':
                return new Collection([
                    "1 EVEN Karl Seitz\n2 TYPE Präsident des Staatsdirektoriums\n2 DATE FROM 30 OCT 1918 TO 09 DEC 1920",
                    "1 EVEN Michael Hainisch\n2 TYPE Bundespräsident\n2 DATE FROM 09 DEC 1920 TO 10 DEC 1928",
                    "1 EVEN Wilhelm Miklas\n2 TYPE Bundespräsident\n2 DATE FROM 10 DEC 1928 TO 12 MAR 1938",
                    "1 EVEN Karl Renner\n2 TYPE Bundespräsident\n2 DATE FROM 20 DEC 1945 TO 31 DEC 1950",
                    "1 EVEN Theodor Körner\n2 TYPE Bundespräsident\n2 DATE FROM 21 JUN 1951 TO 04 JAN 1957",
                    "1 EVEN Adolf Schärf\n2 TYPE Bundespräsident\n2 DATE FROM 22 MAY 1957 TO 28 FEB 1965",
                    "1 EVEN Franz Jonas\n2 TYPE Bundespräsident\n2 DATE FROM 09 JUN 1965 TO 24 APR 1974",
                    "1 EVEN Rudolf Kirchschläger\n2 TYPE Bundespräsident\n2 DATE FROM 08 JUL 1974 TO 08 JUL 1986",
                    "1 EVEN Kurt Waldheim\n2 TYPE Bundespräsident\n2 DATE FROM 08 JUL 1986 TO 08 JUL 1992",
                    "1 EVEN Thomas Klestil\n2 TYPE Bundespräsident\n2 DATE FROM 08 JUL 1992 TO 06 JUL 2004",
                    "1 EVEN Heinz Fischer\n2 TYPE Bundespräsident\n2 DATE FROM 08 JUL 2004 TO 08 JUL 2016",
                    "1 EVEN Alexander Van der Bellen\n2 TYPE Bundespräsident\n2 DATE FROM 26 JAN 2017",
                ]);

            default:
                return new Collection();
        }
    }
}
