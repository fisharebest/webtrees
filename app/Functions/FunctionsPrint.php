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

namespace Fisharebest\Webtrees\Functions;

use Fisharebest\Webtrees\Age;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Module\ModuleMapLinkInterface;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomEditService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

use function app;
use function array_filter;
use function e;
use function explode;
use function in_array;
use function preg_match;
use function preg_match_all;
use function str_contains;
use function strip_tags;
use function strlen;
use function strpos;
use function substr;
use function uasort;
use function view;

use const PREG_SET_ORDER;

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
    private static function printNoteRecord(Tree $tree, string $text, int $nlevel, string $nrec): string
    {
        $text .= Functions::getCont($nlevel, $nrec);

        if (preg_match('/^0 @(' . Gedcom::REGEX_XREF . ')@ NOTE/', $nrec, $match)) {
            // Shared note.
            $note = Registry::noteFactory()->make($match[1], $tree);
            // It must exist.
            assert($note instanceof Note);

            $label      = I18N::translate('Shared note');
            $html       = Registry::markdownFactory()->markdown($tree)->convertToHtml($note->getNote());
            $first_line = '<a href="' . e($note->url()) . '">' . $note->fullName() . '</a>';

            $one_line_only = strip_tags($note->fullName()) === strip_tags($note->getNote());
        } else {
            // Inline note.
            $label = I18N::translate('Note');
            $html  = Registry::markdownFactory()->markdown($tree)->convertToHtml($text);

            [$first_line] = explode("\n", strip_tags($html));
            // Use same logic as note objects
            $first_line = Str::limit($first_line, 100, I18N::translate('…'));

            $one_line_only = !str_contains($text, "\n") && mb_strlen($text) <= 100;
        }

        if ($one_line_only) {
            return
                '<div class="fact_NOTE">' .
                I18N::translate(
                    '<span class="label">%1$s:</span> <span class="field" dir="auto">%2$s</span>',
                    $label,
                    $first_line
                ) .
                '</div>';
        }

        $id       = 'collapse-' . Uuid::uuid4()->toString();
        $expanded = (bool) $tree->getPreference('EXPAND_NOTES');

        return
            '<div class="fact_NOTE">' .
            '<a href="#' . e($id) . '" role="button" data-bs-toggle="collapse" aria-controls="' . e($id) . '" aria-expanded="' . ($expanded ? 'true' : 'false') . '">' .
            view('icons/expand') .
            view('icons/collapse') .
            '</a>' .
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
    public static function printFactNotes(Tree $tree, string $factrec, int $level): string
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
                $note = Registry::noteFactory()->make($nmatch[1], $tree);
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
     * Print fact DATE/TIME
     *
     * @param Fact         $event  event containing the date/age
     * @param GedcomRecord $record the person (or couple) whose ages should be printed
     * @param bool         $anchor option to print a link to calendar
     * @param bool         $time   option to print TIME value
     *
     * @return string
     */
    public static function formatFactDate(Fact $event, GedcomRecord $record, bool $anchor, bool $time): string
    {
        $element_factory = Registry::elementFactory();

        $factrec = $event->gedcom();
        $html    = '';
        // Recorded age
        if (preg_match('/\n2 AGE (.+)/', $factrec, $match)) {
            $fact_age = $element_factory->make($event->tag() . ':AGE')->value($match[1], $record->tree());
        } else {
            $fact_age = '';
        }
        if (preg_match('/\n2 HUSB\n3 AGE (.+)/', $factrec, $match)) {
            $husb_age = $element_factory->make($event->tag() . ':HUSB:AGE')->value($match[1], $record->tree());
        } else {
            $husb_age = '';
        }
        if (preg_match('/\n2 WIFE\n3 AGE (.+)/', $factrec, $match)) {
            $wife_age = $element_factory->make($event->tag() . ':WIFE:AGE')->value($match[1], $record->tree());
        } else {
            $wife_age = '';
        }

        // Calculated age
        [, $fact] = explode(':', $event->tag());

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
                        $age = (string) new Age($birth_date, $date);

                        // Only show calculated age if it differs from recorded age
                        if ($age !== '') {
                            if (
                                $fact_age !== '' && !str_starts_with($fact_age, $age) ||
                                $fact_age === '' && $husb_age === '' && $wife_age === '' ||
                                $husb_age !== '' && !str_starts_with($husb_age, $age) && $record->sex() === 'M' ||
                                $wife_age !== '' && !str_starts_with($wife_age, $age) && $record->sex() === 'F'
                            ) {
                                switch ($record->sex()) {
                                    case 'M':
                                        /* I18N: The age of an individual at a given date */
                                        $ageText = I18N::translateContext('Male', '(aged %s)', $age);
                                        break;
                                    case 'F':
                                        /* I18N: The age of an individual at a given date */
                                        $ageText = I18N::translateContext('Female', '(aged %s)', $age);
                                        break;
                                    default:
                                        /* I18N: The age of an individual at a given date */
                                        $ageText = I18N::translate('(aged %s)', $age);
                                        break;
                                }
                            }
                        }
                    }
                    if ($fact !== 'DEAT' && $death_date->isOK() && Date::compare($death_date, $date) <= 0) {
                        $death_day = $death_date->minimumDate()->day();
                        $event_day = $date->minimumDate()->day();
                        if ($death_day !== 0 && $event_day !== 0 && Date::compare($death_date, $date) === 0) {
                            // On the exact date of death?
                            // NOTE: this path is never reached.  Keep the code (translation) in case
                            // we decide to re-introduce it.
                            $ageText = I18N::translate('(on the date of death)');
                        } else {
                            // After death
                            $age = (string) new Age($death_date, $date);
                            $ageText = I18N::translate('(%s after death)', $age);
                        }
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
    public static function formatFactPlace(Fact $event, bool $anchor, bool $sub_records, bool $lds): string
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

                $latitude  = $event->latitude();
                $longitude = $event->longitude();

                if ($latitude !== null && $longitude !== null) {
                    $html .= '<br><span class="label">' . I18N::translate('Latitude') . ': </span>' . $latitude;
                    $html .= ' <span class="label">' . I18N::translate('Longitude') . ': </span>' . $longitude;

                    // Links to external maps
                    $html .= app(ModuleService::class)
                        ->findByInterface(ModuleMapLinkInterface::class)
                        ->map(fn (ModuleMapLinkInterface $module): string => ' ' . $module->mapLink($event))
                        ->implode('');
                }

                if (preg_match('/\d NOTE (.*)/', $placerec, $match)) {
                    $html .= '<br>' . self::printFactNotes($tree, $placerec, 3);
                }
            }
        }
        if ($lds) {
            if (preg_match('/2 TEMP (.*)/', $event->gedcom(), $match)) {
                $element = Registry::elementFactory()->make($event->tag() . ':TEMP');
                $html .= $element->labelValue($match[1], $tree);
            }
            if (preg_match('/2 STAT (.*)/', $event->gedcom(), $match)) {
                $element = Registry::elementFactory()->make($event->tag() . ':STAT');
                $html .= $element->labelValue($match[1], $tree);
                if (preg_match('/3 DATE (.*)/', $event->gedcom(), $match)) {
                    $date = new Date($match[1]);
                    $element = Registry::elementFactory()->make($event->tag() . ':STAT:DATE');
                    $html .= $element->labelValue($date->display(), $tree);
                }
            }
        }

        return $html;
    }

    /**
     * Print a new fact box on details pages
     *
     * @param Individual|Family $record
     *
     * @return void
     */
    public static function printAddNewFact(GedcomRecord $record): void
    {
        $tree = $record->tree();

        $add_facts = (new GedcomEditService())->factsToAdd($record, false);

        // Add from pick list
        switch ($record->tag()) {
            case Individual::RECORD_TYPE:
                $quick_facts  = explode(',', $tree->getPreference('INDI_FACTS_QUICK'));
                $unique_facts = [
                    'ADOP',
                    'AFN',
                    'BAPL',
                    'BAPM',
                    'BARM',
                    'BASM',
                    'BIRT',
                    'BURI',
                    'CAST',
                    'CHAN',
                    'CHR',
                    'CHRA',
                    'CONF',
                    'CONL',
                    'CREM',
                    'DEAT',
                    'ENDL',
                    'FCOM',
                    'GRAD',
                    'NCHI',
                    'NMR',
                    'ORDN',
                    'PROB',
                    'REFN',
                    'RELI',
                    'RESN',
                    'RETI',
                    'RFN',
                    'RIN',
                    'SEX',
                    'SLGC',
                    'SSN',
                    'WILL',
                ];
                break;

            case Family::RECORD_TYPE:
                $quick_facts  = explode(',', $tree->getPreference('FAM_FACTS_QUICK'));
                $unique_facts = [
                    'DIV',
                    'DIVF',
                    'ENGA',
                    'MARR',
                ];
                break;

            default:
                $quick_facts  = [];
                $unique_facts = [];
                break;
        }

        // Filter existing tags
        $filter_fn = static fn (string $tag): bool => !in_array($tag, $unique_facts, true) || $record->facts([$tag])->isEmpty();

        $quick_facts = array_filter($quick_facts, $filter_fn);


        // Create a label for a subtag
        $label_fn = static fn (string $subtag): string => Registry::elementFactory()->make($record->tag() . ':' . $subtag)->label();

        $quick_facts = array_combine($quick_facts, array_map($label_fn, $quick_facts));
        $add_facts   = array_combine($add_facts, array_map($label_fn, $add_facts));

        uasort($add_facts, I18N::comparator());

        if ((int) $record->tree()->getPreference('MEDIA_UPLOAD') < Auth::accessLevel($record->tree())) {
            unset($add_facts['OBJE'], $quick_facts['OBJE']);
        }

        echo view('edit/add-fact-row', [
            'add_facts'   => $add_facts,
            'quick_facts' => $quick_facts,
            'record'      => $record,
        ]);
    }
}
