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
 * [EN] Dutch historical facts:
 *      The dutch royal heads of state (since 1806)
 * [NL] Nederlandse historische feiten:
 *      De nederlandse staathoofden van koninklijk bloed (sinds 1806)
 */

/**
 * Class HistoryDutchMonarchs
 */
class HistoryDutchMonarchs extends AbstractModule implements ModuleHistoricEventsInterface
{
    use ModuleHistoricEventsTrait;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        return I18N::translate('Nederlandse Monarchiën') . " 🇳🇱";
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
        /**
        * Variables used for "TYPE" in Collection.
        *
        * @return string
        */
        $type_01 = I18N::translate('King of Holland');                      // "Koning van Holland"
        $type_02 = I18N::translate('Emperor of the French');                // "Keizer der Fransen"
        $type_03 = I18N::translate('Sovereign Prince of the Netherlands');  // "Soeverein Vorst der Nederlanden"
        $type_04 = I18N::translate('King of the Netherlands');              // "Koning der Nederlanden"
        $type_05 = I18N::translate('Queen of the Netherlands');             // "Koningin der Nederlanden"

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
    {
        return new Collection([
            "1 EVEN Lodewijk I, de Lamme Koning (Lodewijk Napoleon Bonaparte)\n2 TYPE " . $type_01 . "\n2 DATE FROM 05 JUN 1806 TO 01 JUL 1810\n2 NOTE Koninkrijk Holland (1806 - 1810), vazalstaat van het Eerste Franse Keizerrijk",
            "1 EVEN Lodewijk II (Napoleon Lodewijk Bonaparte)\n2 TYPE " . $type_01 . "\n2 DATE FROM 01 JUL 1810 TO 13 JUL 1810\n2 NOTE Koninkrijk Holland (1806 - 1810), vazalstaat van het Eerste Franse Keizerrijk",
            "1 EVEN Napoleon I (Napoleon Bonaparte)\n2 TYPE " . $type_02 . "\n2 DATE FROM 13 JUL 1810 TO 21 NOV 1813\n2 NOTE Geannexeerd deel van het Eerste Franse Keizerrijk",
            "1 EVEN Willem I (Willem Frederik, Prins van Oranje-Nassau)\n2 TYPE " . $type_03 . "\n2 DATE FROM 21 NOV 1813 TO 16 MAR 1815\n2 NOTE Soeverein vorstendom der Verenigde Nederlanden (1813 - 1815)",
            "1 EVEN Willem I (Willem Frederik van Oranje-Nassau)\n2 TYPE " . $type_04 . "\n2 DATE FROM 16 MAR 1815 TO 21 JUL 1831\n2 NOTE Verenigd Koninkrijk der Nederlanden (1815 - 1830)",
            "1 EVEN Willem I (Willem Frederik van Oranje-Nassau)\n2 TYPE " . $type_04 . "\n2 DATE FROM 21 JUL 1831 TO 07 OKT 1840\n2 NOTE Koninkrijk der Nederlanden (vanaf 1830)",
            "1 EVEN Willem II (Willem Frederik George Lodewijk van Oranje-Nassau)\n2 TYPE " . $type_04 . "\n2 DATE FROM 07 OKT 1840 TO 17 MAR 1849\n2 NOTE Koninkrijk der Nederlanden",
            "1 EVEN Willem III (Willem Alexander Paul Frederik Lodewijk van Oranje-Nassau)\n2 TYPE " . $type_04 . "\n2 DATE FROM 17 MAR 1849 TO 23 NOV 1890\n2 NOTE Koninkrijk der Nederlanden",
            "1 EVEN Wilhelmina (Wilhelmina Helena Pauline Maria van Oranje-Nassau)\n2 TYPE " . $type_05 . "\n2 DATE FROM 23 NOV 1890 TO 04 SEP 1948\n2 NOTE Koninkrijk der Nederlanden",
            "1 EVEN Juliana (Juliana Louise Emma Marie Wilhelmina van Oranje-Nassau)\n2 TYPE " . $type_05 . "\n2 DATE FROM 04 SEP 1948 TO 30 APR 1980\n2 NOTE Koninkrijk der Nederlanden",
            "1 EVEN Beatrix (Beatrix Wilhelmina Armgard van Oranje-Nassau)\n2 TYPE " . $type_05 . "\n2 DATE FROM 30 APR 1980 TO 30 APR 2013\n2 NOTE Koninkrijk der Nederlanden",
            "1 EVEN Willem-Alexander (Willem-Alexander Claus George Ferdinand van Oranje-Nassau)\n2 " . $type_04 . "\n2 DATE FROM 30 APR 2013\n2 NOTE Koninkrijk der Nederlanden",
        ]);
    }
}