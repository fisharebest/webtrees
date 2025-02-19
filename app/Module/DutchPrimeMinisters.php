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

class DutchPrimeMinisters extends AbstractModule implements ModuleHistoricEventsInterface
{
    use ModuleHistoricEventsTrait;

    public function title(): string
    {
        return 'Lijst van premiers en kabinetten van Nederland 🇳🇱';
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
            case 'nl':
                return new Collection([
                    "1 EVEN Gerrit graaf Schimmelpenninck\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 25 MAR 1848 TO 21 NOV 1848\n2 NOTE kabinet-Schimmelpenninck",
                    "1 EVEN Jacob Mattheus de Kempenaer\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 21 NOV 1848 TO 01 NOV 1849\n2 NOTE kabinet-De Kempenaer-Donker Curtius",
                    "1 EVEN Johan Rudolph Thorbecke\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 01 NOV 1849 TO 19 APR 1853\n2 NOTE kabinet-Thorbecke I",
                    "1 EVEN Floris Adriaan van Hall\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 19 APR 1853 TO 01 JUL 1856\n2 kabinet-Van Hall-Donker Curtius",
                    "1 EVEN Justinus Jacobus Leonard van der Brugghen\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 01 JUL 1856 TO 18 MAR 1858\n2 kabinet-Van der Brugghen",
                    "1 EVEN Jan Jacob Rochussen\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 18 MAR 1858 TO 23 FEB 1860\n2 kabinet-Rochussen",
                    "1 EVEN Floris Adriaan van Hall\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 23 FEB 1860 TO 14 MAR 1861\n2 kabinet-Van Hall-Van Heemstra",
                    "1 EVEN Pieter Pompejus baron van Zuylen van Nijevelt\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 14 MAR 1861 TO 10 NOV 1861\n2 kabinet-Van Zuylen van Nijevelt-Van Heemstra",
                    "1 EVEN Schelto baron van Heemstra\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 10 NOV 1861 TO 01 FEB 1862\n2 kabinet-Van Zuylen van Nijevelt-Van Heemstra",
                    "1 EVEN Johan Rudolph Thorbecke\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 01 FEB 1862 TO 10 FEB 1866\n2 kabinet-Thorbecke II",
                    "1 EVEN Isaäc Dignus Fransen van de Putte\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 10 FEB 1866 TO 01 JUN 1866\n2 kabinet-Fransen van de Putte",
                    "1 EVEN Julius Philip Jacob Adriaan graaf van Zuylen van Nijevelt\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 01 JUN 1866 TO 04 JUN 1868\n2 kabinet-Van Zuylen van Nijevelt",
                    "1 EVEN Pieter Philip van Bosse\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 04 JUN 1868 TO 04 JAN 1871\n2 kabinet-Van Bosse-Fock",
                    "1 EVEN Johan Rudolph Thorbecke\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 04 JAN 1871 TO 04 JUN 1872\n2 kabinet-Thorbecke III",
                    "1 EVEN Gerrit de Vries Azn.\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 06 JUL 1872 TO 27 AUG 1874\n2 kabinet-De Vries-Fransen van de Putte",
                    "1 EVEN Jan Heemskerk Azn.\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 27 AUG 1874 TO 03 NOV 1877\n2 kabinet-Heemskerk-Van Lynden van Sandenburg",
                    "1 EVEN Johannes (Jan) Kappeyne van de Coppello n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 03 NOV 1877 TO 20 AUG 1879\n2 kabinet-Kappeyne van de Coppello",
                    "1 EVEN Constant Théodore graaf (Theo) van Lynden van Sandenburg\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 20 AUG 1879 TO 23 APR 1883\n2 kabinet-Van Lynden van Sandenburg",
                    "1 EVEN Æneas baron Mackay\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 21 APR 1888 TO 21 AUG 1891\n2 kabinet-Mackay",
                    "1 EVEN Gijsbert van Tienhoven\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 21 AUG 1891 TO 09 MAY 1894\n2 kabinet-Van Tienhoven",
                    "1 EVEN Jhr. Joan Röell\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 09 MAI 1894 TO 27 JUL 1897\n2 kabinet-Röell",
                    "1 EVEN Nicolaas Gerard Pierson\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 27 JUL 1897 TO 01 AUG 1901\n2 kabinet-Pierson",
                    "1 EVEN Abraham Kuyper\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 01 AUG 1901 TO 17 AUG 1905\n2 kabinet-Kuyper",
                    "1 EVEN Theodoor Herman (Theo) de Meester\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 17 AUG 1905 TO 12 FEB 1908\n2 kabinet-De Meester",
                    "1 EVEN Theodorus (Theo) Heemskerk\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 12 FEB 1908 TO 29 AUG 1913\n2 kabinet-Heemskerk",
                    "1 EVEN Pieter Wilhelm Adrianus Cort van der Linden\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 29 AUG 1913 TO 09 SEP 1918\n2 kabinet-Cort van der Linden",
                    "1 EVEN Jhr. Charles Joseph Marie Ruijs de Beerenbrouck\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 09 SEP 1918 TO 18 SEP 1922\n2 kabinet-Ruijs de Beerenbrouck I",
                    "1 EVEN Jhr. Charles Joseph Marie Ruijs de Beerenbrouck\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 18 SEP 1922 TO 04 AUG 1925\n2 kabinet-Ruijs de Beerenbrouck II",
                    "1 EVEN Hendrikus Colijn\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 04 AUG 1925 TO 08 MAR 1926\n2 kabinet-Colijn I",
                    "1 EVEN Jhr. Dirk Jan de Geer\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 08 MAR 1926 TO 10 AUG 1929\n2 kabinet-De Geer I",
                    "1 EVEN Jhr. Charles Joseph Marie Ruijs de Beerenbrouck\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 10 AUG 1929 TO 26 MAY 1933\n2 kabinet-Ruijs de Beerenbrouck III",
                    "1 EVEN Hendrikus Colijn\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 26 MAY 1933 TO 31 JUL 1935\n2 kabinet-Colijn II",
                    "1 EVEN Hendrikus Colijn\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 31 JUL 1935 TO 24 JUN 1937\n2 kabinet-Colijn III",
                    "1 EVEN Hendrikus Colijn\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 24 JUN 1937 TO 25 JUL 1939\n2 kabinet-Colijn IV",
                    "1 EVEN Hendrikus Colijn\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 25 JUL 1939 TO 10 AUG 1939\n2 kabinet-Colijn V",
                    "1 EVEN Jhr. Dirk Jan de Geer\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 10 AUG 1939 TO 03 SEP 1940\n2 kabinet-De Geer II (Londens kabinet in ballingschap)",
                    "1 EVEN Pieter Sjoerds Gerbrandy\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 03 SEP 1940 TO 27 JUL 1941\n2 kabinet-Gerbrandy I (Londens kabinet in ballingschap)",
                    "1 EVEN Pieter Sjoerds Gerbrandy\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 27 JUL 1941 TO 23 FEB 1945\n2 kabinet-Gerbrandy II (Londens kabinet in ballingschap)",
                    "1 EVEN Pieter Sjoerds Gerbrandy\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 23 FEB 1945 TO 25 JUN 1945\n2 kabinet-Gerbrandy III (Londens kabinet in ballingschap)",
                    "1 EVEN Willem Schermerhorn\n2 TYPE Minister-President\n2 DATE FROM 25 JUN 1945 TO 03 JUL 1946\n2 kabinet-Schermerhorn-Drees",
                    "1 EVEN Louis Joseph Maria Beel\n2 TYPE Minister-President\n2 DATE FROM 03 JUL 1946 TO 07 AUG 1948\n2 kabinet-Beel I",
                    "1 EVEN Willem Drees\n2 TYPE Minister-President\n2 DATE FROM 07 AUG 1948 TO 15 MAR 1951\n2 kabinet-Drees Van Schaik",
                    "1 EVEN Willem Drees\n2 TYPE Minister-President\n2 DATE FROM 15 MAR 1951 TO 02 SEP 1952\n2 kabinet-Drees I",
                    "1 EVEN Willem Drees\n2 TYPE Minister-President\n2 DATE FROM 02 SEP 1952 TO 13 OCT 1956\n2 kabinet-Drees II",
                    "1 EVEN Willem Drees\n2 TYPE Minister-President\n2 DATE FROM 13 OCT 1956 TO 22 DEC 1958\n2 kabinet-Drees III",
                    "1 EVEN Louis Joseph Maria Beel\n2 TYPE Minister-President\n2 DATE FROM 22 DEC 1958 TO 19 MAY 1959\n2 kabinet-Beel II",
                    "1 EVEN Jan Eduard de Quay\n2 TYPE Minister-President\n2 DATE FROM 19 MAY 1959 TO 24 JUL 1963\n2 kabinet-De Quay",
                    "1 EVEN Victor Gerard Marie Marijnen\n2 TYPE Minister-President\n2 DATE FROM 24 JUL 1963 TO 14 APR 1965\n2 kabinet-Marijnen",
                    "1 EVEN Jozef Maria Laurens Theo (Jo) Cals\n2 TYPE Minister-President\n2 DATE FROM 14 APR 1965 TO 22 NOV 1966\n2 kabinet-Cals",
                    "1 EVEN Jelle Zijlstra\n2 TYPE Minister-President\n2 DATE FROM 22 NOV 1966 TO 05 APR 1967\n2 kabinet-Zijlstra",
                    "1 EVEN Petrus Jozef Sietse (Piet) de Jong\n2 TYPE Minister-President\n2 DATE FROM 05 APR 1967 TO 06 JUL 1971\n2 kabinet-De Jong",
                    "1 EVEN Barend Willem Biesheuvel\n2 TYPE Minister-President\n2 DATE FROM 06 JUL 1971 TO 09 AUG 1972\n2 kabinet-Biesheuvel I",
                    "1 EVEN Barend Willem Biesheuvel\n2 TYPE Minister-President\n2 DATE FROM 09 AUG 1972 TO 11 MAY 1973\n2 kabinet-Biesheuvel II",
                    "1 EVEN Johannes Marten (Joop) den Uijl n2 TYPE Minister-President\n2 DATE FROM 11 MAY 1973 TO 19 DEC 1977\n2 kabinet-Den Uyl",
                    "1 EVEN Andreas Antonius Maria (Dries) van Agt\n2 TYPE Minister-President\n2 DATE FROM 19 DEC 1977 TO 11 SEP 1981\n2 kabinet-Van Agt I",
                    "1 EVEN Andreas Antonius Maria (Dries) van Agt\n2 TYPE Minister-President\n2 DATE FROM 11 SEP 1981 TO 29 MAY 1982\n2 kabinet-Van Agt II",
                    "1 EVEN Andreas Antonius Maria (Dries) van Agt\n2 TYPE Minister-President\n2 DATE FROM 29 MAY 1982 TO 04 NOV 1982\n2 kabinet-Van Agt III",
                    "1 EVEN Rudolphus Franciscus Marie (Ruud) Lubbers\n2 TYPE Minister-President\n2 DATE FROM 04 NÒV 1982 TO 14 JUL 1986\n2 kabinet-Lubbers I",
                    "1 EVEN Rudolphus Franciscus Marie (Ruud) Lubbers\n2 TYPE Minister-President\n2 DATE FROM 14 JUL 1986 TO 07 NOV 1989\n2 kabinet-Lubbers II",
                    "1 EVEN Rudolphus Franciscus Marie (Ruud) Lubbers\n2 TYPE Minister-President\n2 DATE FROM 07 NOV 1989 TO 22 AUG 1994\n2 kabinet-Lubbers III",
                    "1 EVEN Willem (Wim) Kok\n2 TYPE Minister-President\n2 DATE FROM 22 AUG 1994 TO 03 AUG 1998\n2 kabinet-Kok I",
                    "1 EVEN Willem (Wim) Kok\n2 TYPE Minister-President\n2 DATE FROM 03 AUG 1998 TO 22 JUL 2002\n2 kabinet-Kok II",
                    "1 EVEN Jan Pieter (Jan Peter) Balkenende\n2 TYPE Minister-President\n2 DATE FROM 22 JUL 2002 TO 27 MAY 2003\n2 kabinet-Balkenende I",
                    "1 EVEN Jan Pieter (Jan Peter) Balkenende\n2 TYPE Minister-President\n2 DATE FROM 27 MAY 2003 TO 07 JUL 2006\n2 kabinet-Balkenende II",
                    "1 EVEN Jan Pieter (Jan Peter) Balkenende\n2 TYPE Minister-President\n2 DATE FROM 07 JUL 2006\ TO 22 FEB 2007\n2 kabinet-Balkenende III",
                    "1 EVEN Jan Pieter (Jan Peter) Balkenende\n2 TYPE Minister-President\n2 DATE FROM 22 FEB 2007 TO 26 OCT 2010\n2 kabinet-Balkenende IV",
                    "1 EVEN Mark Rutte\n2 TYPE Minister-President\n2 DATE FROM 26 OCT 2010 TO 05 NOV 2012\n2 kabinet-Rutte I",
                    "1 EVEN Mark Rutte\n2 TYPE Minister-President\n2 DATE FROM 05 NOV 2012 TO 26 OCT 2017\n2 kabinet-Rutte II",
                    "1 EVEN Mark Rutte\n2 TYPE Minister-President\n2 DATE FROM 26 OCT 2017 TO 10 JAN 2022\n2 kabinet-Rutte III",
                    "1 EVEN Mark Rutte\n2 TYPE Minister-President\n2 DATE FROM 10 JAN 2022 TO 02 JUL 2024\n2 kabinet-Rutte IV",
                    "1 EVEN Dick Schoof\n2 TYPE Minister-President\n2 DATE FROM 02 JUL 2024\n2 kabinet-Schoof",
                ]);

            default:
                return new Collection();
        }
    }
}
