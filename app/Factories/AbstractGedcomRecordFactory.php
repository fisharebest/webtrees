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

namespace Fisharebest\Webtrees\Factories;

use Fisharebest\Webtrees\Cache;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;
use stdClass;

/**
 * Make a GedcomRecord object.
 */
abstract class AbstractGedcomRecordFactory
{
    /** @var Cache */
    protected $cache;

    /**
     * GedcomRecordFactory constructor.
     *
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param Tree $tree
     *
     * @return Collection<stdClass>
     */
    protected function pendingChanges(Tree $tree): Collection
    {
        return $this->cache->remember(__CLASS__ . $tree->id(), static function () use ($tree): Collection {
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
     * @return mixed|string
     */
    protected function extractXref(string $gedcom, string $xref)
    {
        if (preg_match('/^0 @(' . Gedcom::REGEX_XREF . ')@/', $gedcom, $match)) {
            return $match[1];
        }

        return $xref;
    }
}
