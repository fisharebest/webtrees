<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees\Module;

use Illuminate\Support\Collection;

/**
 * Class BritishSocialHistory
 *
 * @package Fisharebest\Webtrees\Module
 */
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
        return 'British social history';
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
     * @return Collection
     * @return string[]
     */
    public function historicEventsAll(): Collection
    {
        return new Collection([
            "1 EVEN The Duke of Wellington defeated Napoleon Bonaparte, ending the Napoleonic wars.\n2 TYPE Battle of Waterloo\n2 DATE 18 JUN 1815\n2 PLAC Waterloo, Belgium",
            "1 EVEN Cavalry was used to disperse a large crowd who were demanding electoral reform.  15 were killed and hundreds injured.\n2 TYPE Peterloo Massacre\n2 DATE 16 AUG 1819\n2 PLAC Manchester, England",
            "1 EVEN Games of the IV Olympiad\n2 TYPE Olympic games\n2 DATE FROM 27 APR 1908 TO 31 OCT 1908\n2 PLAC London, England",
            "1 EVEN Free healthcare for all\n2 TYPE National Health Service\n2 DATE FROM 5 JUL 1948",
            "1 EVEN Games of the XIV Olympiad\n2 TYPE Olympic games\n2 DATE FROM 29 JUL 1948 TO 14 AUG 1948\n2 PLAC London, England",
            "1 EVEN Mass industrial action, power cuts and a three-day working week.\n2 TYPE THe winter of discontent\n2 DATE FROM 27 JUL 2012 TO 12 AUG 2012\n2 PLAC London, England",
            "1 EVEN Games of the XXX Olympiad\n2 TYPE Olympic games\n2 DATE FROM 27 JUL 2012 TO 12 AUG 2012\n2 PLAC London, England",
        ]);
    }
}

;
