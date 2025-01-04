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

namespace Fisharebest\Webtrees\Statistics\Repository\Interfaces;

interface EventRepositoryInterface
{
    /**
     * @param array<string> $events
     */
    public function totalEvents(array $events = []): string;

    public function totalEventsBirth(): string;

    public function totalBirths(): string;

    public function totalEventsDeath(): string;

    public function totalDeaths(): string;

    public function totalEventsMarriage(): string;

    public function totalMarriages(): string;

    public function totalEventsDivorce(): string;

    public function totalDivorces(): string;

    public function totalEventsOther(): string;

    public function firstEvent(): string;

    public function lastEvent(): string;

    public function firstEventYear(): string;

    public function lastEventYear(): string;

    public function firstEventType(): string;

    public function lastEventType(): string;

    public function firstEventName(): string;

    public function lastEventName(): string;

    public function firstEventPlace(): string;

    public function lastEventPlace(): string;
}
