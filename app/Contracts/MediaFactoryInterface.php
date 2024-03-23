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

namespace Fisharebest\Webtrees\Contracts;

use Closure;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Tree;

/**
 * Make a Media object.
 */
interface MediaFactoryInterface extends GedcomRecordFactoryInterface
{
    /**
     * Create a media object.
     */
    public function make(string $xref, Tree $tree, string|null $gedcom = null): Media|null;

    /**
     * Create a media object from a row in the database.
     *
     * @param Tree $tree
     *
     * @return Closure(object):Media
     */
    public function mapper(Tree $tree): Closure;

    /**
     * Create a media object from raw GEDCOM data.
     *
     * @param string      $xref
     * @param string      $gedcom  an empty string for new/pending records
     * @param string|null $pending null for a record with no pending edits,
     *                             empty string for records with pending deletions
     * @param Tree        $tree
     *
     * @return Media
     */
    public function new(string $xref, string $gedcom, string|null $pending, Tree $tree): Media;
}
