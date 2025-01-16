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

/**
 * A repository providing methods for media type related statistics.
 */
interface MediaRepositoryInterface
{
    /**
     * Count the number of media records.
     *
     * @return string
     */
    public function totalMedia(): string;

    /**
     * Count the number of media records with type "audio".
     *
     * @return string
     */
    public function totalMediaAudio(): string;

    /**
     * Count the number of media records with type "book".
     *
     * @return string
     */
    public function totalMediaBook(): string;

    /**
     * Count the number of media records with type "card".
     *
     * @return string
     */
    public function totalMediaCard(): string;

    /**
     * Count the number of media records with type "certificate".
     *
     * @return string
     */
    public function totalMediaCertificate(): string;

    /**
     * Count the number of media records with type "coat of arms".
     *
     * @return string
     */
    public function totalMediaCoatOfArms(): string;

    /**
     * Count the number of media records with type "document".
     *
     * @return string
     */
    public function totalMediaDocument(): string;

    /**
     * Count the number of media records with type "electronic".
     *
     * @return string
     */
    public function totalMediaElectronic(): string;

    /**
     * Count the number of media records with type "magazine".
     *
     * @return string
     */
    public function totalMediaMagazine(): string;

    /**
     * Count the number of media records with type "manuscript".
     *
     * @return string
     */
    public function totalMediaManuscript(): string;

    /**
     * Count the number of media records with type "map".
     *
     * @return string
     */
    public function totalMediaMap(): string;

    /**
     * Count the number of media records with type "microfiche".
     *
     * @return string
     */
    public function totalMediaFiche(): string;

    /**
     * Count the number of media records with type "microfilm".
     *
     * @return string
     */
    public function totalMediaFilm(): string;

    /**
     * Count the number of media records with type "newspaper".
     *
     * @return string
     */
    public function totalMediaNewspaper(): string;

    /**
     * Count the number of media records with type "painting".
     *
     * @return string
     */
    public function totalMediaPainting(): string;

    /**
     * Count the number of media records with type "photograph".
     *
     * @return string
     */
    public function totalMediaPhoto(): string;

    /**
     * Count the number of media records with type "tombstone".
     *
     * @return string
     */
    public function totalMediaTombstone(): string;

    /**
     * Count the number of media records with type "video".
     *
     * @return string
     */
    public function totalMediaVideo(): string;

    /**
     * Count the number of media records with type "other".
     *
     * @return string
     */
    public function totalMediaOther(): string;

    /**
     * Count the number of media records with type "unknown".
     *
     * @return string
     */
    public function totalMediaUnknown(): string;

    /**
     * Create a chart of media types.
     *
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function chartMedia(?string $color_from = null, ?string $color_to = null): string;
}
