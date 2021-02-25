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

use Closure;
use Fisharebest\Webtrees\Contracts\SourceFactoryInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use stdClass;

use function assert;
use function preg_match;

/**
 * Make a Source object.
 */
class SourceFactory extends AbstractGedcomRecordFactory implements SourceFactoryInterface
{
    private const TYPE_CHECK_REGEX = '/^0 @[^@]+@ ' . Source::RECORD_TYPE . '/';


    /**
     * Create a individual.
     *
     * @param string      $xref
     * @param Tree        $tree
     * @param string|null $gedcom
     *
     * @return Source|null
     */
    public function make(string $xref, Tree $tree, string $gedcom = null): ?Source
    {
        return Registry::cache()->array()->remember(__CLASS__ . $xref . '@' . $tree->id(), function () use ($xref, $tree, $gedcom) {
            $gedcom  = $gedcom ?? $this->gedcom($xref, $tree);
            $pending = $this->pendingChanges($tree)->get($xref);

            if ($gedcom === null && ($pending === null || !preg_match(self::TYPE_CHECK_REGEX, $pending))) {
                return null;
            }

            $xref = $this->extractXref($gedcom ?? $pending, $xref);

            return new Source($xref, $gedcom ?? '', $pending, $tree);
        }, null, ['gedrec-' . $xref . '@' . $tree->id()]);
    }

    /**
     * Create a source from a row in the database.
     *
     * @param Tree $tree
     *
     * @return Closure
     */
    public function mapper(Tree $tree): Closure
    {
        return function (stdClass $row) use ($tree): Source {
            $source = $this->make($row->s_id, $tree, $row->s_gedcom);
            assert($source instanceof Source);

            return $source;
        };
    }

    /**
     * Create a source from raw GEDCOM data.
     *
     * @param string      $xref
     * @param string      $gedcom  an empty string for new/pending records
     * @param string|null $pending null for a record with no pending edits,
     *                             empty string for records with pending deletions
     * @param Tree        $tree
     *
     * @return Source
     */
    public function new(string $xref, string $gedcom, ?string $pending, Tree $tree): Source
    {
        return new Source($xref, $gedcom, $pending, $tree);
    }

    /**
     * Fetch GEDCOM data from the database.
     *
     * @param string $xref
     * @param Tree   $tree
     *
     * @return string|null
     */
    protected function gedcom(string $xref, Tree $tree): ?string
    {
        return DB::table('sources')
            ->where('s_id', '=', $xref)
            ->where('s_file', '=', $tree->id())
            ->value('s_gedcom');
    }
}
