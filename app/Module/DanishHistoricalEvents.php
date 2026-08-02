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

namespace Fisharebest\Webtrees\Module;

use Illuminate\Support\Collection;

class DanishHistoricalEvents extends AbstractModule implements ModuleHistoricEventsInterface
{
    use ModuleHistoricEventsTrait;

    public function title(): string
    {
        return 'Danske historiske begivenheder';
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
            case 'da':
                return new Collection([
                    // Historiske begivenheder
                    "1 EVEN 1. Verdenskrig \n2 TYPE Historiske begivenheder\n2 DATE BET 01 AUG 1914 AND 11 NOV 1918\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/1._verdenskrig )",
                    "1 EVEN 2. Verdenskrig \n2 TYPE Historiske begivenheder\n2 DATE BET 01 SEP 1939 AND 02 SEP 1945\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/2._verdenskrig )",
                    "1 EVEN 3 års krigen\n2 TYPE Historiske begivenheder\n2 DATE BET 24 MAR 1848 AND 25 JUL 1850\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/Tre%C3%A5rskrigen)",
                    "1 EVEN Slesvig krigen\n2 TYPE Historiske begivenheder\n2 DATE BET 01 FEB 1864 AND 20 JUL 1864\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/2._Slesvigske_Krig )",
                    // Danmarks Regent:
                    "1 EVEN Christian IV\n2 TYPE Danmarks Regent\n2 DATE FROM 04 APR 1588 TO 28 FEB 1648\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/Christian_4. )",
                    "1 EVEN Christian V\n2 TYPE Danmarks Regent\n2 DATE FROM 09 FEB 1670 TO 25 AUG 1699\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/Christian_5. )",
                    "1 EVEN Christian VI\n2 TYPE Danmarks Regent\n2 DATE FROM 12 OCT 1730 TO 06 AUG 1746\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/Christian_6. )",
                    "1 EVEN Christian VII\n2 TYPE Danmarks Regent\n2 DATE FROM 14 JAN 1766 TO 13 MAR 1808\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/Christian_7. )",
                    "1 EVEN Christian VIII\n2 TYPE Danmarks Regent\n2 DATE FROM 03 DEC 1839 TO 20 JAN 1848\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/Christian_8. )",
                    "1 EVEN Christian IX\n2 TYPE Danmarks Regent\n2 DATE FROM 15 NOV 1863 TO 29 JAN 1906\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/Christian_9. )",
                    "1 EVEN Christian X\n2 TYPE Danmarks Regent\n2 DATE FROM 14 MAY 1912 TO 20 APR 1947\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/Christian_10. )",
                    "1 EVEN Frederik III\n2 TYPE Danmarks Regent\n2 DATE FROM 06 JUL 1648 TO 09 FEB 1670\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/Frederik_3. )",
                    "1 EVEN Frederik IV\n2 TYPE Danmarks Regent\n2 DATE FROM 25 AUG 1699 TO 12 OCT 1730\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/Frederik_4. )",
                    "1 EVEN Frederik V\n2 TYPE Danmarks Regent\n2 DATE FROM 06 AUG 1746 - 14 JAN 1766\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/Frederik_5. )",
                    "1 EVEN Frederik VI\n2 TYPE Danmarks Regent\n2 DATE FROM 13 MAR 1808 TO 03 DEC 1839\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/Frederik_6. )",
                    "1 EVEN Frederik VII\n2 TYPE Danmarks Regent\n2 DATE FROM 20 JAN 1848 TO 15 NOV 1863\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/Frederik_7. )",
                    "1 EVEN Frederik VIII\n2 TYPE Danmarks Regent\n2 29 JAN 1906 TO 14 MAY 1912\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/Frederik_8. )",
                    "1 EVEN Frederik IX\n2 TYPE Danmarks Regent\n2 DATE FROM 20 APR 1947 TO 14 JAN 1972\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/Frederik_9. )",
                    "1 EVEN Margrethe II\n2 TYPE Danmarks Regent\n2 DATE FROM 14 JAN 1972 TO 14 JAN 2024\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/Margrethe_2. )",
                    "1 EVEN Frederik X\n2 TYPE Danmarks Regent\n2 DATE FROM 14 JAN 2024\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/Frederik_10. )",
                    // Regerinsgchef i Danmark:
                    "1 EVEN Adam Wilhelm Moltke (C)\n2 TYPE Regerinsgchef i Danmark\n2 DATE FROM 22 MAR 1848 TO 27 JAN 1852\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/Adam_Wilhelm_Moltke )",
                    "1 EVEN Christian Albrecht Bluhme (H)\n2 TYPE Regerinsgchef i Danmark\n2 DATE FROM 27 JAN 1852 TO 21 APR 1853\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/C.A._Bluhme )",
                    "1 EVEN Anders Sandøe Ørsted (H)\n2 TYPE Regerinsgchef i Danmark\n2 DATE FROM 21 APR 1853 TO 12 DEC 1854\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/A.S._Ørsted )",
                    "1 EVEN Peter Georg Bang (-)\n2 TYPE Regerinsgchef i Danmark\n2 DATE FROM 12 DEC 1854 TO 18 OCT 1856\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/P.G._Bang )",
                    "1 EVEN Carl Christopher Georg Andræ (-)\n2 TYPE Regerinsgchef i Danmark\n2 DATE FROM 18 OCT 1856 TO 13 MAY 1857\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/C.G._Andræ )",
                    "1 EVEN Carl Christian Hall (NL)\n2 TYPE Regerinsgchef i Danmark\n2 DATE FROM 13 MAY 1857 TO 02 DEC 1859\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/C.C._Hall )",
                    "1 EVEN Carl Christian Hall (NL)\n2 TYPE Regerinsgchef i Danmark\n2 DATE FROM 24 FEB 1860 TO 31 DEC 1863\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/C.C._Hall )",
                    "1 EVEN Ditlev Gothard Monrad (NL)\n2 TYPE Regerinsgchef i Danmark\n2 DATE FROM 31 DEC 1863 TO 11 JUL 1864\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/D.G._Monrad )",
                    "1 EVEN Christian Albrecht Bluhme (H)\n2 TYPE Regerinsgchef i Danmark\n2 DATE FROM 11 JUL 1864 TO 06 NOV 1865\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/C.A._Bluhme )",
                    "1 EVEN Christian Emil Krag-Juel-Vind-Frijs (H)\n2 TYPE Regerinsgchef i Danmark\n2 DATE FROM 06 NOV 1865 TO 28 MAY 1870\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/C.E._Frijs )",
                    "1 EVEN Ludvig Holstein-Holsteinborg (H)\n2 TYPE Regerinsgchef i Danmark\n2 DATE FROM 28 MAY 1870 TO 14 JUL 1874\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/Ludvig_Holstein-Holsteinborg )",
                    "1 EVEN Christen Andreas Fonnesbech (BN)\n2 TYPE Regerinsgchef i Danmark\n2 DATE FROM 14 JUL 1874 TO 1875\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/C.A._Fonnesbech )",
                    "1 EVEN Jacob Brønnum Scavenius Estrup (H)\n2 TYPE Regerinsgchef i Danmark\n2 DATE FROM 11 JUN 1875 TO 07 AUG 1894\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/J.B.S._Estrup )",
                    "1 EVEN Kjeld Thor Tage Otto Reedtz-Thott (H)\n2 TYPE Regerinsgchef i Danmark\n2 DATE FROM 07 AUG 1894 TO 23 MAY 1897\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/Tage_Reedtz-Thott )",
                    "1 EVEN Hugo Egmont Hørring (H)\n2 TYPE Regerinsgchef i Danmark\n2 DATE FROM 23 MAY 1897 TO 27 APR 1900\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/Hugo_Hørring )",
                    "1 EVEN Hannibal Sehested (RV)\n2 TYPE Regerinsgchef i Danmark\n2 DATE FROM 27 APR 1900 TO 24 JUL 1901\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/Hannibal_Sehested_(konseilspræsident) )",
                    // Statsminister i Danmark
                    "1 EVEN Johan Henrik Deuntzer (RV)\n2 TYPE Statsminister i Danmark\n2 DATE FROM 24 JUL 1901 TO 14 JAN 1905\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/Johan_Henrik_Deuntzer )",
                    "1 EVEN Jens Christian CHristensen (NL)\n2 TYPE Statsminister i Danmark\n2 DATE FROM 14 JAN 1905 TO 12 OCT 1908\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/J.C._Christensen )",
                    "1 EVEN Niels Thomasius Neergaard (NL)\n2 TYPE Statsminister i Danmark\n2 DATE FROM 12 OCT 1908 TO 16 AUG 1909\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/Niels_Neergaard )",
                    "1 EVEN Johan Ludvig (Louis) Carl Christian Tido (RV)\n2 TYPE Statsminister i Danmark\n2 DATE FROM 16 AUG 1909 TO 28 OCT 1909\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/Ludvig_Holstein-Ledreborg )",
                    "1 EVEN Carl Theodor Zahle (RV)\n2 TYPE Statsminister i Danmark\n2 DATE FROM 28 OCT 1909 TO 05 JUL 1910\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/C.Th._Zahle )",
                    "1 EVEN Klaus Berntsen (V)\n2 TYPE Statsminister i Danmark\n2 DATE FROM 05 JUL 1910 TO 21 JUN 1913\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/Klaus_Berntsen )",
                    "1 EVEN Carl Theodor Zahle (-)\n2 TYPE Statsminister i Danmark\n2 DATE FROM 21 APR 1913 TO 30 MAR 1920\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/C.Th._Zahle )",
                    "1 EVEN Carl Julius Otto Liebe (-)\n2 TYPE Statsminister i Danmark\n2 DATE FROM 30 MAR 1920 TO 05 APR 1920\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/Otto_Liebe )",
                    "1 EVEN Michael Petersen Friis (-)\n2 TYPE Statsminister i Danmark\n2 DATE FROM 05 APR 1920 TO 05 MAY 1920\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/M.P._Friis )",
                    "1 EVEN Niels Thomasius Neergaard (NL)\n2 TYPE Statsminister i Danmark\n2 DATE FROM 05 MAY 1920 TO 23 APR 1924\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/Niels_Neergaard )",
                    "1 EVEN Thorvald August Marinus Stauning (NL)\n2 TYPE Statsminister i Danmark\n2 DATE FROM 24 APR 1924 TO 14 DEC 1926\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/Thorvald_Stauning )",
                    "1 EVEN Thomas Madsen-Mygdalg (V)\n2 TYPE Statsminister i Danmark\n2 DATE FROM 14 DEC 1926 TO 30 APR 1929\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/Thomas_Madsen-Mygdal )",
                    "1 EVEN Thorvald August Marinus Stauning (NL)\n2 TYPE Statsminister i Danmark\n2 DATE FROM 30 APR 1929 TO 03 MAY 1942\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/Thorvald_Stauning )",
                    "1 EVEN Vilhelm Buhl (NL)\n2 TYPE Statsminister i Danmark\n2 DATE FROM 04 MAY 1942 TO 09 NOV 1942\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/Vilhelm_Buhl )",
                    "1 EVEN Erik Julius Christian Scavenius (NL)\n2 TYPE Statsminister i Danmark\n2 DATE FROM 09 NOV 1942 TO 29 AUG 1943\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/Erik_Scavenius )",
                    "1 EVEN Vilhelm Buhl (NL)\n2 TYPE Statsminister i Danmark\n2 DATE FROM 05 MAY 1945 TO 07 NOV 1945\n2 NOTE [wikipedia da](https://da.wikipedia.org/wiki/Vilhelm_Buhl )",
                ]);

            default:
                return new Collection();
        }
    }
}
