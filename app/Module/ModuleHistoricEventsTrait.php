<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

trait ModuleHistoricEventsTrait
{
    public function description(): string
    {
        return I18N::translate('Add historic events to an individual’s page.');
    }

    /**
     * @return Collection<int,string>
     */
    public function historicEventsAll(): Collection
    {
        return new Collection();
    }

    /**
     * @return Collection<int,Fact>
     */
    public function historicEventsForIndividual(Individual $individual): Collection
    {
        $min_date = $individual->getEstimatedBirthDate();
        $max_date = $individual->getEstimatedDeathDate();

        return $this->historicEventsAll()
            ->map(static fn (string $gedcom): Fact => new Fact($gedcom, $individual, 'histo'))
            ->filter(static fn (Fact $fact): bool => Date::compare($fact->date(), $min_date) >= 0)
            ->filter(static fn (Fact $fact): bool => Date::compare($fact->date(), $max_date) <= 0);
    }
}
