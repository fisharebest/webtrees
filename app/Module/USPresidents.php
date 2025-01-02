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

class USPresidents extends AbstractModule implements ModuleHistoricEventsInterface
{
    use ModuleHistoricEventsTrait;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        return 'United States presidents 🇺🇸';
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
                    "1 EVEN George Washington\n2 TYPE 1st President of the United States\n2 DATE 30 APR 1789",
                    "1 EVEN John Adams\n2 TYPE 2nd President of the United States\n2 DATE 4 MAR 1797",
                    "1 EVEN Thomas Jefferson\n2 TYPE 3rd President of the United States\n2 DATE 4 MAR 1801",
                    "1 EVEN James Madison\n2 TYPE 4th President of the United States\n2 DATE 4 MAR 1809",
                    "1 EVEN James Monroe\n2 TYPE 5th President of the United States\n2 DATE 4 MAR 1817",
                    "1 EVEN John Quincy Adams\n2 TYPE 6th President of the United States\n2 DATE 4 MAR 1825",
                    "1 EVEN Andrew Jackson\n2 TYPE 7th President of the United States\n2 DATE 4 MAR 1829",
                    "1 EVEN Martin Van Buren\n2 TYPE 8th President of the United States\n2 DATE 4 MAR 1837",
                    "1 EVEN William Henry Harrison\n2 TYPE 9th President of the United States\n2 DATE 4 MAR 1841",
                    "1 EVEN John Tyler\n2 TYPE 10th President of the United States\n2 DATE 4 APR 1841",
                    "1 EVEN James K Polk\n2 TYPE 11th President of the United States\n2 DATE 4 MAR 1845",
                    "1 EVEN Zachary Taylor\n2 TYPE 12th President of the United States\n2 DATE 4 MAR 1849",
                    "1 EVEN Millard Fillmore\n2 TYPE 13th President of the United States\n2 DATE 9 JUL 1850",
                    "1 EVEN Franklin Pierce\n2 TYPE 14th President of the United States\n2 DATE 4 MAR 1853",
                    "1 EVEN James Buchanan\n2 TYPE 15th President of the United States\n2 DATE 4 MAR 1857",
                    "1 EVEN Abraham Lincoln\n2 TYPE 16th President of the United States\n2 DATE 4 MAR 1861",
                    "1 EVEN Andrew Johnson\n2 TYPE 17th President of the United States\n2 DATE 15 APR 1865",
                    "1 EVEN Ulysses S Grant\n2 TYPE 18th President of the United States\n2 DATE 4 MAR 1869",
                    "1 EVEN Rutherford B Hayes\n2 TYPE 19th President of the United States\n2 DATE 4 MAR 1877",
                    "1 EVEN James A Garfield\n2 TYPE 20th President of the United States\n2 DATE 4 MAR 1881",
                    "1 EVEN Chester A Arthur\n2 TYPE 21st President of the United States\n2 DATE 19 SEP 1881",
                    "1 EVEN Grover Cleveland\n2 TYPE 22nd President of the United States\n2 DATE 4 MAR 1885",
                    "1 EVEN Benjamin Harrison\n2 TYPE 23rd President of the United States\n2 DATE 4 MAR 1889",
                    "1 EVEN Grover Cleveland\n2 TYPE 24th President of the United States\n2 DATE 4 MAR 1893",
                    "1 EVEN William McKinley\n2 TYPE 25th President of the United States\n2 DATE 4 MAR 1897",
                    "1 EVEN Theodore Roosevelt\n2 TYPE 26th President of the United States\n2 DATE 14 SEP 1901",
                    "1 EVEN William Howard Taft\n2 TYPE 27th President of the United States\n2 DATE 4 MAR 1909",
                    "1 EVEN Woodrow Wilson\n2 TYPE 28th President of the United States\n2 DATE 4 MAR 1913",
                    "1 EVEN Warren G Harding\n2 TYPE 29th President of the United States\n2 DATE 4 MAR 1921",
                    "1 EVEN Calvin Coolidge\n2 TYPE 30th President of the United States\n2 DATE 2 AUG 1923",
                    "1 EVEN Herbert Hoover\n2 TYPE 31st President of the United States\n2 DATE 4 MAR 1929",
                    "1 EVEN Franklin D Roosevelt\n2 TYPE 32nd President of the United States\n2 DATE 4 MAR 1933",
                    "1 EVEN Harry S Truman\n2 TYPE 33rd President of the United States\n2 DATE 12 APR 1945",
                    "1 EVEN Dwight D Eisenhower\n2 TYPE 34th President of the United States\n2 DATE 20 JAN 1953",
                    "1 EVEN John F Kennedy\n2 TYPE 35th President of the United States\n2 DATE 20 JAN 1961",
                    "1 EVEN Lyndon B Johnson\n2 TYPE 36th President of the United States\n2 DATE 22 NOV 1963",
                    "1 EVEN Richard Nixon\n2 TYPE 37th President of the United States\n2 DATE 20 JAN 1969",
                    "1 EVEN Gerald Ford\n2 TYPE 38th President of the United States\n2 DATE 9 AUG 1974",
                    "1 EVEN Jimmy Carter\n2 TYPE 39th President of the United States\n2 DATE 20 JAN 1977",
                    "1 EVEN Ronald Reagan\n2 TYPE 40th President of the United States\n2 DATE 20 JAN 1981",
                    "1 EVEN George H W Bush\n2 TYPE 41st President of the United States\n2 DATE 20 JAN 1989",
                    "1 EVEN Bill Clinton\n2 TYPE 42nd President of the United States\n2 DATE 20 JAN 1993",
                    "1 EVEN George W Bush\n2 TYPE 43rd President of the United States\n2 DATE 20 JAN 2001",
                    "1 EVEN Barack Obama\n2 TYPE 44th President of the United States\n2 DATE 20 JAN 2009",
                    "1 EVEN Donald Trump\n2 TYPE 45th President of the United States\n2 DATE 20 JAN 2017",
                    "1 EVEN Joe Biden\n2 TYPE 46th President of the United States\n2 DATE 20 JAN 2021",
                ]);

            default:
                return new Collection();
        }
    }
}
