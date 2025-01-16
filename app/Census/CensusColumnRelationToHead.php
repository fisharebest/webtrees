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

namespace Fisharebest\Webtrees\Census;

use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Services\RelationshipService;

use function app;
use function assert;

/**
 * Relationship to head of household.
 */
class CensusColumnRelationToHead extends AbstractCensusColumn implements CensusColumnInterface
{
    protected const HEAD_OF_HOUSEHOLD = '-';

    /**
     * Generate the likely value of this census column, based on available information.
     *
     * @param Individual $individual
     * @param Individual $head
     *
     * @return string
     */
    public function generate(Individual $individual, Individual $head): string
    {
        if ($individual === $head) {
            return static::HEAD_OF_HOUSEHOLD;
        }

        $relationship_service = app(RelationshipService::class);
        assert($relationship_service instanceof RelationshipService);

        return $relationship_service->getCloseRelationshipName($head, $individual);
    }
}
