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

namespace Fisharebest\Webtrees\Module\BatchUpdate;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\GedcomRecord;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Capsule\Manager as DB;
use Fisharebest\Webtrees\Module\BatchUpdate\BatchUpdateBasePlugin;

/**
 * Class BatchUpdatePrimaryMediaPlugin Batch Update plugin: add missing 1 BIRT/DEAT Y
 */
class BatchUpdatePrimaryMediaPlugin extends BatchUpdateBasePlugin
{
     /** @var array Array of xref of media objects marked as primary */
     private $primary_media;

    /**
     * User-friendly name for this plugin.
     *
     * @return string
     */
    public function getName(): string
    {
        return I18N::translate('Convert Level 1 Primary Media from webtrees 1');
    }

    /**
     * Description / help-text for this plugin.
     *
     * @return string
     */
    public function description(): string
    {
        return I18N::translate('Find Level 1 Primary Media Objects from webtrees 1 and put these objects in the gedcom before the other media objects. These objects will return in webtrees 2 as preferred images in the caroussel, on charts etc.');
    }

    /**
     * Does this record need updating?
     *
     * @param GedcomRecord $record
     *
     * @return bool
     */
    public function doesRecordNeedUpdate(GedcomRecord $record): bool
    {
        $rows = DB::table('media')
            ->join('link', static function (JoinClause $join): void {
                $join
                    ->on('l_file', '=', 'm_file')
                    ->on('l_to', '=', 'm_id');
            })
            ->where('l_type', '=', 'OBJE')
            ->where('l_from', '=', $record->xref())
            ->where('m_file', '=', $record->tree()->id())
            ->where('m_gedcom', 'LIKE', '%1 _PRIM Y%')
            ->pluck('m_id')->toArray();

        $update = false;
        // We should check if there is just one instance of '1 OBJE' in the gedcom or
        // if the primary object(s) found in the database already are at the top of the object list
        // In that case there is no need to reorder images (we preserve the order of the primary images in the current gedcom)
        preg_match_all('/^(1 OBJE @).+/m', $record->gedcom(), $matches); // $matches[0] contains the full match
        foreach ($rows as $row) {
            $primary_media = '1 OBJE @' . $row . '@';
            if (in_array($primary_media, $matches[0])) {
                $key = array_search ($primary_media, $matches[0]);
                $update = ($key >= count($rows)) ? true : false;
            }
             // Store the found items as Gedcom row in a class array to retrieve them later in the process
            $this->primary_media[$record->xref()][]  = $primary_media;

        }
        return $record instanceof Individual && count($rows) > 0 && count($matches[0]) > 1 && $update;
    }

    /**
     * Apply any updates to this record
     *
     * @param GedcomRecord $record
     *
     * @return string
     */
    public function updateRecord(GedcomRecord $record): string
    {

        $tree = $record->tree();
        assert($tree instanceof Tree);

        $xref = $record->xref();
        assert(is_string($xref));

        $individual = Individual::getInstance($xref, $tree);

        $dummy_facts = ['0 @' . $individual->xref() . '@ INDI'];
        $primary_facts = [];
        $other_facts  = [];

        // Put the primary object in a separate array so we can inject it before the other facts.
        foreach ($individual->facts() as $fact) {
            // preserve the order in the Gedcom
            if (in_array($fact->gedcom(), $this->primaryMedia($xref))) {
                $primary_facts[] = $fact->gedcom();
            } else {
                $other_facts[] = $fact->gedcom();
            }
        }

        // Merge the facts
        $gedcom = implode("\n", array_merge($dummy_facts, $primary_facts, $other_facts));

        return $gedcom;
    }

    private function primaryMedia($xref): array {
        return $this->primary_media[$xref];
    }
}
