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

/**
 * A repository providing methods for event related statistics.
 */
interface EventRepositoryInterface
{
    /**
     * Count the number of events (with dates).
     *
     * @param array<string> $events
     *
     * @return string
     */
    public function totalEvents(array $events = []): string;

    /**
     * Count the number of births events (BIRT, CHR, BAPM, ADOP).
     *
     * @return string
     */
    public function totalEventsBirth(): string;

    /**
     * Count the number of births (BIRT).
     *
     * @return string
     */
    public function totalBirths(): string;

    /**
     * Count the number of death events (DEAT, BURI, CREM).
     *
     * @return string
     */
    public function totalEventsDeath(): string;

    /**
     * Count the number of deaths (DEAT).
     *
     * @return string
     */
    public function totalDeaths(): string;

    /**
     * Count the number of marriage events (MARR, _NMR).
     *
     * @return string
     */
    public function totalEventsMarriage(): string;

    /**
     * Count the number of marriages (MARR).
     *
     * @return string
     */
    public function totalMarriages(): string;

    /**
     * Count the number of divorce events (DIV, ANUL, _SEPR).
     *
     * @return string
     */
    public function totalEventsDivorce(): string;

    /**
     * Count the number of divorces (DIV).
     *
     * @return string
     */
    public function totalDivorces(): string;

    /**
     * Count the number of other events (not birth, death, marriage or divorce related).
     *
     * @return string
     */
    public function totalEventsOther(): string;

    /**
     * Find the earliest event.
     *
     * @return string
     */
    public function firstEvent(): string;

    /**
     * Find the latest event.
     *
     * @return string
     */
    public function lastEvent(): string;

    /**
     * Find the year of the earliest event.
     *
     * @return string
     */
    public function firstEventYear(): string;

    /**
     * Find the year of the latest event.
     *
     * @return string
     */
    public function lastEventYear(): string;

    /**
     * Find the type of the earliest event.
     *
     * @return string
     */
    public function firstEventType(): string;

    /**
     * Find the type of the latest event.
     *
     * @return string
     */
    public function lastEventType(): string;

    /**
     * Find the name of the individual with the earliest event.
     *
     * @return string
     */
    public function firstEventName(): string;

    /**
     * Find the name of the individual with the latest event.
     *
     * @return string
     */
    public function lastEventName(): string;

    /**
     * Find the location of the earliest event.
     *
     * @return string
     */
    public function firstEventPlace(): string;

    /**
     * Find the location of the latest event.
     *
     * @return string
     */
    public function lastEventPlace(): string;
}
