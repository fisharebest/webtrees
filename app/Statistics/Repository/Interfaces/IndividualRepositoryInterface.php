<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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
 * A repository providing methods for individual related statistics.
 */
interface IndividualRepositoryInterface
{
    /**
     * How many GEDCOM records exist in the tree.
     *
     * @return string
     */
    public function totalRecords(): string;

    /**
     * How many individuals exist in the tree.
     *
     * @return string
     */
    public function totalIndividuals(): string;

    /**
     * Count the number of males.
     *
     * @return string
     */
    public function totalSexMales(): string;

    /**
     * Count the number of females.
     *
     * @return string
     */
    public function totalSexFemales(): string;

    /**
     * Count the number of individuals with unknown sex.
     *
     * @return string
     */
    public function totalSexUnknown(): string;

    /**
     * Count the total families.
     *
     * @return string
     */
    public function totalFamilies(): string;

    /**
     * Count the number of repositories
     *
     * @return string
     */
    public function totalRepositories(): string;

    /**
     * Count the total number of sources.
     *
     * @return string
     */
    public function totalSources(): string;

    /**
     * Count the number of notes.
     *
     * @return string
     */
    public function totalNotes(): string;

    /**
     * Show the total individuals as a percentage.
     *
     * @return string
     */
    public function totalIndividualsPercentage(): string;

    /**
     * Show the total families as a percentage.
     *
     * @return string
     */
    public function totalFamiliesPercentage(): string;

    /**
     * Show the total number of repositories as a percentage.
     *
     * @return string
     */
    public function totalRepositoriesPercentage(): string;

    /**
     * Show the number of sources as a percentage.
     *
     * @return string
     */
    public function totalSourcesPercentage(): string;

    /**
     * Show the number of notes as a percentage.
     *
     * @return string
     */
    public function totalNotesPercentage(): string;

    /**
     * Count the number of living individuals.
     *
     * @return string
     */
    public function totalLivingPercentage(): string;

    /**
     * Count the number of dead individuals.
     *
     * @return string
     */
    public function totalDeceasedPercentage(): string;

    /**
     * Count the number of males
     *
     * @return string
     */
    public function totalSexMalesPercentage(): string;

    /**
     * Count the number of females.
     *
     * @return string
     */
    public function totalSexFemalesPercentage(): string;

    /**
     * Count the number of individuals with unknown sex.
     *
     * @return string
     */
    public function totalSexUnknownPercentage(): string;

    /**
     * Find common surnames.
     *
     * @return string
     */
    public function getCommonSurname(): string;

    /**
     * Generate a chart showing sex distribution.
     *
     * @param string|null $color_female
     * @param string|null $color_male
     * @param string|null $color_unknown
     *
     * @return string
     */
    public function chartSex(
        string $color_female = null,
        string $color_male = null,
        string $color_unknown = null
    ): string;
}
