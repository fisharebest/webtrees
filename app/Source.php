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

namespace Fisharebest\Webtrees;

use Closure;
use Exception;
use Fisharebest\Webtrees\Http\RequestHandlers\SourcePage;
use Illuminate\Database\Capsule\Manager as DB;
use stdClass;

/**
 * A GEDCOM source (SOUR) object.
 */
class Source extends GedcomRecord
{
    public const RECORD_TYPE = 'SOUR';

    protected const ROUTE_NAME  = SourcePage::class;

    /**
     * A closure which will create a record from a database row.
     *
     * @return Closure
     */
    public static function rowMapper(): Closure
    {
        return static function (stdClass $row): Source {
            return Source::getInstance($row->s_id, Tree::findById((int) $row->s_file), $row->s_gedcom);
        };
    }

    /**
     * Get an instance of a source object. For single records,
     * we just receive the XREF. For bulk records (such as lists
     * and search results) we can receive the GEDCOM data as well.
     *
     * @param string      $xref
     * @param Tree        $tree
     * @param string|null $gedcom
     *
     * @throws Exception
     *
     * @return Source|null
     */
    public static function getInstance(string $xref, Tree $tree, string $gedcom = null): ?self
    {
        $record = parent::getInstance($xref, $tree, $gedcom);

        if ($record instanceof self) {
            return $record;
        }

        return null;
    }

    /**
     * Each object type may have its own special rules, and re-implement this function.
     *
     * @param int $access_level
     *
     * @return bool
     */
    protected function canShowByType(int $access_level): bool
    {
        // Hide sources if they are attached to private repositories ...
        preg_match_all('/\n1 REPO @(.+)@/', $this->gedcom, $matches);
        foreach ($matches[1] as $match) {
            $repo = Repository::getInstance($match, $this->tree);
            if ($repo && !$repo->canShow($access_level)) {
                return false;
            }
        }

        // ... otherwise apply default behavior
        return parent::canShowByType($access_level);
    }

    /**
     * Generate a private version of this record
     *
     * @param int $access_level
     *
     * @return string
     */
    protected function createPrivateGedcomRecord(int $access_level): string
    {
        return '0 @' . $this->xref . "@ SOUR\n1 TITL " . I18N::translate('Private');
    }

    /**
     * Fetch data from the database
     *
     * @param string $xref
     * @param int    $tree_id
     *
     * @return string|null
     */
    protected static function fetchGedcomRecord(string $xref, int $tree_id): ?string
    {
        return DB::table('sources')
            ->where('s_id', '=', $xref)
            ->where('s_file', '=', $tree_id)
            ->value('s_gedcom');
    }

    /**
     * Extract names from the GEDCOM record.
     *
     * @return void
     */
    public function extractNames(): void
    {
        $this->extractNamesFromFacts(1, 'TITL', $this->facts(['TITL']));
    }
}
