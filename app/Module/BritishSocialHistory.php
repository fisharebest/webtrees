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

use Fisharebest\Webtrees\I18N;
use Illuminate\Support\Collection;

class BritishSocialHistory extends AbstractModule implements ModuleHistoricEventsInterface
{
    use ModuleHistoricEventsTrait;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        return 'British social history 🇬🇧';
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
                    "1 EVEN Bubonic plague kills half the population.\n2 TYPE Plague\n2 DATE FROM 1348 TO 1350",
                    "1 EVEN Bubonic plague returns and kills 20% the population.\n2 TYPE Plague\n2 DATE FROM 1361 TO 1362",
                    "1 EVEN Uprising against serfdom and taxation, led by Wat Tyler.\n2 TYPE Peasants Revolt\n2 DATE FROM 30 MAY 1381 TO NOV 1381",
                    "1 EVEN Royalists and parliamentarians battle for control of the country.\n2 TYPE English Civil War\n2 DATE FROM 22 AUG 1642 TO 03 SEP 1651",
                    "1 EVEN Most of London was destroyed by fire.\n2 TYPE Great Fire of London\n2 DATE FROM 02 SEP 1666 TO 06 SEP 1666",
                    "1 EVEN England and Scotland combine to form Great Britain.\n2 TYPE Act of Union\n2 DATE 1 MAY 1707",
                    "1 EVEN The Duke of Wellington defeated Napoleon Bonaparte, ending the Napoleonic wars.\n2 TYPE Battle of Waterloo\n2 DATE 18 JUN 1815\n2 PLAC Waterloo, Belgium",
                    "1 EVEN Cavalry was used to disperse a large crowd who were demanding electoral reform.  15 were killed and hundreds injured.\n2 TYPE Peterloo Massacre\n2 DATE 16 AUG 1819\n2 PLAC Manchester, England",
                    "1 EVEN\n2 TYPE The Crimean War\n2 DATE FROM 16 OCT 1853 TO 30 MAR 1856",
                    "1 EVEN Battle for control of southern Africa\n2 TYPE The Boer War\n2 DATE FROM 11 OCT 1899 TO 31 MAY 1902",
                    "1 EVEN Games of the IV Olympiad\n2 TYPE Olympic Games\n2 DATE FROM 27 APR 1908 TO 31 OCT 1908\n2 PLAC London, England",
                    "1 EVEN\n2 TYPE The Great War\n2 DATE FROM 28 JUL 1914 TO 11 NOV 1918",
                    "1 EVEN\n2 TYPE World War 2\n2 DATE FROM 01 SEP 1939 TO 02 SEP 1945",
                    "1 EVEN Free healthcare for all\n2 TYPE National Health Service\n2 DATE FROM 5 JUL 1948",
                    "1 EVEN Games of the XIV Olympiad\n2 TYPE Olympic Games\n2 DATE FROM 29 JUL 1948 TO 14 AUG 1948\n2 PLAC London, England",
                    "1 EVEN Mass industrial action, power cuts and a three-day working week.\n2 TYPE Winter of Discontent\n2 DATE FROM OCT 1978 TO FEB 1979\n2 PLAC London, England",
                    "1 EVEN Games of the XXX Olympiad\n2 TYPE Olympic Games\n2 DATE FROM 27 JUL 2012 TO 12 AUG 2012\n2 PLAC London, England",
                ]);

            default:
                return new Collection();
        }
    }
}
