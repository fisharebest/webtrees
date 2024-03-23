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

namespace Fisharebest\Webtrees\Factories;

use Closure;
use Fisharebest\Webtrees\Contracts\FamilyFactoryInterface;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;

use function preg_match;

/**
 * Make a Family object.
 */
class FamilyFactory extends AbstractGedcomRecordFactory implements FamilyFactoryInterface
{
    private const TYPE_CHECK_REGEX = '/^0 @[^@]+@ ' . Family::RECORD_TYPE . '/';

    /**
     * Create a family.
     *
     * @param string      $xref
     * @param Tree        $tree
     * @param string|null $gedcom
     *
     * @return Family|null
     */
    public function make(string $xref, Tree $tree, string|null $gedcom = null): Family|null
    {
        return Registry::cache()->array()->remember(self::class . $xref . '@' . $tree->id(), function () use ($xref, $tree, $gedcom) {
            $gedcom ??= $this->gedcom($xref, $tree);
            $pending = $this->pendingChanges($tree)->get($xref);

            if ($gedcom === null && ($pending === null || !preg_match(self::TYPE_CHECK_REGEX, $pending))) {
                return null;
            }

            $xref = $this->extractXref($gedcom ?? $pending, $xref);

            // Preload all the family members using a single database query.
            preg_match_all('/\n1 (?:HUSB|WIFE|CHIL) @(' . Gedcom::REGEX_XREF . ')@/', $gedcom . "\n" . $pending, $match);
            DB::table('individuals')
                ->where('i_file', '=', $tree->id())
                ->whereIn('i_id', $match[1])
                ->get()
                ->map(Registry::individualFactory()->mapper($tree));

            return $this->new($xref, $gedcom ?? '', $pending, $tree);
        });
    }

    /**
     * Create a Family object from a row in the database.
     *
     * @param Tree $tree
     *
     * @return Closure(object):Family
     */
    public function mapper(Tree $tree): Closure
    {
        return fn (object $row): Family => $this->make($row->f_id, $tree, $row->f_gedcom);
    }

    /**
     * Create a Family object from raw GEDCOM data.
     *
     * @param string      $xref
     * @param string      $gedcom  an empty string for new/pending records
     * @param string|null $pending null for a record with no pending edits,
     *                             empty string for records with pending deletions
     * @param Tree        $tree
     *
     * @return Family
     */
    public function new(string $xref, string $gedcom, string|null $pending, Tree $tree): Family
    {
        return new Family($xref, $gedcom, $pending, $tree);
    }

    /**
     * Fetch GEDCOM data from the database.
     *
     * @param string $xref
     * @param Tree   $tree
     *
     * @return string|null
     */
    protected function gedcom(string $xref, Tree $tree): string|null
    {
        return DB::table('families')
            ->where('f_id', '=', $xref)
            ->where('f_file', '=', $tree->id())
            ->value('f_gedcom');
    }
}
