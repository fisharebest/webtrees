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

namespace Fisharebest\Webtrees\Statistics\Repository\Interfaces;

interface IndividualRepositoryInterface
{
    public function totalRecords(): string;

    public function totalIndividuals(): string;

    public function totalSexMales(): string;

    public function totalSexFemales(): string;

    public function totalSexUnknown(): string;

    public function totalFamilies(): string;

    public function totalRepositories(): string;

    public function totalSources(): string;

    public function totalNotes(): string;

    public function totalIndividualsPercentage(): string;

    public function totalIndisWithSourcesPercentage(): string;

    public function totalFamiliesPercentage(): string;

    public function totalFamsWithSourcesPercentage(): string;

    public function totalRepositoriesPercentage(): string;

    public function totalSourcesPercentage(): string;

    public function totalNotesPercentage(): string;

    public function totalLivingPercentage(): string;

    public function totalDeceasedPercentage(): string;

    public function totalSexMalesPercentage(): string;

    public function totalSexFemalesPercentage(): string;

    public function totalSexUnknownPercentage(): string;

    public function getCommonSurname(): string;

    public function chartSex(
        string|null $color_female = null,
        string|null $color_male = null,
        string|null $color_unknown = null
    ): string;
}
