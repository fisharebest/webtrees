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

interface MediaRepositoryInterface
{
    public function totalMedia(): string;

    public function totalMediaAudio(): string;

    public function totalMediaBook(): string;

    public function totalMediaCard(): string;

    public function totalMediaCertificate(): string;

    public function totalMediaCoatOfArms(): string;

    public function totalMediaDocument(): string;

    public function totalMediaElectronic(): string;

    public function totalMediaMagazine(): string;

    public function totalMediaManuscript(): string;

    public function totalMediaMap(): string;

    public function totalMediaFiche(): string;

    public function totalMediaFilm(): string;

    public function totalMediaNewspaper(): string;

    public function totalMediaPainting(): string;

    public function totalMediaPhoto(): string;

    public function totalMediaTombstone(): string;

    public function totalMediaVideo(): string;

    public function totalMediaOther(): string;

    public function totalMediaUnknown(): string;

    public function chartMedia(string|null $color_from = null, string|null $color_to = null): string;
}
