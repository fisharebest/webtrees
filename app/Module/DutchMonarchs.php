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
 * Class DutchMonarchs
 */
class DutchMonarchs extends AbstractModule implements ModuleHistoricEventsInterface
{
    use ModuleHistoricEventsTrait;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        return 'Nederlandse Monarchiën 🇳🇱';
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
            "1 EVEN Lodewijk I, de Lamme Koning\n2 NAME Lodewijk Napoleon Bonaparte\n2 TYPE Koning van Holland\n2 DATE FROM 05 JUN 1806 TO 01 JUL 1810\n2 NOTE Koninkrijk Holland (1806 - 1810), vazalstaat van het Eerste Franse Keizerrijk",
            "1 EVEN Lodewijk II\n2 NAME Napoleon Lodewijk Bonaparte\n2 TYPE Koning van Holland\n2 DATE FROM 01 JUL 1810 TO 13 JUL 1810\n2 NOTE Koninkrijk Holland (1806 - 1810), vazalstaat van het Eerste Franse Keizerrijk",
            "1 EVEN Napoleon I\n2 NAME Napoleon Bonaparte\n2 TYPE Keizer der Fransen\n2 DATE FROM 13 JUL 1810 TO 21 NOV 1813\n2 NOTE Geannexeerd deel van het Eerste Franse Keizerrijk",
            "1 EVEN Willem I\n2 NAME Willem Frederik, Prins van Oranje-Nassau\n2 TYPE Soeverein Vorst der Nederlanden\n2 DATE FROM 21 NOV 1813 TO 16 MAR 1815\n2 NOTE Soeverein vorstendom der Verenigde Nederlanden (1813 - 1815)",
            "1 EVEN Willem I\n2 NAME Willem Frederik van Oranje-Nassau\n2 TYPE Koning der Nederlanden\n2 DATE FROM 16 MAR 1815 TO 21 JUL 1831\n2 NOTE Verenigd Koninkrijk der Nederlanden (1815 - 1830)",
            "1 EVEN Willem I\n2 NAME Willem Frederik van Oranje-Nassau\n2 TYPE Koning der Nederlanden\n2 DATE FROM 21 JUL 1831 TO 07 OKT 1840\n2 NOTE Koninkrijk der Nederlanden (vanaf 1830)",
            "1 EVEN Willem II\n2 NAME Willem Frederik George Lodewijk van Oranje-Nassau\n2 TYPE Koning der Nederlanden\n2 DATE FROM 07 OKT 1840 TO 17 MAR 1849\n2 NOTE Koninkrijk der Nederlanden",
            "1 EVEN Willem III\n2 NAME Willem Alexander Paul Frederik Lodewijk van Oranje-Nassau\n2 TYPE Koning der Nederlanden\n2 DATE FROM 17 MAR 1849 TO 23 NOV 1890\n2 NOTE Koninkrijk der Nederlanden",
            "1 EVEN Wilhelmina\n2 NAME Wilhelmina Helena Pauline Maria van Oranje-Nassau\n2 TYPE Koningin der Nederlanden\n2 DATE FROM 23 NOV 1890 TO 04 SEP 1948\n2 NOTE Koninkrijk der Nederlanden",
            "1 EVEN Juliana\n2 NAME Juliana Louise Emma Marie Wilhelmina van Oranje-Nassau\n2 TYPE Koningin der Nederlanden\n2 DATE FROM 04 SEP 1948 TO 30 APR 1980\n2 NOTE Koninkrijk der Nederlanden",
            "1 EVEN Beatrix\n2 NAME Beatrix Wilhelmina Armgard van Oranje-Nassau\n2 TYPE Koningin der Nederlanden\n2 DATE FROM 30 APR 1980 TO 30 APR 2013\n2 NOTE Koninkrijk der Nederlanden",
            "1 EVEN Willem-Alexander\n2 NAME Willem-Alexander Claus George Ferdinand van Oranje-Nassau\n2 TYPE Koning der Nederlanden\n2 DATE FROM 30 APR 2013\n2 NOTE Koninkrijk der Nederlanden",
        ]);
    }
}
