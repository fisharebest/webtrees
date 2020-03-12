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
use Fisharebest\Webtrees\Http\RequestHandlers\SubmissionPage;
use Illuminate\Database\Capsule\Manager as DB;
use stdClass;

/**
 * A GEDCOM submission (SUBN) object.
 * These records are only used when transferring data between two obsolete systems.
 * There is no need to create them - but we may encounter them in imported GEDCOM files.
 */
class Submission extends GedcomRecord
{
    public const RECORD_TYPE = 'SUBN';

    protected const ROUTE_NAME = SubmissionPage::class;

    /**
     * A closure which will create a record from a database row.
     *
     * @param Tree $tree
     *
     * @return Closure
     */
    public static function rowMapper(Tree $tree): Closure
    {
        return static function (stdClass $row) use ($tree): Submission {
            $submission = Submission::getInstance($row->o_id, $tree, $row->o_gedcom);
            assert($submission instanceof Submission);

            return $submission;
        };
    }

    /**
     * Get an instance of a submission object. For single records,
     * we just receive the XREF. For bulk records (such as lists
     * and search results) we can receive the GEDCOM data as well.
     *
     * @param string      $xref
     * @param Tree        $tree
     * @param string|null $gedcom
     *
     * @throws Exception
     *
     * @return Submission|null
     */
    public static function getInstance(string $xref, Tree $tree, string $gedcom = null): ?Submission
    {
        $record = parent::getInstance($xref, $tree, $gedcom);

        if ($record instanceof self) {
            return $record;
        }

        return null;
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
        return DB::table('other')
            ->where('o_id', '=', $xref)
            ->where('o_file', '=', $tree_id)
            ->where('o_type', '=', self::RECORD_TYPE)
            ->value('o_gedcom');
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
        return '0 @' . $this->xref . "@ SUBM\n1 NAME " . I18N::translate('Private');
    }
    /**
     * Extract names from the GEDCOM record.
     *
     * @return void
     */
    public function extractNames(): void
    {
        $this->getAllNames[] = [
            'type'   => self::RECORD_TYPE,
            'sort'   => I18N::translate('Submission'),
            'full'   => I18N::translate('Submission'),
            'fullNN' => I18N::translate('Submission'),
        ];
    }
}
