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
 * Class HistoryPresidentsAustrian
 */
class HistoryPresidentsAustrian extends AbstractModule implements ModuleHistoricEventsInterface
{
    use ModuleHistoricEventsTrait;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        return 'Bundespräsidenten Österreichs 🇦🇹';
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
            "1 EVEN Franz Seraph Dinghofer (1873 — 1956), NSDAP\n2 TYPE Gleichberechtigter Präsident der Provisorischen Nationalversammlung\n2 DATE FROM 21 OCT 1918 TO 16 FEB 1919\n2 NOTE Provisorische Nationalversammlung für Deutschösterreich\n2 SOUR [Wikipedia; Provisorische Nationalversammlung für Deutschösterreich](https://de.wikipedia.org/wiki/Franz_Dinghofer)",
            "1 EVEN Jodok Fink (1853 — 1929), CS\n2 TYPE Gleichberechtigter Präsident der Provisorischen Nationalversammlung\n2 DATE FROM 21 OCT 1918 TO 16 FEB 1919\n2 NOTE Provisorische Nationalversammlung für Deutschösterreich\n2 SOUR [Wikipedia; Provisorische Nationalversammlung für Deutschösterreich](https://de.wikipedia.org/wiki/Jodok_Fink)",
            "1 EVEN Karl Josef Seitz (1869 — 1950), SDAP\n2 TYPE Gleichberechtigter Präsident der Provisorischen Nationalversammlung\n2 DATE FROM 21 OCT 1918 TO 16 FEB 1919\n2 NOTE Provisorische Nationalversammlung für Deutschösterreich\n2 SOUR [Wikipedia; Provisorische Nationalversammlung für Deutschösterreich](https://de.wikipedia.org/wiki/Karl_Seitz)",
            "1 EVEN Karl Josef Seitz (1869 — 1950), SDAP\n2 TYPE Präsident der Konstituierenden Nationalversammlung\n2 DATE FROM 16 FEB 1919 TO 01 OCT 1920\n2 NOTE Konstituierende Nationalversammlung\n2 SOUR [Wikipedia; Konstituierende Nationalversammlung](https://de.wikipedia.org/wiki/Karl_Seitz)",
            "1 EVEN Karl Josef Seitz (1869 — 1950), SDAP\n2 TYPE Bundespräsident\n2 DATE FROM 01 OCT 1920 TO 09 DEC 1920\n2 NOTE Erste Republik Österreich (1919-1934)\n2 SOUR [Wikipedia; Bundespräsident (Österreich)](https://de.wikipedia.org/wiki/Karl_Seitz)",
            "1 EVEN Michael Arthur Josef Jakob Hainisch (1858 — 1940), parteilos\n2 TYPE Bundespräsident\n2 DATE FROM 09 DEC 1920 TO 10 DEC 1928\n2 NOTE Erste Republik Österreich (1919-1934)\n2 SOUR [Wikipedia; Liste der Bundespräsidenten der Republik Österreich](https://de.wikipedia.org/wiki/Michael_Hainisch)",
            "1 EVEN Wilhelm Miklas (1872 — 1956), CS/VF\n2 TYPE Bundespräsident\n2 DATE FROM 10 DEC 1928 TO 13 MAR 1938\n2 NOTE Erste Republik Österreich (1919-1934)\n2 SOUR [Wikipedia; Liste der Bundespräsidenten der Republik Österreich](https://de.wikipedia.org/wiki/Wilhelm_Miklas)",
            "1 EVEN Adolf Hitler (1889 — 1945), NSDAP\n2 TYPE Führer und Reichskanzler\n2 DATE FROM 13 MAR 1938 TO 30 APR 1945\n2 NOTE Anschluss am Großdeutschen Reich\n2 SOUR [Wikipedia: Weimarer Republik](https://de.wikipedia.org/wiki/Adolf_Hitler)",
            "1 EVEN Karl Renner (1870 — 1950), SPÖ\n2 TYPE Bundespräsident\n2 DATE FROM 20 DEC 1945 TO 31 DEC 1950\n2 NOTE Zweite Republik Österreich\n2 SOUR [Wikipedia: Liste der Bundespräsidenten der Republik Österreich](https://de.wikipedia.org/wiki/Karl_Renner)",
            "1 EVEN Theodor Körner (1873 — 1957), SPÖ\n2 TYPE Bundespräsident\n2 DATE FROM 21 JUN 1951 TO 04 JAN 1957\n2 NOTE Zweite Republik Österreich\n2 SOUR [Wikipedia: Liste der Bundespräsidenten der Republik Österreich](https://de.wikipedia.org/wiki/Theodor_Körner_(Bundespräsident))",
            "1 EVEN Adolf Schärf (1890 — 1965), SPÖ\n2 TYPE Bundespräsident\n2 DATE FROM 22 MAY 1957 TO 28 FEB 1965\n2 NOTE Zweite Republik Österreich\n2 SOUR [Wikipedia: Liste der Bundespräsidenten der Republik Österreich](https://de.wikipedia.org/wiki/Adolf_Schärf)",
            "1 EVEN Franz Josef Jonas (1899 — 1974), SPÖ\n2 TYPE Bundespräsident\n2 DATE FROM 09 JUN 1965 TO 24 APR 1974\n2 NOTE Zweite Republik Österreich\n2 SOUR [Wikipedia: Liste der Bundespräsidenten der Republik Österreich](https://de.wikipedia.org/wiki/Franz_Jonas)",
            "1 EVEN Rudolf Kirchschläger (1915 — 2000), parteilos\n2 TYPE Bundespräsident\n2 DATE FROM 08 JUL 1974 TO 08 JUL 1986\n2 NOTE Zweite Republik Österreich\n2 SOUR [Wikipedia: Liste der Bundespräsidenten der Republik Österreich](https://de.wikipedia.org/wiki/Rudolf_Kirchschläger)",
            "1 EVEN Kurt Josef Waldheim (1918 — 2007), parteilos\n2 TYPE Bundespräsident\n2 DATE FROM 08 JUL 1986 TO 08 JUL 1992\n2 NOTE Zweite Republik Österreich\n2 SOUR [Wikipedia: Liste der Bundespräsidenten der Republik Österreich](https://de.wikipedia.org/wiki/Kurt_Waldheim)",
            "1 EVEN Thomas Klestil (1932 — 2004), ÖVP\n2 TYPE Bundespräsident\n2 DATE FROM 08 JUL 1992 TO 06 JUL 2004\n2 NOTE Zweite Republik Österreich\n2 SOUR [Wikipedia: Liste der Bundespräsidenten der Republik Österreich](https://de.wikipedia.org/wiki/Thomas_Klestil)",
            "1 EVEN Heinz Fischer (* 1938), SPÖ\n2 TYPE Bundespräsident\n2 DATE FROM 08 JUL 2004 TO 08 JUL 2016\n2 NOTE Zweite Republik Österreich\n2 SOUR [Wikipedia: Liste der Bundespräsidenten der Republik Österreich](https://de.wikipedia.org/wiki/Heinz_Fischer)",
            "1 EVEN Alexander Van der Bellen (* 1944), parteilos\n2 TYPE Bundespräsident\n2 DATE FROM 26 JAN 2017\n2 NOTE Zweite Republik Österreich\n2 SOUR [Wikipedia: Liste der Bundespräsidenten der Republik Österreich](https://de.wikipedia.org/wiki/Alexander_Van_der_Bellen)",
        ]);
    }
}
