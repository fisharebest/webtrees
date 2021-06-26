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
 * [EN] Historical facts in German language:
 *      The Presidents of Germany (since 1949) and their historical Predecessors (1848-1949)
 * [DE] Historische Daten in deutscher Sprache:
 *      Die deutsche Bundespräsidenten (seit 1949) und ihre historische Vorgänger (1848-1949)
 *
 * @Author Lars van Ravenzwaaij
 * @Author Hermann Hartenthaler
 */

/**
 * Class HistoryPresidentsGerman
 */
class HistoryPresidentsGerman extends AbstractModule implements ModuleHistoricEventsInterface
{
    use ModuleHistoricEventsTrait;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        $part_title = I18N::translate('Presidents of Germany since');

        return $part_title . " 1848 🇩🇪";
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        return I18N::translate('Historical facts (in German): Presidents of Germany (since 1949) and their historical Predecessors (1848 - 1949)');
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
        $event_type_01 = I18N::translate('Imperial Administrator');                                     // "Reichsverweser"
        $event_type_02 = I18N::translate('State Organ');                                                // "Staatsorgan"
        $event_type_03 = I18N::translate('Federal Presidium');                                          // "Bundespräsidium"
        $event_type_04 = I18N::translate('Chairman of the Revolutionary Council of Peoples Deputies');  // "Vorsitzenden des revolutionären Rats der Volksbeauftragten"
        $event_type_05 = I18N::translate('President of the Empire');                                    // "Reichspräsident"
        $event_type_06 = I18N::translate('Occupation Force');                                           // "Besatzungsmacht"
        $event_type_07 = I18N::translate('President of the Republic');                                  // "Präsident der Republik"
        $event_type_08 = I18N::translate('Federal President');                                          // "Bundespräsident"

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
        "1 EVEN Erzherzog Johann Baptist Josef Fabian Sebastian von Österreich (1782 — 1859)\n2 TYPE " . $event_type_01 . "\n2 DATE FROM 29 JUN 1848 TO 20 DEZ 1849\n2 NOTE Die Frankfurter Nationalversammlung wählte ihn am 29. Juni 1848. Am 12. Juli 1848 übertrug der Bundestag des Deutschen Bundes ihm seine Befugnisse.\n2 SOUR [Wikipedia: Bundeskanzler(Deutschland)](https://de.wikipedia.org/wiki/Bundespräsident_(Deutschland)#Vom_Deutschen_Bund_zum_modernen_Bundesstaat )",
        "1 EVEN Bundeszentralkommission\n2 TYPE " . $event_type_02 . "\n2 DATE FROM 20 DEZ 1850 TO 23 AUG 1851\n2 NOTE In der Bundeszentralkommission arbeiteten 1849–1851 Österreich und Preußen zusammen. Sie übte die Befugnisse der Provisorischen Zentralgewalt des Deutschen Reichs von 1848/1849 aus.\n2 SOUR [Wikipedia: Bundeszentralkommission](https://de.wikipedia.org/wiki/Bundeszentralkommission )",
        "1 EVEN Bundespräsidium\n2 TYPE " . $event_type_02 . "\n2 DATE FROM 23 AUG 1851 TO 24 AUG 1866 \n2 NOTE Den Vorsitz des sogenannten Bundestages (offiziell Bundesversammlung), das Bundespräsidium, hatte der Vertreter Österreichs als Präsidialgesandter inne.\n2 SOUR [Wikipedia: Bundestag (Deutscher Bund)](https://de.wikipedia.org/wiki/Bundestag_(Deutscher_Bund))",
        "1 EVEN Wilhelm I «Wilhelm Friedrich Ludwig von Preußen» (1797 — 1888)\n2 TYPE " . $event_type_03 . "\n2 DATE FROM 01 JUL 1867 TO 09 MAR 1888\n2 NOTE Er wurde am 2. Januar 1861 König von Preußen, am 1. Juli 1867 dazu Präsident des Norddeutschen Bundes und schließlich ab dem 18. Januar 1871 in Personalunion Deutscher Kaiser.\n2 SOUR [Wikipedia: https://de.wikipedia.org/wiki/Wilhelm_I._(Deutsches_Reich)](Wilhelm I. (Deutsches Reich))",
        "1 EVEN Friedruch III «Friedrich Wilhelm Nikolaus Karl von Preußen» (1831 — 1888)\n2 TYPE " . $event_type_03 . "\n2 DATE FROM 09 MAR 1888 TO 15 JUN 1888\n2 NOTE Als König von Preußen war ebenfalls Präsident des Norddeutschen Bundes und in Personalunion Deutscher Kaiser.\n2 SOUR [Wikipedia: Friedrich III. (Deutsches Reich)](https://de.wikipedia.org/wiki/Friedrich_III._(Deutsches_Reich))",
        "1 EVEN Wilhelm II «Friedrich Wilhelm Viktor Albert von Preußen» (1859 — 1941)\n2 TYPE " . $event_type_03 . "\n2 DATE FROM 15 JUN 1888 TO 09 NOV 1918\n2 NOTE Bis zur Novemberrevolution 1948 übte er ebenfalls die Ämter seiner beiden Vorgänger aus. Ab dem 9. November 1918 befand er sich dann im niederländischen Exil.\n2 SOUR [Wikipedia: Wilhelm II. (Deutsches Reich)](https://de.wikipedia.org/wiki/Wilhelm_II._(Deutsches_Reich))",
        "1 EVEN Friedrich Ebert (1871 — 1925), SPD\n2 TYPE " . $event_type_04 . "\n2 DATE FROM 10 NOV 1918 TO 11 FEB 1919\n2 NOTE Provisorische Regierung Deutschlands\n2 SOUR [Wikipedia: Rat der Volksbeauftragten](https://de.wikipedia.org/wiki/Friedrich_Ebert )",
        "1 EVEN Friedrich Ebert (1871 — 1925), SPD\n2 TYPE " . $event_type_05 . "\n2 DATE FROM 11 FEB 1919 TO 28 FEB 1925\n2 NOTE Sein Amtszeit wäre bis zum 30. Juni 1925 gegangen, wenn er nicht vorher im Amt gestorben wäre.\n2 SOUR [Wikipedia: Liste der Reichspräsidenten](https://de.wikipedia.org/wiki/Friedrich_Ebert )",
        "1 EVEN Paul Ludwig Hans Anton von Beneckendorff und von Hindenburg (1847 — 1934), parteilos\n2 TYPE " . $event_type_05 . "\n2 DATE FROM 11 FEB 1919 TO 02 AUG 1934\n2 NOTE Er wurde 1925 zum zweiten Reichspräsidenten der Weimarer Republik gewählt. Am 30. Januar 1933 ernannte er Adolf Hitler zum Reichskanzler.\n2 SOUR [Wikipedia: Liste der Reichspräsidenten](https://de.wikipedia.org/wiki/Paul_von_Hindenburg )",
        "1 EVEN Adolf Hitler (1889 — 1945), NSDAP\n2 TYPE " . $event_type_05 . "\n2 DATE FROM 02 AUG 1934 TO 30 APR 1945\n2 NOTE Per Gesetz vom 1. August 1934 übernahm Reichskanzler Hitler beide Ämter in Personalunion bis zu seinem Selbstmord am 30. April 1945.\n2 SOUR [Wikipedia: Liste der Reichspräsidenten](https://de.wikipedia.org/wiki/Adolf_Hitler )",
        "1 EVEN Karl Dönitz  (1891 — 1980), NSDAP\n2 TYPE " . $event_type_05 . "\n2 DATE FROM 01 MAY 1945 TO 23 MAY 1945\n2 NOTE Amtsübernahme kraft Hitlers letzten Willens. Deshalb ist die Rechtmäßigkeit seiner Reichspräsidentschaft umstritten. Am 23. Mai 1945 wurde er Amt verhaftet und abgesetzt.\n2 SOUR [Wikipedia: Liste der Reichspräsidenten](https://de.wikipedia.org/wiki/Karl_Dönitz )",
        "1 EVEN Besetztes Deutschland\n2 TYPE " . $event_type_06 . "\n2 DATE FROM 23 MAY 1945 TO 12 SEP 1949",
        "1 EVEN Friedrich Wilhelm Reinhold Pieck, SED (1876–1960)\n2 TYPE " . $event_type_07 . "\n2 DATE FROM 11 OCT 1949 TO 07 SEP 1960\n2 NOTE Das Präsidentenamt der Deutschen Demokratischen Republik gab es von 1949 bis zum Jahr 1960 und wurde danach vom Staatsrat als kollektives Staatsoberhaupt der Deutschen Demokratischen Republik ausgeübt.\n2 SOUR [Wikipedia: Präsident der DDR](https://de.wikipedia.org/wiki/Wilhelm_Pieck )",
        "1 EVEN Theodor Heuss (1884 — 1963), FDP\n2 TYPE " . $event_type_08 . "\n2 DATE FROM 12 SEP 1949 TO 12 SEP 1959\n2 Durch Die erste Bundesversammlung wurde er zum ersten bundesdeutschen Staatsoberhaupt der Nachkriegszeit gewählt.\n2 SOUR [Wikipedia: Bundeskanzler (Deutschland](https://de.wikipedia.org/wiki/Theodor_Heuss )",
        "1 EVEN Karl Heinrich Lübke (1894 — 1972), CDU\n2 TYPE " . $event_type_08 . "\n2 DATE FROM 13 SEP 1959 TO 30 JUN 1969\n2 NOTE Von seiner Präsidentschaft blieben manche rhetorische Fehlgriffe in Erinnerung, die auch auf Auslandsreisen zu fragwürdigen Situationen führten, aber einer fortgeschrittenen Zerebralsklerose zugeschrieben werden konnten. Viele Zitate, die für Irritationen sorgten, waren jedoch, wie der damalige Spiegel-Mitarbeiter Hermann L. Gremliza 40 Jahre später offenbarte, bloße Erfindungen der Redaktion des Nachrichtenmagazins.\n2 SOUR [Wikipedia: Bundeskanzler (Deutschland](https://de.wikipedia.org/wiki/Heinrich_Lübke )",
        "1 EVEN Gustav Walter Heinemann (1899 — 1976), SPD\n2 TYPE " . $event_type_08 . "\n2 DATE FROM 01 JUL 1969 TO 30 JUN 1974\n2 NOTE Er wurde erst im dritten Wahlgang und ohne absolute Mehrheit der Bundesversammlung ins Amt gewählt und verschiedentlich als unbequemer Mahner und ein im Christentum fest verwurzelter Politiker gewürdigt.\n2 SOUR [Wikipedia: Bundeskanzler (Deutschland](https://de.wikipedia.org/wiki/Gustav_Heinemann )",
        "1 EVEN Walter Scheel (1919 — 2016), FDP\n2 TYPE " . $event_type_08 . "\n2 DATE FROM 01 JUL 1974 TO 30 JUN 1979\n2 NOTE Insbesondere zu Beginn seiner Amtszeit wurde er häufig als überambitioniert eingeschätzt, später allerdings wurde er in der Bevölkerung unerwartet populär und erwarb sich als Redner Respekt.\n2 SOUR [Wikipedia: Bundeskanzler (Deutschland](https://de.wikipedia.org/wiki/Walter_Scheel )",
        "1 EVEN Karl Walter Claus Carstens (1914 — 1992), CDU\n2 TYPE " . $event_type_08 . "\n2 DATE FROM 01 JUL 1979 TO 30 JUN 1984\n2 NOTE Seine staatsrechtlich bedeutsamste Entscheidung war die Auflösung des Bundestages nach der absichtlich verlorenen Vertrauensfrage Helmut Kohls 1982/1983.\n2 SOUR [Wikipedia: Bundeskanzler (Deutschland](https://de.wikipedia.org/wiki/Karl_Carstens )",
        "1 EVEN Richard Karl Freiherr von Weizsäcker (1920 — 2015), CDU\n2 TYPE " . $event_type_08 . "\n2 DATE FROM 01 JUL 1984 TO 30 JUN 1994\n2 NOTE Richard von Weizsäcker ging als einer der bedeutendsten Bundespräsidenten in die Geschichte ein und war der erste Bundeskanzler des vereinten Deutschlands.\n2 SOUR [Wikipedia: Bundeskanzler (Deutschland](https://de.wikipedia.org/wiki/Richard_von_Weizsäcker )",
        "1 EVEN Roman Herzog (1934 — 2017), CDU\n2 TYPE " . $event_type_08 . "\n2 DATE FROM 01 JUL 1994 TO 30 JUN 1999\n2 NOTE Der bis zu seiner Wahl als Präsident des Bundesverfassungsgerichts amtierende Roman Herzog wird besonders als Präsident der Ruck-Rede in Berlin 1997 wahrgenommen\n2 SOUR [Wikipedia: Bundeskanzler (Deutschland](https://de.wikipedia.org/wiki/Roman_Herzog )",
        "1 EVEN Johannes Rau (1931 — 2006), SPD\n2 TYPE " . $event_type_08 . "\n2 DATE FROM 01 JUL 1999 TO 30 JUN 2004\n2 NOTE Seinen – durchaus nicht nur abwertend gemeinten – Spitznamen „Bruder Johannes“ hatte er jedoch schon wesentlich früher wegen seiner öffentlich gelebten Religiosität respektive seines oft als pastoral empfundenen Habitus erhalten.\n2 SOUR [Wikipedia: Bundeskanzler (Deutschland](https://de.wikipedia.org/wiki/Johannes_Rau )",
        "1 EVEN Horst Köhler  (* 1943), CDU\n2 TYPE " . $event_type_08 . "\n2 DATE FROM 01 JUL 2004 TO 31 MAY 2010\n2 NOTE Nach Kritik an einer Äußerung Köhlers in einem Interview, dass „im Notfall auch militärischer Einsatz notwendig ist, um unsere Interessen zu wahren, zum Beispiel freie Handelswege“, erklärte Köhler am 31. Mai 2010 seinen Rücktritt mit sofortiger Wirkung.\n2 SOUR [Wikipedia: Bundeskanzler (Deutschland](https://de.wikipedia.org/wiki/Horst_Köhler )",
        "1 EVEN Amt vakant\n2 DATE FROM 31 MAY 2010 TO 30 JUN 2010",
        "1 EVEN Christian Wilhelm Walter Wulff (* 1959), CDU\n2 TYPE " . $event_type_08 . "\n2 DATE FROM 30 JUN 2010 TO 17 FEB 2012\n2 NOTE Nachdem die Staatsanwaltschaft Hannover die Aufhebung seiner Immunität beantragt hatte – das erste Mal, dass dies bei einem Bundespräsidenten geschah –, trat er am 17. Februar 2012 mit sofortiger Wirkung zurück.\n2 SOUR [Wikipedia: Bundeskanzler(Deutschland](https://de.wikipedia.org/wiki/Christian_Wulff )",
        "1 EVEN Amt vakant\n2 DATE FROM 17 FEB 2012 TO 18 MAR 2012",
        "1 EVEN Joachim Gauck (* 1940), parteilos\n2 TYPE " . $event_type_08 . "\n2 DATE FROM  18 MAR 2012 TO 18 MAR 2017\n2 NOTE Gauck war der erste parteilose Bundeskanzler der Bundesrepublik Deutschland sowie der erste ehemalige DDR-Bürger, der das Amt des Bundespräsidenten bekleidet hat.\n2 SOUR [Wikipedia: Bundeskanzler (Deutschland](https://de.wikipedia.org/wiki/Joachim_Gauck )",
        "1 EVEN Frank-Walter Steinmeier (* 1956), SPD\n2 TYPE " . $event_type_08 . "\n2 DATE FROM  18 MAR 2017\n2 NOTE Derzeit amtierender Bundeskanzler\n2 SOUR [Wikipedia: Bundeskanzler (Deutschland](https://de.wikipedia.org/wiki/Frank-Walter_Steinmeier )",
        ]);
    }
}
