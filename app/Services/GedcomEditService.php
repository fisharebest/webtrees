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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\Elements\AbstractXrefElement;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;

use function array_diff;
use function array_filter;
use function array_keys;
use function array_merge;
use function array_shift;
use function array_slice;
use function array_values;
use function assert;
use function count;
use function explode;
use function implode;
use function max;
use function preg_replace;
use function preg_split;
use function str_repeat;
use function str_replace;
use function substr_count;

use const ARRAY_FILTER_USE_BOTH;
use const ARRAY_FILTER_USE_KEY;
use const PHP_INT_MAX;

/**
 * Utilities to edit/save GEDCOM data.
 */
class GedcomEditService
{
    /**
     * Reassemble edited GEDCOM fields into a GEDCOM fact/event string.
     *
     * @param string        $record_type
     * @param array<string> $levels
     * @param array<string> $tags
     * @param array<string> $values
     *
     * @return string
     */
    public function editLinesToGedcom(string $record_type, array $levels, array $tags, array $values): string
    {
        // Assert all arrays are the same size.
        $count = count($levels);
        assert($count > 0);
        assert(count($tags) === $count);
        assert(count($values) === $count);

        $gedcom_lines = [];
        $hierarchy    = [$record_type];

        for ($i = 0; $i < $count; $i++) {
            $hierarchy[$levels[$i]] = $tags[$i];

            $full_tag   = implode(':', array_slice($hierarchy, 0, 1 + (int) $levels[$i]));
            $element    = Registry::elementFactory()->make($full_tag);
            $values[$i] = $element->canonical($values[$i]);

            // If "1 FACT Y" has a DATE or PLAC, then delete the value of Y
            if ($levels[$i] === '1' && $values[$i] === 'Y') {
                for ($j = $i + 1; $j < $count && $levels[$j] > $levels[$i]; ++$j) {
                    if ($levels[$j] === '2' && ($tags[$j] === 'DATE' || $tags[$j] === 'PLAC') && $values[$j] !== '') {
                        $values[$i] = '';
                        break;
                    }
                }
            }

            // Find the next tag at the same level.  Check if any child tags have values.
            $children_with_values = false;
            for ($j = $i + 1; $j < $count && $levels[$j] > $levels[$i]; $j++) {
                if ($values[$j] !== '') {
                    $children_with_values = true;
                }
            }

            if ($values[$i] !== '' || $children_with_values  && !$element instanceof AbstractXrefElement) {
                if ($values[$i] === '') {
                    $gedcom_lines[] = $levels[$i] . ' ' . $tags[$i];
                } else {
                    // We use CONC for editing NOTE records.
                    if ($tags[$i] === 'CONC') {
                        $next_level = (int) $levels[$i];
                    } else {
                        $next_level = 1 + (int) $levels[$i];
                    }

                    $gedcom_lines[] = $levels[$i] . ' ' . $tags[$i] . ' ' . str_replace("\n", "\n" . $next_level . ' CONT ', $values[$i]);
                }
            } else {
                $i = $j - 1;
            }
        }

        return implode("\n", $gedcom_lines);
    }

    /**
     * Add blank lines, to allow a user to add/edit new values.
     *
     * @param Fact $fact
     * @param bool $include_hidden
     *
     * @return string
     */
    public function insertMissingFactSubtags(Fact $fact, bool $include_hidden): string
    {
        return $this->insertMissingLevels($fact->record()->tree(), $fact->tag(), $fact->gedcom(), $include_hidden);
    }

    /**
     * Add blank lines, to allow a user to add/edit new values.
     *
     * @param GedcomRecord $record
     * @param bool         $include_hidden
     *
     * @return string
     */
    public function insertMissingRecordSubtags(GedcomRecord $record, bool $include_hidden): string
    {
        $gedcom = $this->insertMissingLevels($record->tree(), $record->tag(), $record->gedcom(), $include_hidden);

        // NOTE records have data at level 0.  Move it to 1 CONC.
        if ($record instanceof Note) {
            return preg_replace('/^0 @[^@]+@ NOTE/', '1 CONC', $gedcom);
        }

        return preg_replace('/^0.*\n/', '', $gedcom);
    }

    /**
     * List of facts/events to add to families and individuals.
     *
     * @param Family|Individual $record
     * @param bool              $include_hidden
     *
     * @return array<string>
     */
    public function factsToAdd(GedcomRecord $record, bool $include_hidden): array
    {
        $subtags = Registry::elementFactory()->make($record->tag())->subtags();

        $subtags = array_filter($subtags, static fn (string $v, string $k) => !str_ends_with($v, ':1') || $record->facts([$k])->isEmpty(), ARRAY_FILTER_USE_BOTH);

        $subtags = array_keys($subtags);

        // Don't include facts/events that we have hidden in the control panel.
        $subtags = array_filter($subtags, fn (string $subtag): bool => !$this->isHiddenTag($record->tag() . ':' . $subtag));

        if (!$include_hidden) {
            $fn_hidden = fn (string $t): bool => !$this->isHiddenTag($record->tag() . ':' . $t);
            $subtags   = array_filter($subtags, $fn_hidden);
        }

        return array_diff($subtags, ['HUSB', 'WIFE', 'CHIL', 'FAMC', 'FAMS', 'CHAN']);
    }

    /**
     * @param Tree   $tree
     * @param string $tag
     * @param string $gedcom
     * @param bool   $include_hidden
     *
     * @return string
     */
    protected function insertMissingLevels(Tree $tree, string $tag, string $gedcom, bool $include_hidden): string
    {
        $next_level = substr_count($tag, ':') + 1;
        $factory    = Registry::elementFactory();
        $subtags    = $factory->make($tag)->subtags();

        // Merge CONT records onto their parent line.
        $gedcom = strtr($gedcom, [
            "\n" . $next_level . ' CONT ' => "\r",
            "\n" . $next_level . ' CONT' => "\r",
        ]);

        // The first part is level N.  The remainder are level N+1.
        $parts  = preg_split('/\n(?=' . $next_level . ')/', $gedcom);
        $return = array_shift($parts) ?? '';

        foreach ($subtags as $subtag => $occurrences) {
            if (!$include_hidden && $this->isHiddenTag($tag . ':' . $subtag)) {
                continue;
            }

            [$min, $max] = explode(':', $occurrences);

            $min = (int) $min;

            if ($max === 'M') {
                $max = PHP_INT_MAX;
            } else {
                $max = (int) $max;
            }

            $count = 0;

            // Add expected subtags in our preferred order.
            foreach ($parts as $n => $part) {
                if (str_starts_with($part, $next_level . ' ' . $subtag)) {
                    $return .= "\n" . $this->insertMissingLevels($tree, $tag . ':' . $subtag, $part, $include_hidden);
                    $count++;
                    unset($parts[$n]);
                }
            }

            // Allowed to have more of this subtag?
            if ($count < $max) {
                // Create a new one.
                $gedcom  = $next_level . ' ' . $subtag;
                $default = $factory->make($tag . ':' . $subtag)->default($tree);
                if ($default !== '') {
                    $gedcom .= ' ' . $default;
                }

                $number_to_add = max(1, $min - $count);
                $gedcom_to_add = "\n" . $this->insertMissingLevels($tree, $tag . ':' . $subtag, $gedcom, $include_hidden);

                $return .= str_repeat($gedcom_to_add, $number_to_add);
            }
        }

        // Now add any unexpected/existing data.
        if ($parts !== []) {
            $return .= "\n" . implode("\n", $parts);
        }

        return $return;
    }

    /**
     * List of tags to exclude when creating new data.
     *
     * @param string $tag
     *
     * @return bool
     */
    private function isHiddenTag(string $tag): bool
    {
        // Function to filter hidden tags.
        $fn_hide = static fn (string $x): bool => (bool) Site::getPreference('HIDE_' . $x);

        $preferences = array_filter(Gedcom::HIDDEN_TAGS, $fn_hide, ARRAY_FILTER_USE_KEY);
        $preferences = array_values($preferences);
        $hidden_tags = array_merge(...$preferences);

        foreach ($hidden_tags as $hidden_tag) {
            if (str_contains($tag, $hidden_tag)) {
                return true;
            }
        }

        return false;
    }
}
