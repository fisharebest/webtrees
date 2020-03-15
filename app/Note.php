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
use Fisharebest\Webtrees\Http\RequestHandlers\NotePage;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Str;
use stdClass;

/**
 * A GEDCOM note (NOTE) object.
 */
class Note extends GedcomRecord
{
    public const RECORD_TYPE = 'NOTE';

    protected const ROUTE_NAME = NotePage::class;

    /**
     * A closure which will create a record from a database row.
     *
     * @param Tree $tree
     *
     * @return Closure
     */
    public static function rowMapper(Tree $tree): Closure
    {
        return static function (stdClass $row) use ($tree): Note {
            $note = Note::getInstance($row->o_id, $tree, $row->o_gedcom);
            assert($note instanceof Note);

            return $note;
        };
    }

    /**
     * Get an instance of a note object. For single records,
     * we just receive the XREF. For bulk records (such as lists
     * and search results) we can receive the GEDCOM data as well.
     *
     * @param string      $xref
     * @param Tree        $tree
     * @param string|null $gedcom
     *
     * @throws Exception
     *
     * @return Note|null
     */
    public static function getInstance(string $xref, Tree $tree, string $gedcom = null): ?Note
    {
        $record = parent::getInstance($xref, $tree, $gedcom);

        if ($record instanceof self) {
            return $record;
        }

        return null;
    }

    /**
     * Get the text contents of the note
     *
     * @return string
     */
    public function getNote(): string
    {
        if (preg_match('/^0 @' . Gedcom::REGEX_XREF . '@ NOTE ?(.*(?:\n1 CONT ?.*)*)/', $this->gedcom . $this->pending, $match)) {
            return preg_replace("/\n1 CONT ?/", "\n", $match[1]);
        }

        return '';
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
        // Hide notes if they are attached to private records
        $linked_ids = DB::table('link')
            ->where('l_file', '=', $this->tree->id())
            ->where('l_to', '=', $this->xref)
            ->pluck('l_from');

        foreach ($linked_ids as $linked_id) {
            $linked_record = GedcomRecord::getInstance($linked_id, $this->tree);
            if ($linked_record && !$linked_record->canShow($access_level)) {
                return false;
            }
        }

        // Apply default behavior
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
        return '0 @' . $this->xref . '@ NOTE ' . I18N::translate('Private');
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
     * Create a name for this note - apply (and remove) markup, then take
     * a maximum of 100 characters from the first non-empty line.
     *
     * @return void
     */
    public function extractNames(): void
    {
        $text = trim($this->getNote());

        [$text] = explode("\n", $text);

        if ($text !== '') {
            $this->addName('NOTE', Str::limit($text, 100, I18N::translate('…')), $this->gedcom());
        }
    }
}
