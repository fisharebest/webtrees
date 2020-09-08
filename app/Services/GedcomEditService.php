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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ServerRequestInterface;

use function array_merge;
use function array_unique;
use function assert;
use function count;
use function preg_match_all;
use function str_replace;
use function trim;

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
    public function handleUpdates(string $newged, $levelOverride = 'no'): string
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
                        if (($this->tag[$j] !== 'OBJE') || ($this->tag[$k] === 'FILE')) {
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
                        $newline .= ' @' . $this->text[$j] . '@';
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
     * Create a form to add a new fact.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     * @param string                 $fact
     *
     * @return string
     */
    public function addNewFact(ServerRequestInterface $request, Tree $tree, $fact): string
    {
        $params = (array) $request->getParsedBody();

        $FACT = $params[$fact];
        $DATE = $params[$fact . '_DATE'] ?? '';
        $PLAC = $params[$fact . '_PLAC'] ?? '';

        if ($DATE !== '' || $PLAC !== '' || $FACT !== '' && $FACT !== 'Y') {
            if ($FACT !== '' && $FACT !== 'Y') {
                $gedrec = "\n1 " . $fact . ' ' . $FACT;
            } else {
                $gedrec = "\n1 " . $fact;
            }
            if ($DATE !== '') {
                $gedrec .= "\n2 DATE " . $DATE;
            }
            if ($PLAC !== '') {
                $gedrec .= "\n2 PLAC " . $PLAC;

                if (preg_match_all('/(' . Gedcom::REGEX_TAG . ')/', $tree->getPreference('ADVANCED_PLAC_FACTS'), $match)) {
                    foreach ($match[1] as $tag) {
                        $TAG = $params[$fact . '_' . $tag];
                        if ($TAG !== '') {
                            $gedrec .= "\n3 " . $tag . ' ' . $TAG;
                        }
                    }
                }
                $LATI = $params[$fact . '_LATI'] ?? '';
                $LONG = $params[$fact . '_LONG'] ?? '';
                if ($LATI !== '' || $LONG !== '') {
                    $gedrec .= "\n3 MAP\n4 LATI " . $LATI . "\n4 LONG " . $LONG;
                }
            }
            if ((bool) ($params['SOUR_' . $fact] ?? false)) {
                return $this->updateSource($gedrec, 'yes');
            }

            return $gedrec;
        }

        if ($FACT === 'Y') {
            if ((bool) ($params['SOUR_' . $fact] ?? false)) {
                return $this->updateSource("\n1 " . $fact . ' Y', 'yes');
            }

            return "\n1 " . $fact . ' Y';
        }

        return '';
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
     * Create a form to add a sex record.
     *
     * @param ServerRequestInterface $request
     *
     * @return string
     */
    public function addNewSex(ServerRequestInterface $request): string
    {
        $params = (array) $request->getParsedBody();

        switch ($params['SEX']) {
            case 'M':
                return "\n1 SEX M";
            case 'F':
                return "\n1 SEX F";
            default:
                return "\n1 SEX U";
        }
    }

    /**
     * Assemble the pieces of a newly created record into gedcom
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return string
     */
    public function addNewName(ServerRequestInterface $request, Tree $tree): string
    {
        $params = (array) $request->getParsedBody();
        $gedrec = "\n1 NAME " . $params['NAME'];

        $tags = [
            'NPFX',
            'GIVN',
            'SPFX',
            'SURN',
            'NSFX',
            'NICK',
        ];

        if (preg_match_all('/(' . Gedcom::REGEX_TAG . ')/', $tree->getPreference('ADVANCED_NAME_FACTS'), $match)) {
            $tags = array_merge($tags, $match[1]);
        }

        // Paternal and Polish and Lithuanian surname traditions can also create a _MARNM
        $SURNAME_TRADITION = $tree->getPreference('SURNAME_TRADITION');
        if ($SURNAME_TRADITION === 'paternal' || $SURNAME_TRADITION === 'polish' || $SURNAME_TRADITION === 'lithuanian') {
            $tags[] = '_MARNM';
        }

        foreach (array_unique($tags) as $tag) {
            $TAG = $params[$tag];

            if ($TAG !== '') {
                $gedrec .= "\n2 " . $tag . ' ' . $TAG;
            }
        }

        return $gedrec;
    }
}
