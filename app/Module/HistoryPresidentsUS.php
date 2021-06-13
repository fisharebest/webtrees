<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

/**
 * Class HistoryPresidentsUS
 */
class HistoryPresidentsUS extends AbstractModule implements ModuleHistoricEventsInterface
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
     * All events provided by this module.
     *
     * @return Collection<string>
     */
    public function historicEventsAll(): Collection
    {
        return new Collection([
            "1 EVEN George Washington (1732 — 1799), Unaffiliated\n2 TYPE 1st President of the United States\n2 DATE FROM 30 APR 1789 TO 04 MAR 1797\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/George_Washington)",
            "1 EVEN John Adams (1735 — 1826), Federalist\n2 TYPE 2nd President of the United States\n2 DATE FROM 04 MAR 1797 TO 04 MAR 1801\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/John_Adams)",
            "1 EVEN Thomas Jefferson (1743 — 1826), Democratic-Republican\n2 TYPE 3rd President of the United States\n2 DATE FROM 04 MAR 1801 TO 04 MAR 1809\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/Thomas_Jefferson)",
            "1 EVEN James Madison (1751 — 1836), Democratic-Republican\n2 TYPE 4th President of the United States\n2 DATE FROM 04 MAR 1809 TO 04 MAR 1817\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/James_Madison)",
            "1 EVEN James Monroe (1758 — 1831), Democratic-Republican\n2 TYPE 5th President of the United States\n2 DATE FROM 04 MAR 1817 TO 04 MAR 1825\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/James_Monroe)",
            "1 EVEN John Quincy Adams (1767 — 1848), National Republican\n2 TYPE 6th President of the United States\n2 DATE FROM 04 MAR 1825 TO 04 MAR 1829\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/John_Quincy_Adams)",
            "1 EVEN Andrew Jackson (1767 — 1845), Democratic\n2 TYPE 7th President of the United States\n2 DATE FROM 04 MAR 1829 TO 04 MAR 1837\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/Andrew_Jackson)",
            "1 EVEN Martin Van Buren (1782 — 1862), Democratic\n2 TYPE 8th President of the United States\n2 DATE FROM 04 MAR 1837 TO 04 MAR 1841\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/Martin_Van_Buren)",
            "1 EVEN William Henry Harrison (1773 — 1841), Whig\n2 TYPE 9th President of the United States\n2 DATE FROM 04 MAR 1841 TO 04 APR 1841\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/William_Henry_Harrison)",
            "1 EVEN John Tyler (1790 — 1862), Unaffiliated\n2 TYPE 10th President of the United States\n2 DATE FROM 04 APR 1841 TO 04 MAR 1845\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/John_Tyler)",
            "1 EVEN James K. Polk (1795 — 1849), Democratic\n2 TYPE 11th President of the United States\n2 DATE FROM 04 MAR 1845 TO 04 MAR 1849\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/James_K._Polk)",
            "1 EVEN Zachary Taylor (1784 — 1850), Whig\n2 TYPE 12th President of the United States\n2 DATE FROM 04 MAR 1849 TO 09 JUL 1850\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/Zachary_Taylor)",
            "1 EVEN Millard Fillmore (1800 — 1874), Whig\n2 TYPE 13th President of the United States\n2 DATE FROM 09 JUL 1850 TO 004 MAR 1853\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/Millard_Fillmore)",
            "1 EVEN Franklin Pierce (1804 — 1869), Democratic\n2 TYPE 14th President of the United States\n2 DATE FROM 04 MAR 1853 TO 004 MAR 1857\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/Franklin_Pierce)",
            "1 EVEN James Buchanan (1791 — 1868), Democratic\n2 TYPE 15th President of the United States\n2 DATE FROM 04 MAR 1857 TO 04 MAR 1861\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/James_Buchanan)",
            "1 EVEN Abraham Lincoln (1809 — 1865), Republican\n2 TYPE 16th President of the United States\n2 DATE FROM 04 MAR 1861 TO 15 APR 1865\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/Abraham_Lincoln)",
            "1 EVEN Andrew Johnson (1808 — 1875), Democratic\n2 TYPE 17th President of the United States\n2 DATE FROM 15 APR 1865 TO 04 MAR 1869\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/Andrew_Johnson)",
            "1 EVEN Ulysses S. Grant (1822 — 1885, Republican)\n2 TYPE 18th President of the United States\n2 DATE FROM 04 MAR 1869 TO 04 MAR 1877\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/Ulysses_S._Grant)",
            "1 EVEN Rutherford B. Hayes (1822 — 1893, Republica), Republican\n2 TYPE 19th President of the United States\n2 DATE FROM 04 MAR 1877 TO 04 MAR 1877\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/Rutherford_B._Hayes)",
            "1 EVEN James A. Garfield (1831 — 1881), Republican\n2 TYPE 20th President of the United States\n2 DATE FROM 04 MAR 1881 TO 19 SEP 1881\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/James_A._Garfield)",
            "1 EVEN Chester A. Arthur (1829 — 1886), Republican\n2 TYPE 21st President of the United States\n2 DATE FROM 19 SEP 1881 TO 04 MAR 1885\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/Chester_A._Arthur",
            "1 EVEN Grover Cleveland(1837 — 1908), Democratic \n2 TYPE 22nd President of the United States\n2 DATE FROM 04 MAR 1885 TO 04 MAR 1889\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/Grover_Cleveland)",
            "1 EVEN Benjamin Harrison(1833 — 1901), Republican \n2 TYPE 23rd President of the United States\n2 DATE FROM 04 MAR 1889 TO 04 MAR 1893\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/Benjamin_Harrison)",
            "1 EVEN Grover Cleveland (1837 — 1908), Democratic\n2 TYPE 24th President of the United States\n2 DATE FROM 04 MAR 1885 TO 04 MAR 1889\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/Grover_Cleveland)",
            "1 EVEN William McKinley (1843 — 1901), Republican\n2 TYPE 25th President of the United States\n2 DATE FROM 04 MAR 1897 TO 14 SEP 1901\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/William_McKinley)",
            "1 EVEN Theodore Roosevelt (1858 — 1919), Republican\n2 TYPE 26th President of the United States\n2 DATE FROM 14 SEP 1901 TO 04 MAR 1909\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/Theodore_Roosevelt)",
            "1 EVEN William Howard Taft (1857 — 1930), Republican\n2 TYPE 27th President of the United States\n2 DATE FROM 04 MAR 1909 TO 04 MAR 1913\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/William_Howard_Taft)",
            "1 EVEN Woodrow Wilson (1856 — 1924), Democratic\n2 TYPE 28th President of the United States\n2 DATE FROM 04 MAR 1913 TO 04 MAR 1921\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/Woodrow_Wilson)",
            "1 EVEN Warren G. Harding(1865 — 1923), Republican \n2 TYPE 29th President of the United States\n2 DATE FROM 04 MAR 1921 TO 02 AUG 1923\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/Warren_G._Harding)",
            "1 EVEN Calvin Coolidge (1872 — 1933), Republican\n2 TYPE 30th President of the United States\n2 DATE FROM 02 AUG 1923 TO 04 MAR 1929\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/Calvin_Coolidge)",
            "1 EVEN Herbert Hoover (1874 — 1964), Republican\n2 TYPE 31st President of the United States\n2 DATE FROM 04 MAR 1929 TO 04 MAR 1933\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/Herbert_Hoover)",
            "1 EVEN Franklin D. Roosevelt (1882 — 1945), Democratic\n2 TYPE 32nd President of the United States\n2 DATE FROM 04 MAR 1933 TO 12 APR 1945\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/Franklin_D._Roosevelt)",
            "1 EVEN Harry S. Truman (1884 — 1972), Democratic\n2 TYPE 33rd President of the United States\n2 DATE FROM 12 APR 1945 TO 20 JAN 1953\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/Harry_S._Truman)",
            "1 EVEN Dwight D. Eisenhower (1890 — 1969), Republican\n2 TYPE 34th President of the United States\n2 DATE FROM 20 JAN 1953 TO 20 JAN 1961\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/Dwight_D._Eisenhower)",
            "1 EVEN John F. Kennedy (1917 — 1963), Democratic\n2 TYPE 35th President of the United States\n2 DATE FROM 20 JAN 1961 TO 22 NOV 1963\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/John_F._Kennedy)",
            "1 EVEN Lyndon B. Johnson (1908 — 1973), Democratic\n2 TYPE 36th President of the United States\n2 DATE FROM 22 NOV 1963 TO 20 JAN 1969\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/Lyndon_B._Johnson)",
            "1 EVEN Richard Nixon (1913 — 1994), Republican\n2 TYPE 37th President of the United States\n2 DATE FROM 20 JAN 1969 TO 09 AUG 1974\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/Richard_Nixon)",
            "1 EVEN Gerald Ford (1913 — 2006), Republican\n2 TYPE 38th President of the United States\n2 DATE FROM 09 AUG 1974 TO 20 JAN 1977\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/Gerald_Ford)",
            "1 EVEN Jimmy Carter(* 1924), Democratic \n2 TYPE 39th President of the United States\n2 DATE FROM 20 JAN 1977 TO 20 JAN 1981\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/Jimmy_Carter)",
            "1 EVEN Ronald Reagan (1911 — 2004, Republica), Republican\n2 TYPE 40th President of the United States\n2 DATE FROM 20 JAN 1981 TO 20 JAN 1989\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/Ronald_Reagan)",
            "1 EVEN George H. W. Bush (1924 — 2018), Republican\n2 TYPE 41st President of the United States\n2 DATE FROM 20 JAN 1989 TO 20 JAN 1993\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/George_H._W._Bush)",
            "1 EVEN Bill Clinton (* 1946), Democratic\n2 TYPE 42nd President of the United States\n2 DATE FROM 20 JAN 1993 TO 20 JAN 2001\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/Bill_Clinton)",
            "1 EVEN George W. Bush (* 1946), Republican\n2 TYPE 43rd President of the United States\n2 DATE FROM 20 JAN 2001 TO 20 JAN 2009\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/George_W._Bush)",
            "1 EVEN Barack Obama (* 1961), Democratic\n2 TYPE 44th President of the United States\n2 DATE FROM 20 JAN 2009 TO 20 JAN 2017\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/Barack_Obama)",
            "1 EVEN Donald Trump (* 1946), Republican\n2 TYPE 45th President of the United States\n2 DATE FROM 20 JAN 2017 TO 20 JAN 2021\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/Donald_Trump)",
            "1 EVEN Joe Biden (* 1942), Democratic \n2 TYPE 46th President of the United States\n2 DATE FROM 20 JAN 2021\n2 SOUR [Wikipedia: List of presidents of the United States](https://en.wikipedia.org/wiki/Joe_Biden)",
        ]);
    }
}
