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

namespace Fisharebest\Webtrees\Factories;

use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;

/**
 * Make a GedcomRecord object.
 */
abstract class AbstractGedcomRecordFactory
{
    /**
     * @param Tree $tree
     *
     * @return Collection<object>
     */
    protected function pendingChanges(Tree $tree): Collection
    {
        // Caution - this cache can be overwritten by GedcomExportService
        return Registry::cache()->array()->remember(__CLASS__ . $tree->id(), static function () use ($tree): Collection {
            return DB::table('change')
                ->where('gedcom_id', '=', $tree->id())
                ->where('status', '=', 'pending')
                ->orderBy('change_id')
                ->pluck('new_gedcom', 'xref');
        });
    }

    /**
     * We may have searched for X123, but found the record for x123.
     *
     * @param string $gedcom
     * @param string $xref
     *
     * @return string
     */
    protected function extractXref(string $gedcom, string $xref): string
    {
        if (preg_match('/^0 @(' . Gedcom::REGEX_XREF . ')@/', $gedcom, $match)) {
            return $match[1];
        }

        return $xref;
    }
}
