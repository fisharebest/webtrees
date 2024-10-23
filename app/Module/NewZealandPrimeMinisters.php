<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

use Fisharebest\Webtrees\I18N;
use Illuminate\Support\Collection;

/**
 * Class NewZealandPrimeMinisters
 */
class NewZealandPrimeMinisters extends AbstractModule implements ModuleHistoricEventsInterface
{
    use ModuleHistoricEventsTrait;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        return 'New Zealand Premiers / Prime Ministers';
    }

    /**
     * Should this module be enabled when it is first installed?
     *
     * @return bool
     */
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
            case 'en-AU':
            case 'en-GB':
            case 'en-US':
                return new Collection([
                    "1 EVEN Henry Sewell\n2 TYPE Premier of New Zealand\n2 DATE FROM 07 MAY 1856 TO 20 MAY 1856",
                    "1 EVEN William Fox\n2 TYPE Premier of New Zealand\n2 DATE FROM 20 MAY 1856 TO 02 JUN 1856",
                    "1 EVEN Edward Stafford\n2 TYPE Premier of New Zealand\n2 DATE FROM 02 JUN 1856 TO 12 JUL 1861",
                    "1 EVEN William Fox\n2 TYPE Premier of New Zealand\n2 DATE FROM 12 JUL 1861 TO 06 AUG 1862",
                    "1 EVEN Alfred Domett\n2 TYPE Premier of New Zealand\n2 DATE FROM 06 AUG 1862 TO 30 OCT 1863",
                    "1 EVEN Frederick Whitaker\n2 TYPE Premier of New Zealand\n2 DATE FROM 30 OCT 1863 TO 24 NOV 1864",
                    "1 EVEN Frederick Weld\n2 TYPE Premier of New Zealand\n2 DATE FROM 24 NOV 1864 TO 16 OCT 1865",
                    "1 EVEN Edward Stafford\n2 TYPE Premier of New Zealand\n2 DATE FROM 16 OCT 1865 TO 28 JUN 1869",
                    "1 EVEN William Fox \n2 TYPE Premier of New Zealand\n2 DATE FROM 28 JUN 1869 TO 10 SEP 1872",
                    "1 EVEN Edward Stafford\n2 TYPE Premier of New Zealand\n2 DATE FROM 10 SEP 1872 TO 11 OCT 1872",
                    "1 EVEN George Waterhouse\n2 TYPE Premier of New Zealand\n2 DATE FROM 11 OCT 1872 TO 03 MAR 1873",
                    "1 EVEN William Fox\n2 TYPE Premier of New Zealand\n2 DATE FROM 03 MAR 1873 TO 08 APR 1873",
                    "1 EVEN Julius Vogel\n2 TYPE Premier of New Zealand\n2 DATE FROM 08 APR 1873 TO 06 JUL 1875",
                    "1 EVEN Daniel Pollen\n2 TYPE Premier of New Zealand\n2 DATE FROM 06 JUL 1875 TO 15 FEB 1876",
                    "1 EVEN Julius Vogel\n2 TYPE Premier of New Zealand\n2 DATE FROM 15 FEB 1876 TO 01 SEP 1876",
                    "1 EVEN Harry Atkinson\n2 TYPE Premier of New Zealand\n2 DATE FROM 01 SEP 1876 TO 13 OCT 1877",
                    "1 EVEN George Grey\n2 TYPE Premier of New Zealand\n2 DATE FROM 13 OCT 1877 TO 08 OCT 1879",
                    "1 EVEN John Hall\n2 TYPE Premier of New Zealand\n2 DATE FROM 08 OCT 1879 TO 21 APR 1882",
                    "1 EVEN Frederick Whitaker\n2 TYPE Premier of New Zealand\n2 DATE FROM 21 APR 1882 TO 25 SEP 1883",
                    "1 EVEN Harry Atkinson\n2 TYPE Premier of New Zealand\n2 DATE FROM 25 SEP 1883 TO 16 AUG 1883",
                    "1 EVEN Robert Stout\n2 TYPE Premier of New Zealand\n2 DATE FROM 16 AUG 1884 TO 28 AUG 1884",
                    "1 EVEN Harry Atkinson\n2 TYPE Premier of New Zealand\n2 DATE FROM 28 AUG 1884 TO 03 SEP 1884",
                    "1 EVEN Robert Stout\n2 TYPE Premier of New Zealand\n2 DATE FROM 03 SEP 1884 TO 08 OCT 1887",
                    "1 EVEN Harry Atkinson\n2 TYPE Premier of New Zealand\n2 DATE FROM 08 OCT 1887 TO 21 JAN 1891",
                    "1 EVEN John Ballance\n2 TYPE Premier of New Zealand\n2 DATE FROM 24 JAN 1891 TO 27 APR 1893",
                    "1 EVEN Richard Seddon\n2 TYPE Premier of New Zealand\n2 DATE FROM 01 MAY 1893 TO 10 JUN 1906",
                    "1 EVEN William Hall-Jones\n2 TYPE Prime Minister of New Zealand\n2 DATE FROM 21 JUN 1906 TO 06 AUG 1906",
                    "1 EVEN Joseph Ward\n2 TYPE Prime Minister of New Zealand\n2 DATE FROM 06 AUG 1906 TO 12 MAR 1912",
                    "1 EVEN Thomas Mackenzie\n2 TYPE Prime Minister of New Zealand\n2 DATE FROM 28 MAR 1912 TO 10 JUL 1912",
                    "1 EVEN William Massey\n2 TYPE Prime Minister of New Zealand\n2 DATE FROM 10 JUL 1912 TO 10 MAY 1925",
                    "1 EVEN Francis Henry Dillon Bell\n2 TYPE Prime Minister of New Zealand\n2 DATE FROM 14 MAY 1925 TO 30 MAY 1925",
                    "1 EVEN Gordon Coates\n2 TYPE Prime Minister of New Zealand\n2 DATE FROM 30 MAY 1925 TO 10 DEC 1928",
                    "1 EVEN George Forbes\n2 TYPE Prime Minister of New Zealand\n2 DATE FROM 10 DEC 1928 TO 28 MAY 1930",
                    "1 EVEN Joseph Ward\n2 TYPE Prime Minister of New Zealand\n2 DATE FROM 28 MAY 1930 TO 06 DEC 1935",
                    "1 EVEN Michael Joseph Savage\n2 TYPE Prime Minister of New Zealand\n2 DATE FROM 06 DEC 1935 TO 27 MAR 1940",
                    "1 EVEN Peter Fraser\n2 TYPE Prime Minister of New Zealand\n2 DATE FROM 27 MAR 1940 TO 13 DEC 1949",
                    "1 EVEN Sidney Holland\n2 TYPE Prime Minister of New Zealand\n2 DATE FROM 13 DEC 1949 TO 20 SEP 1957",
                    "1 EVEN Keith Holyoake\n2 TYPE Prime Minister of New Zealand\n2 DATE FROM 20 SEP 1957 TO 12 DEC 1957",
                    "1 EVEN Walter Nash\n2 TYPE Prime Minister of New Zealand\n2 DATE FROM 12 DEC 1957 TO 12 DEC 1960",
                    "1 EVEN Keith Holyoake\n2 TYPE Prime Minister of New Zealand\n2 DATE FROM 12 DEC 1960 TO 07 FEB 1972",
                    "1 EVEN Jack Marshall\n2 TYPE Prime Minister of New Zealand\n2 DATE FROM 07 FEB 1972 TO 08 DEC 1972",
                    "1 EVEN Norman Kirk\n2 TYPE Prime Minister of New Zealand\n2 DATE FROM 08 DEC 1972 TO 31 AUG 1974",
                    "1 EVEN Bill Rowling\n2 TYPE Prime Minister of New Zealand\n2 DATE FROM 06 SEP 1974 TO 12 DEC 1975",
                    "1 EVEN Rob Muldoon\n2 TYPE Prime Minister of New Zealand\n2 DATE FROM 12 DEC 1975 TO 26 JUL 1984",
                    "1 EVEN David Lange\n2 TYPE Prime Minister of New Zealand\n2 DATE FROM 26 JUL 1984 TO 08 AUG 1989",
                    "1 EVEN Geoffrey Palmer\n2 TYPE Prime Minister of New Zealand\n2 DATE FROM 08 AUG 1989 TO 04 SEP 1990",
                    "1 EVEN Mike Moore\n2 TYPE Prime Minister of New Zealand\n2 DATE FROM 04 SEP 1990 TO 02 NOV 1990",
                    "1 EVEN Jim Bolger\n2 TYPE Prime Minister of New Zealand\n2 DATE FROM 02 NOV 1990 TO 08 DEC 1997",
                    "1 EVEN Jenny Shipley\n2 TYPE Prime Minister of New Zealand\n2 DATE FROM 08 DEC 1997 TO 05 DEC 1999",
                    "1 EVEN Helen Clark\n2 TYPE Prime Minister of New Zealand\n2 DATE FROM 05 DEC 1999 TO 19 NOV 2008",
                    "1 EVEN John Key\n2 TYPE Prime Minister of New Zealand\n2 DATE FROM 19 NOV 2008 TO 12 DEC 2016",
                    "1 EVEN Bill English\n2 TYPE Prime Minister of New Zealand\n2 DATE FROM 12 DEC 2016 TO 26 OCT 2017",
                    "1 EVEN Jacinda Ardern\n2 TYPE Prime Minister of New Zealand\n2 DATE FROM 26 OCT 2017 TO 25 JAN 2023",
                    "1 EVEN Chris Hipkins\n2 TYPE Prime Minister of New Zealand\n2 DATE FROM 25 JAN 2023 TO 14 OCT 2023",
                    "1 EVEN Chris Luxon\n2 TYPE Prime Minister of New Zealand\n2 DATE FROM 14 OCT 2023",
                ]);

            default:
                return new Collection();
        }
    }
}
