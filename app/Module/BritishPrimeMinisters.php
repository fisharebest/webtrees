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
 * Class BritishPrimeMinisters
 */
class BritishPrimeMinisters extends AbstractModule implements ModuleHistoricEventsInterface
{
    use ModuleHistoricEventsTrait;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        return 'British prime ministers 🇬🇧';
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
     * All events provided by this module.
     *
     * @return Collection<int,string>
     */
    public function historicEventsAll(): Collection
    {
        switch (I18N::languageTag()) {
            case 'en-AU':
            case 'en-GB':
            case 'en-US':
                return new Collection([
                    "1 EVEN Robert Walpole\n2 TYPE Prime Minister of Great Britain\n2 DATE FROM 03 APR 1721 TO 16 FEB 1742",
                    "1 EVEN Spencer Compton\n2 TYPE Prime Minister of Great Britain\n2 DATE FROM 16 FEB 1742 TO 27 AUG 1743",
                    "1 EVEN Henry Pelham\n2 TYPE Prime Minister of Great Britain\n2 DATE FROM 27 AUG 1743 TO 16 MAR 1754",
                    "1 EVEN Thomas Pelham-Holles\n2 TYPE Prime Minister of Great Britain\n2 DATE FROM 16 MAR 1754 TO 16 NOV 1756",
                    "1 EVEN William Cavendish\n2 TYPE Prime Minister of Great Britain\n2 DATE FROM 16 NOV 1756 TO 29 JUN 1757",
                    "1 EVEN Thomas Pelham-Holles\n2 TYPE Prime Minister of Great Britain\n2 DATE FROM 29 JUN 1757 TO 26 MAY 1762",
                    "1 EVEN John Stuart\n2 TYPE Prime Minister of Great Britain\n2 DATE FROM 26 MAY 1762 TO 26 MAY 1762",
                    "1 EVEN George Grenville\n2 TYPE Prime Minister of Great Britain\n2 DATE FROM 26 MAY 1762 TO 13 JUL 1765",
                    "1 EVEN Charles Watson-Wentworth\n2 TYPE Prime Minister of Great Britain\n2 DATE FROM 13 JUL 1765 TO 30 JUL 1766",
                    "1 EVEN William Pitt\n2 TYPE Prime Minister of Great Britain\n2 DATE FROM 30 JUL 1766 TO 14 OCT 1768",
                    "1 EVEN Augustus FitzRoy\n2 TYPE Prime Minister of Great Britain\n2 DATE FROM 14 OCT 1768 TO 28 JAN 1770",
                    "1 EVEN Frederick North\n2 TYPE Prime Minister of Great Britain\n2 DATE FROM 28 JAN 1770 TO 27 MAR 1782",
                    "1 EVEN Charles Watson-Wentworth\n2 TYPE Prime Minister of Great Britain\n2 DATE FROM 27 MAR 1782 TO 04 JUL 1782",
                    "1 EVEN William Petty\n2 TYPE Prime Minister of Great Britain\n2 DATE FROM 04 JUL 1782 TO 02 APR 1783",
                    "1 EVEN William Cavendish-Bentinck\n2 TYPE Prime Minister of Great Britain\n2 DATE FROM 02 APR 1783 TO 19 DEC 1783",
                    "1 EVEN William Pitt the Younger\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 19 DEC 1783 TO 17 MAR 1801",
                    "1 EVEN Henry Addington\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 17 MAR 1801 TO 10 MAY 1804",
                    "1 EVEN William Pitt the Younger\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 10 MAY 1804 TO 11 FEB 1806",
                    "1 EVEN William Grenville\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 11 FEB 1806 TO 31 MAR 1807",
                    "1 EVEN William Cavendish-Bentinck\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 31 MAR 1807 TO 04 OCT 1809",
                    "1 EVEN Spencer Perceval\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 04 OCT 1809 TO 08 JUN 1812",
                    "1 EVEN Robert Jenkinson\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 08 JUN 1812 TO 12 APR 1827",
                    "1 EVEN George Canning\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 12 APR 1827 TO 31 AUG 1827",
                    "1 EVEN Frederick John Robinson\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 31 AUG 1827 TO 22 JAN 1828",
                    "1 EVEN Arthur Wellesley, Duke of Wellington\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 22 JAN 1828 TO 22 NOV 1830",
                    "1 EVEN Charles Grey\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 22 NOV 1830 TO 16 JUL 1834",
                    "1 EVEN William Lamb\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 16 JUL 1834 TO 17 NOV 1834",
                    "1 EVEN Arthur Wellesley, Duke of Wellington\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 17 NOV 1834 TO 10 DEC 1834",
                    "1 EVEN Robert Peel\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 10 DEC 1834 TO 18 APR 1835",
                    "1 EVEN William Lamb\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 18 APR 1835 TO 30 AUG 1841",
                    "1 EVEN Robert Peel\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 30 AUG 1841 TO 30 JUN 1846",
                    "1 EVEN John Russell\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 30 JUN 1846 TO 20 FEB 1852",
                    "1 EVEN Edward Smith-Stanley\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 20 FEB 1852 TO 19 DEC 1852",
                    "1 EVEN George Hamilton-Gordon\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 19 DEC 1852 TO 06 FEB 1855",
                    "1 EVEN Henry John Temple\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 06 FEB 1855 TO 20 FEB 1858",
                    "1 EVEN Edward Smith-Stanley\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 20 FEB 1858 TO 12 JUN 1859",
                    "1 EVEN Henry John Temple\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 12 JUN 1859 TO 29 OCT 1865",
                    "1 EVEN John Russell\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 29 OCT 1865 TO 28 JUN 1866",
                    "1 EVEN Edward Smith-Stanley\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 28 JUN 1866 TO 27 FEB 1868",
                    "1 EVEN Benjamin Disraeli\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 27 FEB 1868 TO 03 DEC 1868",
                    "1 EVEN William Ewart Gladstone\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 03 DEC 1868 TO 20 FEB 1874",
                    "1 EVEN Benjamin Disraeli\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 20 FEB 1874 TO 23 APR 1880",
                    "1 EVEN Robert Gascoyne-Cecil\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 23 APR 1880 TO 01 FEB 1886",
                    "1 EVEN William Ewart Gladstone\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 01 FEB 1886 TO 25 JUL 1886",
                    "1 EVEN Robert Gascoyne-Cecil\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 25 JUL 1886 TO 15 AUG 1892",
                    "1 EVEN William Ewart Gladstone\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 15 AUG 1892 TO 05 MAR 1894",
                    "1 EVEN Archibald Primrose\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 05 MAR 1894 TO 25 JUN 1895",
                    "1 EVEN Robert Gascoyne-Cecil\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 25 JUN 1895 TO 12 JUL 1902",
                    "1 EVEN Arthur Balfour\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 12 JUL 1902 TO 05 DEC 1905",
                    "1 EVEN Henry Campbell-Bannerman\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 05 DEC 1905 TO 05 APR 1908",
                    "1 EVEN Herbert Henry Asquith\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 05 APR 1908 TO 06 DEC 1916",
                    "1 EVEN David Lloyd George\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 06 DEC 1916 TO 23 OCT 1922",
                    "1 EVEN Bonar Law\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 23 OCT 1922 TO 22 MAY 1923",
                    "1 EVEN Stanley Baldwin\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 22 MAY 1923 TO 22 JAN 1924",
                    "1 EVEN Ramsey MacDonald\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 22 JAN 1924 TO 07 JUN 1935",
                    "1 EVEN Stanley Baldwin\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 07 JUN 1935 TO 28 MAY 1937",
                    "1 EVEN Neville Chamberlain\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 28 MAY 1937 TO 10 MAY 1940",
                    "1 EVEN Winston Churchill\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 10 MAY 1940 TO 26 JUL 1945",
                    "1 EVEN Clement Atlee\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 26 JUL 1945 TO 26 OCT 1951",
                    "1 EVEN Winston Churchill\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 26 OCT 1951 TO 06 APR 1955",
                    "1 EVEN Anthony Eden\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 06 APR 1955 TO 10 JAN 1957",
                    "1 EVEN Harold Macmillan\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 10 JAN 1957 TO 19 OCT 1963",
                    "1 EVEN Alex Douglas-Home\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 19 OCT 1963 TO 16 OCT 1964",
                    "1 EVEN Harold Wilson\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 16 OCT 1964 TO 19 JUN 1970",
                    "1 EVEN Edward Heath\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 19 JUN 1970 TO 04 MAR 1974",
                    "1 EVEN Harold Wilson\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 04 MAR 1974 TO 05 APR 1976",
                    "1 EVEN James Callaghan\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 05 APR 1976 TO 04 MAY 1979",
                    "1 EVEN Margaret Thatcher\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 04 MAY 1979 TO 28 NOV 1990",
                    "1 EVEN John Major\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 28 NOV 1990 TO 02 MAY 1997",
                    "1 EVEN Tony Blair\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 02 MAY 1997 TO 27 JUN 2007",
                    "1 EVEN Gordon Brown\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 27 JUN 2007 TO 11 MAY 2010",
                    "1 EVEN David Cameron\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 11 MAY 2010 TO 13 JUL 2016",
                    "1 EVEN Theresa May\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 13 JUL 2016 TO 24 JUL 2019",
                    "1 EVEN Boris Johnson\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 24 JUL 2019 TO 06 SEP 2022",
                    "1 EVEN Liz Truss\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 06 SEP 2022 TO 25 OCT 2022",
                    "1 EVEN Rishi Sunak\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 25 OCT 2022 TO 05 JUL 2024",
                    "1 EVEN Keir Starmer\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 05 JUL 2024",
                ]);

            default:
                return new Collection();
        }
    }
}
