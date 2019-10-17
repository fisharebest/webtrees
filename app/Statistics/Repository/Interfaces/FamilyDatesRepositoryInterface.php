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

namespace Fisharebest\Webtrees\Statistics\Repository\Interfaces;

/**
 * A repository providing methods for family dates related statistics (birth, death, marriage, divorce).
 */
interface FamilyDatesRepositoryInterface
{
    /**
     * Find the earliest birth.
     *
     * @return string
     */
    public function firstBirth(): string;

    /**
     * Find the earliest birth year.
     *
     * @return string
     */
    public function firstBirthYear(): string;

    /**
     * Find the name of the earliest birth.
     *
     * @return string
     */
    public function firstBirthName(): string;

    /**
     * Find the earliest birth place.
     *
     * @return string
     */
    public function firstBirthPlace(): string;

    /**
     * Find the latest birth.
     *
     * @return string
     */
    public function lastBirth(): string;

    /**
     * Find the latest birth year.
     *
     * @return string
     */
    public function lastBirthYear(): string;

    /**
     * Find the latest birth name.
     *
     * @return string
     */
    public function lastBirthName(): string;

    /**
     * Find the latest birth place.
     *
     * @return string
     */
    public function lastBirthPlace(): string;

    /**
     * Find the earliest death.
     *
     * @return string
     */
    public function firstDeath(): string;

    /**
     * Find the earliest death year.
     *
     * @return string
     */
    public function firstDeathYear(): string;

    /**
     * Find the earliest death name.
     *
     * @return string
     */
    public function firstDeathName(): string;

    /**
     * Find the earliest death place.
     *
     * @return string
     */
    public function firstDeathPlace(): string;

    /**
     * Find the latest death.
     *
     * @return string
     */
    public function lastDeath(): string;

    /**
     * Find the latest death year.
     *
     * @return string
     */
    public function lastDeathYear(): string;

    /**
     * Find the latest death name.
     *
     * @return string
     */
    public function lastDeathName(): string;

    /**
     * Find the place of the latest death.
     *
     * @return string
     */
    public function lastDeathPlace(): string;

    /**
     * Find the earliest marriage.
     *
     * @return string
     */
    public function firstMarriage(): string;

    /**
     * Find the year of the earliest marriage.
     *
     * @return string
     */
    public function firstMarriageYear(): string;

    /**
     * Find the names of spouses of the earliest marriage.
     *
     * @return string
     */
    public function firstMarriageName(): string;

    /**
     * Find the place of the earliest marriage.
     *
     * @return string
     */
    public function firstMarriagePlace(): string;

    /**
     * Find the latest marriage.
     *
     * @return string
     */
    public function lastMarriage(): string;

    /**
     * Find the year of the latest marriage.
     *
     * @return string
     */
    public function lastMarriageYear(): string;

    /**
     * Find the names of spouses of the latest marriage.
     *
     * @return string
     */
    public function lastMarriageName(): string;

    /**
     * Find the location of the latest marriage.
     *
     * @return string
     */
    public function lastMarriagePlace(): string;

    /**
     * Find the earliest divorce.
     *
     * @return string
     */
    public function firstDivorce(): string;

    /**
     * Find the year of the earliest divorce.
     *
     * @return string
     */
    public function firstDivorceYear(): string;

    /**
     * Find the names of individuals in the earliest divorce.
     *
     * @return string
     */
    public function firstDivorceName(): string;

    /**
     * Find the location of the earliest divorce.
     *
     * @return string
     */
    public function firstDivorcePlace(): string;

    /**
     * Find the latest divorce.
     *
     * @return string
     */
    public function lastDivorce(): string;

    /**
     * Find the year of the latest divorce.
     *
     * @return string
     */
    public function lastDivorceYear(): string;

    /**
     * Find the names of the individuals in the latest divorce.
     *
     * @return string
     */
    public function lastDivorceName(): string;

    /**
     * Find the location of the latest divorce.
     *
     * @return string
     */
    public function lastDivorcePlace(): string;
}
