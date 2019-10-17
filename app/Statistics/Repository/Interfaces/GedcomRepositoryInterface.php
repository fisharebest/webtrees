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
 * A repository providing methods for GEDCOM related statistics.
 */
interface GedcomRepositoryInterface
{
    /**
     * Get the name used for GEDCOM files and URLs.
     *
     * @return string
     */
    public function gedcomFilename(): string;

    /**
     * Get the internal ID number of the tree.
     *
     * @return int
     */
    public function gedcomId(): int;

    /**
     * Get the descriptive title of the tree.
     *
     * @return string
     */
    public function gedcomTitle(): string;

    /**
     * Get the software originally used to create the GEDCOM file.
     *
     * @return string
     */
    public function gedcomCreatedSoftware(): string;

    /**
     * Get the version of software which created the GEDCOM file.
     *
     * @return string
     */
    public function gedcomCreatedVersion(): string;

    /**
     * Get the date the GEDCOM file was created.
     *
     * @return string
     */
    public function gedcomDate(): string;

    /**
     * When was this tree last updated?
     *
     * @return string
     */
    public function gedcomUpdated(): string;

    /**
     * What is the significant individual from this tree?
     *
     * @return string
     */
    public function gedcomRootId(): string;
}
