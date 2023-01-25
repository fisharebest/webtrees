<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Algorithm\MyersDiff;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Submitter;
use Fisharebest\Webtrees\Tree;

use function e;
use function explode;
use function implode;
use function preg_replace_callback;
use function strip_tags;

/**
 * Bulk updates on genealogy data
 */
class DataFixService
{
    /**
     * Since we know the type, this is quicker than calling Registry::gedcomRecordFactory()->make().
     *
     * @param string $xref
     * @param Tree   $tree
     * @param string $type
     *
     * @return GedcomRecord|null
     */
    public function getRecordByType(string $xref, Tree $tree, string $type): ?GedcomRecord
    {
        switch ($type) {
            case Family::RECORD_TYPE:
                return Registry::familyFactory()->make($xref, $tree);

            case Individual::RECORD_TYPE:
                return Registry::individualFactory()->make($xref, $tree);

            case Note::RECORD_TYPE:
                return Registry::noteFactory()->make($xref, $tree);

            case Media::RECORD_TYPE:
                return Registry::mediaFactory()->make($xref, $tree);

            case Repository::RECORD_TYPE:
                return Registry::repositoryFactory()->make($xref, $tree);

            case Source::RECORD_TYPE:
                return Registry::sourceFactory()->make($xref, $tree);

            case Submitter::RECORD_TYPE:
                return Registry::submitterFactory()->make($xref, $tree);

            default:
                return Registry::gedcomRecordFactory()->make($xref, $tree);
        }
    }

    /**
     * Default preview generator.
     *
     * @param Tree   $tree
     * @param string $old_gedcom
     * @param string $new_gedcom
     *
     * @return string
     */
    public function gedcomDiff(Tree $tree, string $old_gedcom, string $new_gedcom): string
    {
        $old_lines   = explode("\n", $old_gedcom);
        $new_lines   = explode("\n", $new_gedcom);
        $algorithm   = new MyersDiff();
        $differences = $algorithm->calculate($old_lines, $new_lines);
        $diff_lines  = [];

        foreach ($differences as $difference) {
            switch ($difference[1]) {
                case MyersDiff::DELETE:
                    $diff_lines[] = '<del>' . e($difference[0]) . '</del><br>';
                    break;
                case MyersDiff::INSERT:
                    $diff_lines[] = '<ins>' . e($difference[0]) . '</ins><br>';
                    break;
                case MyersDiff::KEEP:
                    $diff_lines[] = e($difference[0]) . '<br>';
                    break;
            }
        }

        $html = implode('', $diff_lines);

        $html = preg_replace_callback('/@(' . Gedcom::REGEX_XREF . ')@/', static function (array $match) use ($tree): string {
            $record = Registry::gedcomRecordFactory()->make($match[0], $tree);

            if ($record instanceof GedcomRecord) {
                $title = strip_tags($record->fullName());
                $href  = e($record->url());

                return '<a href="' . $href . '" title="' . $title . '">' . $match[0] . '</a>';
            }

            return $match[0];
        }, $html);

        return '<pre class="gedcom-data">' . $html . '</pre>';
    }
}
