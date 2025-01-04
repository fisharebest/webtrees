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

interface FamilyDatesRepositoryInterface
{
    public function firstBirth(): string;

    public function firstBirthYear(): string;

    public function firstBirthName(): string;

    public function firstBirthPlace(): string;

    public function lastBirth(): string;

    public function lastBirthYear(): string;

    public function lastBirthName(): string;

    public function lastBirthPlace(): string;

    public function firstDeath(): string;

    public function firstDeathYear(): string;

    public function firstDeathName(): string;

    public function firstDeathPlace(): string;

    public function lastDeath(): string;

    public function lastDeathYear(): string;

    public function lastDeathName(): string;

    public function lastDeathPlace(): string;

    public function firstMarriage(): string;

    public function firstMarriageYear(): string;

    public function firstMarriageName(): string;

    public function firstMarriagePlace(): string;

    public function lastMarriage(): string;

    public function lastMarriageYear(): string;

    public function lastMarriageName(): string;

    public function lastMarriagePlace(): string;

    public function firstDivorce(): string;

    public function firstDivorceYear(): string;

    public function firstDivorceName(): string;

    public function firstDivorcePlace(): string;

    public function lastDivorce(): string;

    public function lastDivorceYear(): string;

    public function lastDivorceName(): string;

    public function lastDivorcePlace(): string;
}
