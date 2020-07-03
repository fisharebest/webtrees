<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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

namespace Fisharebest\Webtrees\Functions;

use Fisharebest\Webtrees\Age;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Factory;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomCode\GedcomCodeStat;
use Fisharebest\Webtrees\GedcomCode\GedcomCodeTemp;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\Header;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Submission;
use Fisharebest\Webtrees\Submitter;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use LogicException;
use Ramsey\Uuid\Uuid;

use function array_filter;
use function array_intersect;
use function array_merge;
use function array_search;
use function e;
use function explode;
use function in_array;
use function preg_match;
use function preg_match_all;
use function preg_replace_callback;
use function preg_split;
use function str_contains;
use function strip_tags;
use function strlen;
use function strpos;
use function strtoupper;
use function substr;
use function trim;
use function uasort;
use function view;

use const PREG_SET_ORDER;
use const PREG_SPLIT_NO_EMPTY;

/**
 * Class FunctionsPrint - common functions
 *
 * @deprecated since 2.0.6.  Will be removed in 2.1.0
 */
class FunctionsPrint
{
    /**
     * print a note record
     *
     * @param Tree   $tree
     * @param string $text
     * @param int    $nlevel the level of the note record
     * @param string $nrec   the note record to print
     *
     * @return string
     */
    private static function printNoteRecord(Tree $tree, $text, $nlevel, $nrec): string
    {
        $text .= Functions::getCont($nlevel, $nrec);

        if (preg_match('/^0 @(' . Gedcom::REGEX_XREF . ')@ NOTE/', $nrec, $match)) {
            // Shared note.
            $note = Factory::note()->make($match[1], $tree);
            // It must exist.
            assert($note instanceof Note);

            $label      = I18N::translate('Shared note');
            $html       = Filter::formatText($note->getNote(), $tree);
            $first_line = '<a href="' . e($note->url()) . '">' . $note->fullName() . '</a>';
        } else {
            // Inline note.
            $label = I18N::translate('Note');
            $html  = Filter::formatText($text, $tree);

            // Only one line?  Remove block-level attributes and skip expand/collapse.
            if (!str_contains($text, "\n")) {
                return
                    '<div class="fact_NOTE">' .
                    I18N::translate(
                        '<span class="label">%1$s:</span> <span class="field" dir="auto">%2$s</span>',
                        $label,
                        strip_tags($html, '<a><strong><em>')
                    ) .
                    '</div>';
            }

            [$text] = explode("\n", strip_tags($text));
            $first_line = Str::limit($text, 50, I18N::translate('…'));
        }

        $id       = 'collapse-' . Uuid::uuid4()->toString();
        $expanded = (bool) $tree->getPreference('EXPAND_NOTES');

        return
            '<div class="fact_NOTE">' .
            '<a href="#' . e($id) . '" role="button" data-toggle="collapse" aria-controls="' . e($id) . '" aria-expanded="' . ($expanded ? 'true' : 'false') . '">' .
            view('icons/expand') .
            view('icons/collapse') .
            '</a> ' .
            '<span class="label">' . $label . ':</span> ' .
            $first_line .
            '</div>' .
            '<div id="' . e($id) . '" class="collapse ' . ($expanded ? 'show' : '') . '">' .
            $html .
            '</div>';
    }

    /**
     * Print all of the notes in this fact record
     *
     * @param Tree   $tree
     * @param string $factrec The fact to print the notes from
     * @param int    $level   The level of the notes
     *
     * @return string HTML
     */
    public static function printFactNotes(Tree $tree, $factrec, $level): string
    {
        $data          = '';
        $previous_spos = 0;
        $nlevel        = $level + 1;
        $ct            = preg_match_all("/$level NOTE (.*)/", $factrec, $match, PREG_SET_ORDER);
        for ($j = 0; $j < $ct; $j++) {
            $spos1 = strpos($factrec, $match[$j][0], $previous_spos);
            $spos2 = strpos($factrec . "\n$level", "\n$level", $spos1 + 1);
            if (!$spos2) {
                $spos2 = strlen($factrec);
            }
            $previous_spos = $spos2;
            $nrec          = substr($factrec, $spos1, $spos2 - $spos1);
            if (!isset($match[$j][1])) {
                $match[$j][1] = '';
            }
            if (!preg_match('/^@(' . Gedcom::REGEX_XREF . ')@$/', $match[$j][1], $nmatch)) {
                $data .= self::printNoteRecord($tree, $match[$j][1], $nlevel, $nrec);
            } else {
                $note = Factory::note()->make($nmatch[1], $tree);
                if ($note) {
                    if ($note->canShow()) {
                        $noterec = $note->gedcom();
                        $nt      = preg_match("/0 @$nmatch[1]@ NOTE (.*)/", $noterec, $n1match);
                        $data    .= self::printNoteRecord($tree, $nt > 0 ? $n1match[1] : '', 1, $noterec);
                    }
                } else {
                    $data = '<div class="fact_NOTE"><span class="label">' . I18N::translate('Note') . '</span>: <span class="field error">' . $nmatch[1] . '</span></div>';
                }
            }
        }

        return $data;
    }

    /**
     * Format age of parents in HTML
     *
     * @param Individual $person child
     * @param Date       $birth_date
     *
     * @return string HTML
     */
    public static function formatParentsAges(Individual $person, Date $birth_date): string
    {
        $html     = '';
        $families = $person->childFamilies();
        // Multiple sets of parents (e.g. adoption) cause complications, so ignore.
        if ($birth_date->isOK() && $families->count() === 1) {
            $family = $families->first();
            foreach ($family->spouses() as $parent) {
                if ($parent->getBirthDate()->isOK()) {
                    $sex      = '<small>' . view('icons/sex', ['sex' => $parent->sex()]) . '</small>';
                    $age      = new Age($parent->getBirthDate(), $birth_date);
                    $deatdate = $parent->getDeathDate();
                    switch ($parent->sex()) {
                        case 'F':
                            // Highlight mothers who die in childbirth or shortly afterwards
                            if ($deatdate->isOK() && $deatdate->maximumJulianDay() < $birth_date->minimumJulianDay() + 90) {
                                $html .= ' <span title="' . I18N::translate('Death of a mother') . '" class="parentdeath">' . $sex . I18N::number($age->ageYears()) . '</span>';
                            } else {
                                $html .= ' <span title="' . I18N::translate('Mother’s age') . '">' . $sex . I18N::number($age->ageYears()) . '</span>';
                            }
                            break;
                        case 'M':
                            // Highlight fathers who die before the birth
                            if ($deatdate->isOK() && $deatdate->maximumJulianDay() < $birth_date->minimumJulianDay()) {
                                $html .= ' <span title="' . I18N::translate('Death of a father') . '" class="parentdeath">' . $sex . I18N::number($age->ageYears()) . '</span>';
                            } else {
                                $html .= ' <span title="' . I18N::translate('Father’s age') . '">' . $sex . I18N::number($age->ageYears()) . '</span>';
                            }
                            break;
                        default:
                            $html .= ' <span title="' . I18N::translate('Parent’s age') . '">' . $sex . I18N::number($age->ageYears()) . '</span>';
                            break;
                    }
                }
            }
            if ($html) {
                $html = '<span class="age">' . $html . '</span>';
            }
        }

        return $html;
    }

    /**
     * Convert a GEDCOM age string to localized text.
     *
     * @param string $age_string
     *
     * @return string
     */
    public static function formatGedcomAge(string $age_string): string
    {
        switch (strtoupper($age_string)) {
            case 'CHILD':
                return I18N::translate('Child');
            case 'INFANT':
                return I18N::translate('Infant');
            case 'STILLBORN':
                return I18N::translate('Stillborn');
            default:
                return (string) preg_replace_callback(
                    [
                        '/(\d+)([ymwd])/',
                    ],
                    static function (array $match): string {
                        $num = (int) $match[1];

                        switch ($match[2]) {
                            case 'y':
                                return I18N::plural('%s year', '%s years', $num, I18N::number($num));
                            case 'm':
                                return I18N::plural('%s month', '%s months', $num, I18N::number($num));
                            case 'w':
                                return I18N::plural('%s week', '%s weeks', $num, I18N::number($num));
                            case 'd':
                                return I18N::plural('%s day', '%s days', $num, I18N::number($num));
                            default:
                                throw new LogicException('Should never get here');
                        }
                    },
                    $age_string
                ) ;
        }
    }

    /**
     * Print fact DATE/TIME
     *
     * @param Fact         $event  event containing the date/age
     * @param GedcomRecord $record the person (or couple) whose ages should be printed
     * @param bool         $anchor option to print a link to calendar
     * @param bool         $time   option to print TIME value
     *
     * @return string
     */
    public static function formatFactDate(Fact $event, GedcomRecord $record, $anchor, $time): string
    {
        $factrec = $event->gedcom();
        $html    = '';
        // Recorded age
        if (preg_match('/\n2 AGE (.+)/', $factrec, $match)) {
            $fact_age = self::formatGedcomAge($match[1]);
        } else {
            $fact_age = '';
        }
        if (preg_match('/\n2 HUSB\n3 AGE (.+)/', $factrec, $match)) {
            $husb_age = self::formatGedcomAge($match[1]);
        } else {
            $husb_age = '';
        }
        if (preg_match('/\n2 WIFE\n3 AGE (.+)/', $factrec, $match)) {
            $wife_age = self::formatGedcomAge($match[1]);
        } else {
            $wife_age = '';
        }

        // Calculated age
        $fact = $event->getTag();
        if (preg_match('/\n2 DATE (.+)/', $factrec, $match)) {
            $date = new Date($match[1]);
            $html .= ' ' . $date->display($anchor);
            // time
            if ($time && preg_match('/\n3 TIME (.+)/', $factrec, $match)) {
                $html .= ' – <span class="date">' . $match[1] . '</span>';
            }
            if ($record instanceof Individual) {
                if (in_array($fact, Gedcom::BIRTH_EVENTS, true) && $record->tree()->getPreference('SHOW_PARENTS_AGE')) {
                    // age of parents at child birth
                    $html .= self::formatParentsAges($record, $date);
                }
                if ($fact !== 'BIRT' && $fact !== 'CHAN' && $fact !== '_TODO') {
                    // age at event
                    $birth_date = $record->getBirthDate();
                    // Can't use getDeathDate(), as this also gives BURI/CREM events, which
                    // wouldn't give the correct "days after death" result for people with
                    // no DEAT.
                    $death_event = $record->facts(['DEAT'])->first();
                    if ($death_event instanceof Fact) {
                        $death_date = $death_event->date();
                    } else {
                        $death_date = new Date('');
                    }
                    $ageText = '';
                    if ($fact === 'DEAT' || Date::compare($date, $death_date) <= 0 || !$record->isDead()) {
                        // Before death, print age
                        $age = (new Age($birth_date, $date))->ageAtEvent(false);
                        // Only show calculated age if it differs from recorded age
                        if ($age !== '') {
                            if ($fact_age !== '' && $fact_age !== $age) {
                                $ageText = $age;
                            } elseif ($fact_age === '' && $husb_age === '' && $wife_age === '') {
                                $ageText = $age;
                            } elseif ($husb_age !== '' && $husb_age !== $age && $record->sex() === 'M') {
                                $ageText = $age;
                            } elseif ($wife_age !== '' && $wife_age !== $age && $record->sex() === 'F') {
                                $ageText = $age;
                            }
                        }
                    }
                    if ($fact !== 'DEAT' && $death_date->isOK() && Date::compare($death_date, $date) < 0) {
                        // After death, print time since death
                        $ageText = (new Age($death_date, $date))->timeAfterDeath();
                        // Family events which occur after death are probably errors
                        if ($event->record() instanceof Family) {
                            $ageText .= view('icons/warning');
                        }
                    }
                    if ($ageText !== '') {
                        $html .= ' <span class="age">' . $ageText . '</span>';
                    }
                }
            }
        }
        // print gedcom ages
        $age_labels = [
            I18N::translate('Age')     => $fact_age,
            I18N::translate('Husband') => $husb_age,
            I18N::translate('Wife')    => $wife_age,
        ];

        foreach (array_filter($age_labels) as $label => $age) {
            $html .= ' <span class="label">' . $label . ':</span> <span class="age">' . $age . '</span>';
        }

        return $html;
    }

    /**
     * print fact PLACe TEMPle STATus
     *
     * @param Fact $event       gedcom fact record
     * @param bool $anchor      to print a link to placelist
     * @param bool $sub_records to print place subrecords
     * @param bool $lds         to print LDS TEMPle and STATus
     *
     * @return string HTML
     */
    public static function formatFactPlace(Fact $event, $anchor = false, $sub_records = false, $lds = false): string
    {
        $tree = $event->record()->tree();

        if ($anchor) {
            // Show the full place name, for facts/events tab
            $html = $event->place()->fullName(true);
        } else {
            // Abbreviate the place name, for chart boxes
            return $event->place()->shortName();
        }

        if ($sub_records) {
            $placerec = Functions::getSubRecord(2, '2 PLAC', $event->gedcom());
            if ($placerec !== '') {
                if (preg_match_all('/\n3 (?:_HEB|ROMN) (.+)/', $placerec, $matches)) {
                    foreach ($matches[1] as $match) {
                        $wt_place = new Place($match, $tree);
                        $html     .= ' - ' . $wt_place->fullName();
                    }
                }
                $map_lati = '';
                $cts      = preg_match('/\d LATI (.*)/', $placerec, $match);
                if ($cts > 0) {
                    $map_lati = $match[1];
                    $html     .= '<br><span class="label">' . I18N::translate('Latitude') . ': </span>' . $map_lati;
                }
                $map_long = '';
                $cts      = preg_match('/\d LONG (.*)/', $placerec, $match);
                if ($cts > 0) {
                    $map_long = $match[1];
                    $html     .= ' <span class="label">' . I18N::translate('Longitude') . ': </span>' . $map_long;
                }
                if ($map_lati && $map_long) {
                    $map_lati = trim(strtr($map_lati, 'NSEW,�', ' - -. ')); // S5,6789 ==> -5.6789
                    $map_long = trim(strtr($map_long, 'NSEW,�', ' - -. ')); // E3.456� ==> 3.456

                    $html .= '<a href="https://maps.google.com/maps?q=' . e($map_lati) . ',' . e($map_long) . '" rel="nofollow" title="' . I18N::translate('Google Maps™') . '">' .
                        view('icons/google-maps') .
                        '<span class="sr-only">' . I18N::translate('Google Maps™') . '</span>' .
                        '</a>';

                    $html .= '<a href="https://www.bing.com/maps/?lvl=15&cp=' . e($map_lati) . '~' . e($map_long) . '" rel="nofollow" title="' . I18N::translate('Bing Maps™') . '">' .
                        view('icons/bing-maps') .
                        '<span class="sr-only">' . I18N::translate('Bing Maps™') . '</span>' .
                        '</a>';

                    $html .= '<a href="https://www.openstreetmap.org/#map=15/' . e($map_lati) . '/' . e($map_long) . '" rel="nofollow" title="' . I18N::translate('OpenStreetMap™') . '">' .
                        view('icons/openstreetmap') .
                        '<span class="sr-only">' . I18N::translate('OpenStreetMap™') . '</span>' .
                        '</a>';
                }
                if (preg_match('/\d NOTE (.*)/', $placerec, $match)) {
                    $html .= '<br>' . self::printFactNotes($tree, $placerec, 3);
                }
            }
        }
        if ($lds) {
            if (preg_match('/2 TEMP (.*)/', $event->gedcom(), $match)) {
                $html .= '<br>' . I18N::translate('LDS temple') . ': ' . GedcomCodeTemp::templeName($match[1]);
            }
            if (preg_match('/2 STAT (.*)/', $event->gedcom(), $match)) {
                $html .= '<br>' . I18N::translate('Status') . ': ' . GedcomCodeStat::statusName($match[1]);
                if (preg_match('/3 DATE (.*)/', $event->gedcom(), $match)) {
                    $date = new Date($match[1]);
                    $html .= ', ' . GedcomTag::getLabel('STAT:DATE') . ': ' . $date->display();
                }
            }
        }

        return $html;
    }

    /**
     * Check for facts that may exist only once for a certain record type.
     * If the fact already exists in the second array, delete it from the first one.
     *
     * @param array<string>    $uniquefacts
     * @param Collection<Fact> $recfacts
     *
     * @return array<string>
     */
    public static function checkFactUnique(array $uniquefacts, Collection $recfacts): array
    {
        foreach ($recfacts as $factarray) {
            $fact = $factarray->getTag();

            $key = array_search($fact, $uniquefacts, true);
            if ($key !== false) {
                unset($uniquefacts[$key]);
            }
        }

        return $uniquefacts;
    }

    /**
     * Print a new fact box on details pages
     *
     * @param GedcomRecord     $record    the person, family, source etc the fact will be added to
     * @param Collection<Fact> $usedfacts an array of facts already used in this record
     * @param string           $type      the type of record INDI, FAM, SOUR etc
     *
     * @return void
     */
    public static function printAddNewFact(GedcomRecord $record, Collection $usedfacts, $type): void
    {
        $tree = $record->tree();

        // -- Add from pick list
        switch ($type) {
            case Individual::RECORD_TYPE:
                $addfacts    = preg_split('/[, ;:]+/', $tree->getPreference('INDI_FACTS_ADD'), -1, PREG_SPLIT_NO_EMPTY);
                $uniquefacts = preg_split('/[, ;:]+/', $tree->getPreference('INDI_FACTS_UNIQUE'), -1, PREG_SPLIT_NO_EMPTY);
                $quickfacts  = preg_split('/[, ;:]+/', $tree->getPreference('INDI_FACTS_QUICK'), -1, PREG_SPLIT_NO_EMPTY);
                break;

            case Family::RECORD_TYPE:
                $addfacts    = preg_split('/[, ;:]+/', $tree->getPreference('FAM_FACTS_ADD'), -1, PREG_SPLIT_NO_EMPTY);
                $uniquefacts = preg_split('/[, ;:]+/', $tree->getPreference('FAM_FACTS_UNIQUE'), -1, PREG_SPLIT_NO_EMPTY);
                $quickfacts  = preg_split('/[, ;:]+/', $tree->getPreference('FAM_FACTS_QUICK'), -1, PREG_SPLIT_NO_EMPTY);
                break;

            case Source::RECORD_TYPE:
                $addfacts    = preg_split('/[, ;:]+/', $tree->getPreference('SOUR_FACTS_ADD'), -1, PREG_SPLIT_NO_EMPTY);
                $uniquefacts = preg_split('/[, ;:]+/', $tree->getPreference('SOUR_FACTS_UNIQUE'), -1, PREG_SPLIT_NO_EMPTY);
                $quickfacts  = preg_split('/[, ;:]+/', $tree->getPreference('SOUR_FACTS_QUICK'), -1, PREG_SPLIT_NO_EMPTY);
                break;

            case Note::RECORD_TYPE:
                $addfacts    = preg_split('/[, ;:]+/', $tree->getPreference('NOTE_FACTS_ADD'), -1, PREG_SPLIT_NO_EMPTY);
                $uniquefacts = preg_split('/[, ;:]+/', $tree->getPreference('NOTE_FACTS_UNIQUE'), -1, PREG_SPLIT_NO_EMPTY);
                $quickfacts  = preg_split('/[, ;:]+/', $tree->getPreference('NOTE_FACTS_QUICK'), -1, PREG_SPLIT_NO_EMPTY);
                break;

            case Repository::RECORD_TYPE:
                $addfacts    = preg_split('/[, ;:]+/', $tree->getPreference('REPO_FACTS_ADD'), -1, PREG_SPLIT_NO_EMPTY);
                $uniquefacts = preg_split('/[, ;:]+/', $tree->getPreference('REPO_FACTS_UNIQUE'), -1, PREG_SPLIT_NO_EMPTY);
                $quickfacts  = preg_split('/[, ;:]+/', $tree->getPreference('REPO_FACTS_QUICK'), -1, PREG_SPLIT_NO_EMPTY);
                break;

            case Media::RECORD_TYPE:
                $addfacts    = ['NOTE'];
                $uniquefacts = [];
                $quickfacts  = [];
                break;

            case Submitter::RECORD_TYPE:
                $addfacts    = ['LANG', 'OBJE', 'NOTE', 'SHARED_NOTE'];
                $uniquefacts = ['ADDR', 'EMAIL', 'NAME', 'PHON', 'WWW'];
                $quickfacts  = [];
                break;

            case Submission::RECORD_TYPE:
                $addfacts    = ['NOTE', 'SHARED_NOTE'];
                $uniquefacts = ['FAMF', 'TEMP', 'ANCE', 'DESC', 'ORDI', 'SUBM'];
                $quickfacts  = [];
                break;

            case Header::RECORD_TYPE:
                $addfacts    = [];
                $uniquefacts = ['COPR', 'LANG', 'SUBM'];
                $quickfacts  = [];
                break;
            default:
                return;
        }
        $addfacts            = array_merge(self::checkFactUnique($uniquefacts, $usedfacts), $addfacts);
        $quickfacts          = array_intersect($quickfacts, $addfacts);
        $translated_addfacts = [];
        foreach ($addfacts as $addfact) {
            $translated_addfacts[$addfact] = GedcomTag::getLabel($record->tag() . ':' . $addfact);
        }
        uasort($translated_addfacts, static function (string $x, string $y): int {
            return I18N::strcasecmp(I18N::translate($x), I18N::translate($y));
        });

        echo view('edit/add-fact-row', [
            'add_facts'   => $translated_addfacts,
            'quick_facts' => $quickfacts,
            'record'      => $record,
            'tree'        => $tree,
        ]);
    }
}
