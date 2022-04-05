<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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

use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Illuminate\Support\Collection;

/**
 * Trait ModuleHistoricEventsTrait - Show historic events on an individual‘s page
 */
trait ModuleHistoricEventsTrait
{
    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        return I18N::translate('Add historic events to an individual’s page.');
    }

    /**
     * All events provided by this module.
     *
     * @return Collection<int,string>
     */
    public function historicEventsAll(): Collection
    {
        return new Collection([
            "1 EVEN foo\n2 TYPE bar\n2 DATE FROM 6 FEB 1952"
        ]);
    }

    /**
     * Which events should we show for an individual?
     *
     * @param Individual $individual
     *
     * @return Collection<int,Fact>
     */
    public function historicEventsForIndividual(Individual $individual): Collection
    {
        $min_date = $individual->getEstimatedBirthDate();
        $max_date = $individual->getEstimatedDeathDate();

        return (new Collection($this->historicEventsAll()))
            ->map(static function (string $gedcom) use ($individual): Fact {
                return new Fact($gedcom, $individual, 'histo');
            })
            ->filter(static function (Fact $fact) use ($min_date, $max_date): bool {
                return Date::compare($fact->date(), $min_date) >= 0 && Date::compare($fact->date(), $max_date) <= 0;
            });
    }
}
