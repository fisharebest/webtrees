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

use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;

use function array_filter;
use function array_merge;
use function array_shift;
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
use function trim;

use const ARRAY_FILTER_USE_BOTH;
use const ARRAY_FILTER_USE_KEY;
use const PHP_INT_MAX;

/**
 * Utilities to edit/save GEDCOM data.
 */
class GedcomEditService
{
    /** @var string[] */
    public $glevels = [];

    /** @var string[] */
    public $tag = [];

    /** @var string[] */
    public $islink = [];

    /** @var string[] */
    public $text = [];

    /** @var string[] */
    protected $glevelsSOUR = [];

    /** @var string[] */
    protected $tagSOUR = [];

    /** @var string[] */
    protected $islinkSOUR = [];

    /** @var string[] */
    protected $textSOUR = [];

    /** @var string[] */
    protected $glevelsRest = [];

    /** @var string[] */
    protected $tagRest = [];

    /** @var string[] */
    protected $islinkRest = [];

    /** @var string[] */
    protected $textRest = [];

    /**
     * This function splits the $glevels, $tag, $islink, and $text arrays so that the
     * entries associated with a SOUR record are separate from everything else.
     *
     * Input arrays:
     * - $glevels[] - an array of the gedcom level for each line that was edited
     * - $tag[] - an array of the tags for each gedcom line that was edited
     * - $islink[] - an array of 1 or 0 values to indicate when the text is a link element
     * - $text[] - an array of the text data for each line
     *
     * Output arrays:
     * ** For the SOUR record:
     * - $glevelsSOUR[] - an array of the gedcom level for each line that was edited
     * - $tagSOUR[] - an array of the tags for each gedcom line that was edited
     * - $islinkSOUR[] - an array of 1 or 0 values to indicate when the text is a link element
     * - $textSOUR[] - an array of the text data for each line
     * ** For the remaining records:
     * - $glevelsRest[] - an array of the gedcom level for each line that was edited
     * - $tagRest[] - an array of the tags for each gedcom line that was edited
     * - $islinkRest[] - an array of 1 or 0 values to indicate when the text is a link element
     * - $textRest[] - an array of the text data for each line
     *
     * @return void
     */
    public function splitSource(): void
    {
        $this->glevelsSOUR = [];
        $this->tagSOUR     = [];
        $this->islinkSOUR  = [];
        $this->textSOUR    = [];

        $this->glevelsRest = [];
        $this->tagRest     = [];
        $this->islinkRest  = [];
        $this->textRest    = [];

        $inSOUR    = false;
        $levelSOUR = 0;

        // Assume all arrays are the same size.
        $count = count($this->glevels);

        for ($i = 0; $i < $count; $i++) {
            if ($inSOUR) {
                if ($levelSOUR < $this->glevels[$i]) {
                    $dest = 'S';
                } else {
                    $inSOUR = false;
                    $dest   = 'R';
                }
            } elseif ($this->tag[$i] === 'SOUR') {
                $inSOUR    = true;
                $levelSOUR = $this->glevels[$i];
                $dest      = 'S';
            } else {
                $dest = 'R';
            }

            if ($dest === 'S') {
                $this->glevelsSOUR[] = $this->glevels[$i];
                $this->tagSOUR[]     = $this->tag[$i];
                $this->islinkSOUR[]  = $this->islink[$i];
                $this->textSOUR[]    = $this->text[$i];
            } else {
                $this->glevelsRest[] = $this->glevels[$i];
                $this->tagRest[]     = $this->tag[$i];
                $this->islinkRest[]  = $this->islink[$i];
                $this->textRest[]    = $this->text[$i];
            }
        }
    }

    /**
     * Add new GEDCOM lines from the $xxxRest interface update arrays, which
     * were produced by the splitSOUR() function.
     * See the FunctionsEdit::handle_updatesges() function for details.
     *
     * @param string $inputRec
     *
     * @return string
     */
    public function updateRest(string $inputRec): string
    {
        if (count($this->tagRest) === 0) {
            return $inputRec; // No update required
        }

        // Save original interface update arrays before replacing them with the xxxRest ones
        $glevelsSave = $this->glevels;
        $tagSave     = $this->tag;
        $islinkSave  = $this->islink;
        $textSave    = $this->text;

        $this->glevels = $this->glevelsRest;
        $this->tag     = $this->tagRest;
        $this->islink  = $this->islinkRest;
        $this->text    = $this->textRest;

        $myRecord = $this->handleUpdates($inputRec, 'no'); // Now do the update

        // Restore the original interface update arrays (just in case ...)
        $this->glevels = $glevelsSave;
        $this->tag     = $tagSave;
        $this->islink  = $islinkSave;
        $this->text    = $textSave;

        return $myRecord;
    }

    /**
     * Add new gedcom lines from interface update arrays
     * The edit_interface and FunctionsEdit::add_simple_tag function produce the following
     * arrays incoming from the $_POST form
     * - $glevels[] - an array of the gedcom level for each line that was edited
     * - $tag[] - an array of the tags for each gedcom line that was edited
     * - $islink[] - an array of 1 or 0 values to tell whether the text is a link element and should be surrounded by @@
     * - $text[] - an array of the text data for each line
     * With these arrays you can recreate the gedcom lines like this
     * <code>$glevel[0].' '.$tag[0].' '.$text[0]</code>
     * There will be an index in each of these arrays for each line of the gedcom
     * fact that is being edited.
     * If the $text[] array is empty for the given line, then it means that the
     * user removed that line during editing or that the line is supposed to be
     * empty (1 DEAT, 1 BIRT) for example. To know if the line should be removed
     * there is a section of code that looks ahead to the next lines to see if there
     * are sub lines. For example we don't want to remove the 1 DEAT line if it has
     * a 2 PLAC or 2 DATE line following it. If there are no sub lines, then the line
     * can be safely removed.
     *
     * @param string $newged        the new gedcom record to add the lines to
     * @param string $levelOverride Override GEDCOM level specified in $glevels[0]
     *
     * @return string The updated gedcom record
     */
    public function handleUpdates(string $newged, string $levelOverride = 'no'): string
    {
        if ($levelOverride === 'no') {
            $levelAdjust = 0;
        } else {
            $levelAdjust = 1;
        }

        // Assert all arrays are the same size.
        assert(count($this->glevels) === count($this->tag));
        assert(count($this->glevels) === count($this->text));
        assert(count($this->glevels) === count($this->islink));

        $count = count($this->glevels);

        for ($j = 0; $j < $count; $j++) {
            // Look for empty SOUR reference with non-empty sub-records.
            // This can happen when the SOUR entry is deleted but its sub-records
            // were incorrectly left intact.
            // The sub-records should be deleted.
            if ($this->tag[$j] === 'SOUR' && ($this->text[$j] === '@@' || $this->text[$j] === '')) {
                $this->text[$j] = '';
                $k              = $j + 1;
                while ($k < $count && $this->glevels[$k] > $this->glevels[$j]) {
                    $this->text[$k] = '';
                    $k++;
                }
            }

            if (trim($this->text[$j]) !== '') {
                $pass = true;
            } else {
                //-- for facts with empty values they must have sub records
                //-- this section checks if they have subrecords
                $k    = $j + 1;
                $pass = false;
                while ($k < $count && $this->glevels[$k] > $this->glevels[$j]) {
                    if ($this->text[$k] !== '') {
                        if ($this->tag[$j] !== 'OBJE' || $this->tag[$k] === 'FILE') {
                            $pass = true;
                            break;
                        }
                    }
                    $k++;
                }
            }

            //-- if the value is not empty or it has sub lines
            //--- then write the line to the gedcom record
            //-- we have to let some emtpy text lines pass through... (DEAT, BIRT, etc)
            if ($pass) {
                $newline = (int) $this->glevels[$j] + $levelAdjust . ' ' . $this->tag[$j];
                if ($this->text[$j] !== '') {
                    if ($this->islink[$j]) {
                        $newline .= ' @' . trim($this->text[$j], '@') . '@';
                    } else {
                        $newline .= ' ' . $this->text[$j];
                    }
                }
                $next_level = 1 + (int) $this->glevels[$j] + $levelAdjust;

                $newged .= "\n" . str_replace("\n", "\n" . $next_level . ' CONT ', $newline);
            }
        }

        return $newged;
    }

    /**
     * Add new GEDCOM lines from the $xxxSOUR interface update arrays, which
     * were produced by the splitSOUR() function.
     * See the FunctionsEdit::handle_updatesges() function for details.
     *
     * @param string $inputRec
     * @param string $levelOverride
     *
     * @return string
     */
    public function updateSource(string $inputRec, string $levelOverride = 'no'): string
    {
        if (count($this->tagSOUR) === 0) {
            return $inputRec; // No update required
        }

        // Save original interface update arrays before replacing them with the xxxSOUR ones
        $glevelsSave = $this->glevels;
        $tagSave     = $this->tag;
        $islinkSave  = $this->islink;
        $textSave    = $this->text;

        $this->glevels = $this->glevelsSOUR;
        $this->tag     = $this->tagSOUR;
        $this->islink  = $this->islinkSOUR;
        $this->text    = $this->textSOUR;

        $myRecord = $this->handleUpdates($inputRec, $levelOverride); // Now do the update

        // Restore the original interface update arrays (just in case ...)
        $this->glevels = $glevelsSave;
        $this->tag     = $tagSave;
        $this->islink  = $islinkSave;
        $this->text    = $textSave;

        return $myRecord;
    }

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

            // Include this line if there is a value - or if there is a child record with a value.
            $include = $values[$i] !== '';

            for ($j = $i + 1; !$include && $j < $count && $levels[$j] > $levels[$i]; $j++) {
                $include = $values[$j] !== '';
            }

            if ($include) {
                if ($values[$i] === '') {
                    $gedcom_lines[] = $levels[$i] . ' ' . $tags[$i];
                } else {
                    if ($tags[$i] === 'CONC') {
                        $next_level = (int) $levels[$i];
                    } else {
                        $next_level = 1 + (int) $levels[$i];
                    }

                    $gedcom_lines[] = $levels[$i] . ' ' . $tags[$i] . ' ' . str_replace("\n", "\n" . $next_level . ' CONT ', $values[$i]);
                }
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

        $subtags = array_filter($subtags, fn (string $v, string $k) => !str_ends_with($v, ':1') || $record->facts([$k])->isEmpty(), ARRAY_FILTER_USE_BOTH);

        $subtags = array_keys($subtags);

        if (!$include_hidden) {
            $fn_hidden = fn (string $t): bool => !$this->isHiddenTag($record->tag() . ':' . $t);
            $subtags   = array_filter($subtags, $fn_hidden);
        }

        $subtags = array_diff($subtags, ['HUSB', 'WIFE', 'CHIL', 'FAMC', 'FAMS', 'CHAN']);

        return $subtags;
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
        $return = array_shift($parts);

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
        $fn_hide = fn (string $x): bool => (bool) Site::getPreference('HIDE_' . $x);

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
