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

use Fisharebest\Webtrees\I18N;
use Illuminate\Support\Collection;

/**
 * Events provided by this module.
 * [EN] Historical facts (in German language):
 *      The Chancellors of Germany (since 1949) and their historical Predecessors (1867-1949)
 * [DE] Historische Daten (in deutscher Sprache):
 *      Die Bundeskanzler der BRD und Staatratsvorsitzenden der DDR (seit 1949) sowie deren historischen Vorgänger (1867-1949)
 *
 * @Author Lars van Ravenzwaaij
 * @Author Hermann Hartenthaler
 */

/**
 * Class HistoryChancellorsGerman
 */
class HistoryChancellorsGerman extends AbstractModule implements ModuleHistoricEventsInterface
{
    use ModuleHistoricEventsTrait;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        $part_title = I18N::translate('German chancellorships since');

        return $part_title . " 1867 🇩🇪";
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        return I18N::translate('Historical facts (in German): Chancellors of Germany (since 1949) and their historical Predecessors (1867 - 1949)');
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
        /**
        * Variables used for "TYPE" in Collection.
        *
        * @return string
        */
        $event_type_01 = I18N::translate('Imperial Chancellor');                                          // "Reichskanzler"
        $event_type_02 = I18N::translate('Chairman of the Revolutionary Council of Peoples Deputies');    // "Vorsitzenden des revolutionären Rats der Volksbeauftragten"
        $event_type_03 = I18N::translate('Leader and Imperial chancellor');                               // "Führer und Reichskanzler"
        $event_type_04 = I18N::translate('Occupation Force');                                             // "Besatzungsmacht"
        $event_type_05 = I18N::translate('Chancellor');                                                   // "Bundeskanzler"
        $event_type_06 = I18N::translate('Chancellor');                                                   // "Bundeskanzlerin"
        $event_type_07 = I18N::translate('Acting Chancellor');                                            // "Geschäftsführender Bundeskanzler"
        $event_type_08 = I18N::translate('President of the Republic');                                    // "Präsident der Republik"
        $event_type_09 = I18N::translate('Chairman of the Council of State');                             // "Vorsitzender des Staatsrats"

        /**
         * Each line is a GEDCOM style record to describe an event, including newline chars (\n)
         *     1 EVEN <description>
         *     2 TYPE <description>
         *     2 DATE <date period>
         *     2 NOTE <remark> to EVEN
         *     2 SOUR [Wikipedia: <title>](<link> )
         *
         * As Markdown is used for "NOTE", Markdown should be enabled for your tree (see
         * Control panel / Manage family trees / Preferences and then scroll down to "Text"
         * and mark the option "markdown"). If markdown is disabled the links are still
         * working with a necessary blank at the end, but the formatting isn't so nice.
         */
        return new Collection([
            "1 EVEN Fürst Otto von Bismarck (1815 — 1898\n2 TYPE "{$event_type_05}"\n2 DATE FROM 14 JUL 1867 TO 04 MAY 1871\n2 NOTE Norddeutscher Bund\n2 SOUR [Wikipedia: Bundeskanzler (Norddeutscher Bund)](https://de.wikipedia.org/wiki/Bundeskanzler_(Norddeutscher_Bund) )",
            "1 EVEN Fürst Otto von Bismarck (1815 — 1898\n2 TYPE "{$event_type_01}"\n2 DATE FROM 04 MAY 1871 TO 20 MAR 1890\n2 NOTE Deutsches Kaiserreich\n2 SOUR [Wikipedia: Reichskanzler (Deutsches Kaiserreich)](https://de.wikipedia.org/wiki/Otto_von_Bismarck )",
            "1 EVEN Graf Leo von Caprivi (1831 — 1899)\n2 TYPE "{$event_type_01}"\n2 DATE FROM 20 MAR 1890 TO 26 OCT 1894\n2 NOTE Deutsches Kaiserreich\n2 SOUR [Wikipedia: Reichskanzler (Deutsches Kaiserreich)](https://de.wikipedia.org/wiki/Leo_von_Caprivi )",
            "1 EVEN Fürst Chlodwig zu Hohenlohe-Schillingsfürst (1819 — 1901)\n2 TYPE "{$event_type_01}"\n2 DATE FROM 29 OCT 1894 TO 17 OCT 1900\n2 NOTE Deutsches Kaiserreich\n2 SOUR [Wikipedia: Reichskanzler (Deutsches Kaiserreich)](https://de.wikipedia.org/wiki/Chlodwig_zu_Hohenlohe-Schillingsfürst )",
            "1 EVEN Fürst Bernhard von Bülow (1849 — 1929)\n2 TYPE "{$event_type_01}"\n2 DATE FROM 17 OCT 1900 TO 14 JUL 1909\n2 NOTE Deutsches Kaiserreich\n2 SOUR [Wikipedia: Reichskanzler (Deutsches Kaiserreich)](https://de.wikipedia.org/wiki/Bernhard_von_Bülow )",
            "1 EVEN Theobald von Bethmann Hollweg (1856 — 1921)\n2 TYPE "{$event_type_01}"\n2 DATE FROM 14 JUL 1909 TO 13 JUL 1917\n2 NOTE Deutsches Kaiserreich\n2 SOUR [Wikipedia: Reichskanzler (Deutsches Kaiserreich)](https://de.wikipedia.org/wiki/Theobald_von_Bethmann_Hollweg )",
            "1 EVEN Georg Michaelis (1857 — 1936)\n2 TYPE "{$event_type_01}"\n2 DATE FROM 13 JUL 1917 TO 01 NOV 1917\n2 NOTE Deutsches Kaiserreich\n2 SOUR [Wikipedia: Reichskanzler (Deutsches Kaiserreich)](https://de.wikipedia.org/wiki/Georg_Michaelis )",
            "1 EVEN Graf Georg von Hertling (1843 — 1919)\n2 TYPE "{$event_type_01}"\n2 DATE FROM 01 NOV 1917 TO 30 SEP 1918\n2 NOTE Deutsches Kaiserreich\n2 SOUR [Wikipedia: Reichskanzler (Deutsches Kaiserreich)](https://de.wikipedia.org/wiki/Georg_von_Hertling )",
            "1 EVEN Prinz Max von Baden (1867 — 1929)\n2 TYPE "{$event_type_01}"\n2 DATE FROM 03 OCT 1918 TO 09 NOV 1918\n2 NOTE Deutsches Kaiserreich\n2 SOUR [Wikipedia: Reichskanzler (Deutsches Kaiserreich)](https://de.wikipedia.org/wiki/Max_von_Baden )",
            "1 EVEN Friedrich Ebert (1871 — 1925), SPD\n2 TYPE "{$event_type_02}"\n2 DATE FROM 10 NOV 1918 TO 11 FEB 1919\n2 NOTE Provisorische Regierung Deutschlands\n2 SOUR [Wikipedia: Rat der Volksbeauftragten](https://de.wikipedia.org/wiki/Friedrich_Ebert )",
            "1 EVEN Philipp Heinrich Scheidemann (1865 — 1939), SPD\n2 TYPE "{$event_type_01}"\n2 DATE FROM 13 FEB 1919 TO 20 JUN 1919\n2 NOTE Weimarer Republik\n2 SOUR [Wikipedia: Weimarer Republik](https://de.wikipedia.org/wiki/Philipp_Scheidemann )",
            "1 EVEN Gustav Adolf Bauer (1870 — 1944), SPD\n2 TYPE "{$event_type_01}"\n2 DATE FROM 21 JUN 1919 TO 26 MAR 1920\n2 NOTE Weimarer Republik\n2 SOUR [Wikipedia: Weimarer Republik](https://de.wikipedia.org/wiki/Gustav_Bauer )",
            "1 EVEN Hermann Müller (1876 — 1931), SPD\n2 TYPE "{$event_type_01}"\n2 DATE FROM 27 MAR 1920 TO 08 JUN 1920\n2 NOTE Weimarer Republik\n2 SOUR [Wikipedia: Weimarer Republik](https://de.wikipedia.org/wiki/Hermann_Müller_(Reichskanzler) )",
            "1 EVEN Constantin Fehrenbach (1852 — 1926), Zentrum\n2 TYPE "{$event_type_01}"\n2 DATE FROM 25 JUN 1920 TO 04 MAY 1921\n2 NOTE Weimarer Republik\n2 SOUR [Wikipedia: Weimarer Republik](https://de.wikipedia.org/wiki/Constantin_Fehrenbach )",
            "1 EVEN Karl Joseph Wirth (1879 — 1956), Zentrum\n2 TYPE "{$event_type_01}"\n2 DATE FROM 10 MAY 1921 TO 14 NOV 1922\n2 NOTE Weimarer Republik\n2 SOUR [Wikipedia: Weimarer Republik](https://de.wikipedia.org/wiki/Joseph_Wirth )",
            "1 EVEN Carl Josef Wilhelm Cuno (1876 — 1933), parteilos\n2 TYPE "{$event_type_01}"\n2 DATE FROM 22 NOV 1922 TO 12 AUG 1923\n2 NOTE Weimarer Republik\n2 SOUR [Wikipedia: Weimarer Republik](https://de.wikipedia.org/wiki/Wilhelm_Cuno )",
            "1 EVEN Gustav Ernst Stresemann (1878 — 1929), DVP\n2 TYPE "{$event_type_01}"\n2 DATE FROM 13 AUG 1923 TO 23 NOV 1923\n2 NOTE Weimarer Republik\n2 SOUR [Wikipedia: Weimarer Republik](https://de.wikipedia.org/wiki/Gustav_Stresemann )",
            "1 EVEN Wilhelm Marxm (1863 — 1946), Zentru\n2 TYPE "{$event_type_01}"\n2 DATE FROM 30 NOV 1923 TO 15 JAN 1925\n2 NOTE Weimarer Republik\n2 SOUR [Wikipedia: Weimarer Republik](https://de.wikipedia.org/wiki/Wilhelm_Marx )",
            "1 EVEN Hans Luther (1879 — 1962), parteilos\n2 TYPE "{$event_type_01}"\n2 DATE FROM 15 JAN 1925 TO 16 MAY 1926\n2 NOTE Weimarer Republik\n2 SOUR [Wikipedia: Weimarer Republik](https://de.wikipedia.org/wiki/Hans_Luther )",
            "1 EVEN Wilhelm Marx (1863 — 1946), Zentrum\n2 TYPE "{$event_type_01}"\n2 DATE FROM 16 MAY 1926 TO 28 JUN 1928\n2 NOTE Weimarer Republik\n2 SOUR [Wikipedia: Weimarer Republik](https://de.wikipedia.org/wiki/Wilhelm_Marx )",
            "1 EVEN Hermann Müller (1876 — 1931), SPD\n2 TYPE "{$event_type_01}"\n2 DATE FROM 28 JUN 1928 TO 30 MAR 1930\n2 NOTE Weimarer Republik\n2 SOUR [Wikipedia: Weimarer Republik](https://de.wikipedia.org/wiki/Hermann_Müller_(Reichskanzler) )",
            "1 EVEN Heinrich Aloysius Maria Elisabeth Brüning (1885 — 1970), Zentrum\n2 TYPE "{$event_type_01}"\n2 DATE FROM 30 MAR 1930 TO 31 MAY 1932\n2 NOTE Weimarer Republik\n2 SOUR [Wikipedia: Weimarer Republik](https://de.wikipedia.org/wiki/Heinrich_Brüning )",
            "1 EVEN Franz Joseph Hermann Michael Maria von Papen (1879 — 1969), parteilos\n2 TYPE "{$event_type_01}"\n2 DATE FROM 01 JUN 1932 TO 01 DEC 1932\n2 NOTE Weimarer Republik\n2 SOUR [Wikipedia: Weimarer Republik](https://de.wikipedia.org/wiki/Franz_von_Papen )",
            "1 EVEN Kurt Ferdinand Friedrich Hermann von Schleicher (1882 — 1934), parteilos\n2 TYPE "{$event_type_01}"\n2 DATE FROM 03 DEC 1932 TO 28 JAN 1933\n2 NOTE Weimarer Republik\n2 SOUR [Wikipedia: Weimarer Republik](https://de.wikipedia.org/wiki/Kurt_von_Schleicher )",
            "1 EVEN Adolf Hitler (1889 — 1945), NSDAP\n2 TYPE "{$event_type_01}"\n2 DATE FROM 30 JAN 1933 TO 01 AUG 1933\n2 NOTE Großdeutsches Reich\n2 SOUR [Wikipedia: Weimarer Republik](https://de.wikipedia.org/wiki/Adolf_Hitler )",
            "1 EVEN Adolf Hitler (1889 — 1945), NSDAP\n2 TYPE "{$event_type_03}"\n2 DATE FROM 01 AUG 1933 TO 30 APR 1945\n2 NOTE Großdeutsches Reich\n2 SOUR [Wikipedia: Weimarer Republik](https://de.wikipedia.org/wiki/Adolf_Hitler )",
            "1 EVEN Alliierter Kontrollrat für Deutschland\n2 TYPE "{$event_type_04}"\n2 DATE FROM 30 JUL 1945 TO 20 MAR 1948\n2 NOTE Besetztes Deutschland\n2 SOUR [Wikipedia: Deutschland 1945 bis 1949](https://de.wikipedia.org/wiki/Alliierter_Kontrollrat )",
            "1 EVEN Alliierter Kontrollrat für Deutschland, ohne Sowjetunion\n2 TYPE "{$event_type_04}"\n2 DATE FROM 20 MAR 1948 TO 15 SEP 1949\n2 NOTE Geteiltes Deutschland, Westzone\n2 SOUR [Wikipedia: Deutschland 1945 bis 1949](https://de.wikipedia.org/wiki/Alliierter_Kontrollrat )",
            "1 EVEN Sowjetische Militäradministration in Deutschland\n2 TYPE "{$event_type_04}"\n2 DATE FROM 20 MAR 1948 TO 10 OCT 1949\n2 NOTE Geteiltes Deutschland, Ostzone\n2 SOUR [Wikipedia: Deutschland 1945 bis 1949](https://de.wikipedia.org/wiki/Sowjetische_Militäradministration_in_Deutschland )",
            "1 EVEN Konrad Hermann Joseph Adenauer (1876 — 1967), CDU\n2 TYPE "{$event_type_05}"\n2 DATE FROM 15 SEP 1949 TO 16 OCT 1963\n2 NOTE Bundesrepublik Deutschland\n2 SOUR [Wikipedia: Bundeskanzler (Deutschland)](https://de.wikipedia.org/wiki/Konrad_Adenauer )",
            "1 EVEN Friedrich Wilhelm Reinhold Pieck (1876 — 1960), SED\n2 TYPE "{$event_type_08}"\n2 DATE FROM 11 OCT 1949 TO 07 SEP 1960\n2 NOTE Deutschen Demokratischen Republik\n2 SOUR [Wikipedia: Präsident der DDR](https://de.wikipedia.org/wiki/Wilhelm_Pieck )",
            "1 EVEN Walter Ernst Paul Ulbricht (1893 — 1973), SED\n2 TYPE "{$event_type_09}"\n2 DATE FROM 12 SEP 1960 TO 01 AUG 1973\n2 NOTE Deutschen Demokratischen Republik\n2 SOUR [Wikipedia: Präsident der DDR](https://de.wikipedia.org/wiki/Walter_Ulbricht )",
            "1 EVEN Ludwig Wilhelm Erhard (1897 — 1977), CDU\n2 TYPE "{$event_type_05}"\n2 DATE FROM 16 OCT 1963 TO 01 DEC 1966\n2 NOTE Bundesrepublik Deutschland\n2 SOUR [Wikipedia: Bundeskanzler (Deutschland)](https://de.wikipedia.org/wiki/Ludwig_Erhard )",
            "1 EVEN Kurt Georg Kiesinger (1904 — 1988), CDU\n2 TYPE "{$event_type_05}"\n2 DATE FROM 01 DEC 1966 TO 21 OCT 1969\n2 NOTE Bundesrepublik Deutschland\n2 SOUR [Wikipedia: Bundeskanzler (Deutschland)](https://de.wikipedia.org/wiki/Kurt_Georg_Kiesinger )",
            "1 EVEN Willy Brandt (1913 — 1992), SPD\n2 TYPE "{$event_type_05}"\n2 DATE FROM 21 OCT 1969 TO 07 MAY 1974\n2 NOTE Bundesrepublik Deutschland\n2 SOUR [Wikipedia: Bundeskanzler (Deutschland)](https://de.wikipedia.org/wiki/Willy_Brandt )",
            "1 EVEN Willi Stoph (1914 — 1999), SED\n2 TYPE "{$event_type_09}"\n2 DATE FROM 03 OCT 1973 TO 29 OCT 1976\n2 NOTE Deutschen Demokratischen Republik\n2 SOUR [Wikipedia: Vorsitzender des Staatsrats](https://de.wikipedia.org/wiki/Willi_Stoph )",
            "1 EVEN Walter Scheel (1919 — 2016), FDP\n2 TYPE "{$event_type_07}"\n2 DATE FROM 07 MAY 1974 TO 16 MAY 1974\n2 NOTE Bundesrepublik Deutschland\n2 SOUR [Wikipedia: Bundeskanzler (Deutschland)](https://de.wikipedia.org/wiki/Walter_Scheel )",
            "1 EVEN Helmut Heinrich Waldemar Schmidt (1918 — 2015), SDP\n2 TYPE "{$event_type_05}"\n2 DATE FROM 16 MAY 1974 TO 01 OCT 1982\n2 NOTE Bundesrepublik Deutschland\n2 SOUR [Wikipedia: Bundeskanzler (Deutschland)](https://de.wikipedia.org/wiki/Helmut_Schmidt )",
            "1 EVEN Erich Ernst Paul Honecker (1912 — 1994), SED\n2 TYPE "{$event_type_09}"\n2 DATE FROM 29 OCT 1976 TO 24 OCT 1989\n2 NOTE Deutschen Demokratischen Republik\n2 SOUR [Wikipedia: Vorsitzender des Staatsrats](https://de.wikipedia.org/wiki/Erich_Honecker )",
            "1 EVEN Helmut Josef Michael Kohl (1930 — 2017), CDU\n2 TYPE "{$event_type_05}"\n2 DATE FROM 01 OCT 1982 TO 27 OCT 1998\n2 NOTE Bundesrepublik Deutschland\n2 SOUR [Wikipedia: Bundeskanzler (Deutschland)](https://de.wikipedia.org/wiki/Helmut_Kohl )",
            "1 EVEN Egon Rudi Ernst Krenz (* 1937), SED\n2 TYPE "{$event_type_09}"\n2 DATE FROM 24 OCT 1989 TO 06 DEC 1989\n2 NOTE Deutschen Demokratischen Republik\n2 SOUR [Wikipedia: Vorsitzender des Staatsrats](https://de.wikipedia.org/wiki/Egon_Krenz )",
            "1 EVEN Manfred Gerlach (1928 — 2011), LDPD\n2 TYPE "{$event_type_09}"\n2 DATE FROM 06 DEC 1989 TO 05 APR 1990\n2 NOTE Deutschen Demokratischen Republik\n2 SOUR [Wikipedia: Vorsitzender des Staatsrats](https://de.wikipedia.org/wiki/Manfred_Gerlach )",
            "1 EVEN Gerhard Fritz Kurt Schröder (* 1944), SPD\n2 TYPE "{$event_type_05}"\n2 DATE FROM 27 OCT 1998 TO 22 NOV 2005\n2 NOTE Bundesrepublik Deutschland\n2 SOUR [Wikipedia: Bundeskanzler (Deutschland)](https://de.wikipedia.org/wiki/Gerhard_Schröder )",
            "1 EVEN Angela Dorothea Merkel geb. Kasner (* 1954), CDU\n2 TYPE "{$event_type_06}"\n2 DATE FROM 22 NOV 2005\n2 NOTE Bundesrepublik Deutschland\n2 SOUR [Wikipedia: Bundeskanzler (Deutschland)](https://de.wikipedia.org/wiki/Angela_Merkel )",
        ]);
    }
}
