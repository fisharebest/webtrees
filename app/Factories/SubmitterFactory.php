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
use Fisharebest\Webtrees\Contracts\SubmitterFactoryInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Submitter;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use stdClass;

use function assert;
use function preg_match;

/**
 * Make a Submitter object.
 */
class SubmitterFactory extends AbstractGedcomRecordFactory implements SubmitterFactoryInterface
{
    private const TYPE_CHECK_REGEX = '/^0 @[^@]+@ ' . Submitter::RECORD_TYPE . '/';

    /**
     * Create a submitter.
     *
     * @param string      $xref
     * @param Tree        $tree
     * @param string|null $gedcom
     *
     * @return Submitter|null
     */
    public function make(string $xref, Tree $tree, string $gedcom = null): ?Submitter
    {
        return Registry::cache()->array()->remember(__CLASS__ . $xref . '@' . $tree->id(), function () use ($xref, $tree, $gedcom) {
            $gedcom  = $gedcom ?? $this->gedcom($xref, $tree);
            $pending = $this->pendingChanges($tree)->get($xref);

            if ($gedcom === null && ($pending === null || !preg_match(self::TYPE_CHECK_REGEX, $pending))) {
                return null;
            }

            $xref = $this->extractXref($gedcom ?? $pending, $xref);

            return new Submitter($xref, $gedcom ?? '', $pending, $tree);
        }, null, ['gedrec-' . $xref . '@' . $tree->id()]);
    }

    /**
     * Create a submitter from a row in the database.
     *
     * @param Tree $tree
     *
     * @return Closure
     */
    public function mapper(Tree $tree): Closure
    {
        return function (stdClass $row) use ($tree): Submitter {
            $submitter = $this->make($row->o_id, $tree, $row->o_gedcom);
            assert($submitter instanceof Submitter);

            return $submitter;
        };
    }

    /**
     * Create a submitter from raw GEDCOM data.
     *
     * @param string      $xref
     * @param string      $gedcom  an empty string for new/pending records
     * @param string|null $pending null for a record with no pending edits,
     *                             empty string for records with pending deletions
     * @param Tree        $tree
     *
     * @return Submitter
     */
    public function new(string $xref, string $gedcom, ?string $pending, Tree $tree): Submitter
    {
        return new Submitter($xref, $gedcom, $pending, $tree);
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
        return DB::table('other')
            ->where('o_id', '=', $xref)
            ->where('o_file', '=', $tree->id())
            ->where('o_type', '=', Submitter::RECORD_TYPE)
            ->value('o_gedcom');
    }
}
