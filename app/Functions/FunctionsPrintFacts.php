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
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\Elements\UnknownElement;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\Http\RequestHandlers\EditFactPage;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Module\ModuleChartInterface;
use Fisharebest\Webtrees\Module\ModuleInterface;
use Fisharebest\Webtrees\Module\RelationshipsChartModule;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Tree;
use Ramsey\Uuid\Uuid;

use function app;
use function array_merge;
use function count;
use function e;
use function explode;
use function implode;
use function ob_get_clean;
use function ob_start;
use function preg_match;
use function preg_match_all;
use function preg_replace;
use function route;
use function str_contains;
use function strip_tags;
use function strlen;
use function strpos;
use function substr;
use function trim;
use function view;

use const PREG_SET_ORDER;

/**
 * Class FunctionsPrintFacts - common functions
 *
 * @deprecated since 2.0.6.  Will be removed in 2.1.0
 */
class FunctionsPrintFacts
{
    /**
     * Print a fact record, for the individual/family/source/repository/etc. pages.
     * Although a Fact has a parent object, we also need to know
     * the GedcomRecord for which we are printing it. For example,
     * we can show the death of X on the page of Y, or the marriage
     * of X+Y on the page of Z. We need to know both records to
     * calculate ages, relationships, etc.
     *
     * @param Fact         $fact
     * @param GedcomRecord $record
     *
     * @return void
     */
    public static function printFact(Fact $fact, GedcomRecord $record): void
    {
        $parent = $fact->record();
        $tree   = $parent->tree();
        $tag    = $fact->getTag();
        $label  = $fact->label();
        $value  = $fact->value();
        $type   = $fact->attribute('TYPE');
        $id     = $fact->id();

        $element = Registry::elementFactory()->make($fact->tag());

        // This preference is named HIDE instead of SHOW
        $hide_errors = $tree->getPreference('HIDE_GEDCOM_ERRORS') === '0';

        // Some facts don't get printed here ...
        switch ($tag) {
            case 'NOTE':
                self::printMainNotes($fact, 1);

                return;
            case 'SOUR':
                self::printMainSources($fact, 1);

                return;
            case 'OBJE':
                self::printMainMedia($fact, 1);

                return;
            case 'FAMC':
            case 'FAMS':
            case 'CHIL':
            case 'HUSB':
            case 'WIFE':
                // These are internal links, not facts
                return;
            case '_WT_OBJE_SORT':
                // These links were once used internally to record the sort order.
                return;
            default:
                if ($element instanceof UnknownElement && $hide_errors) {
                    return;
                }
                break;
        }

        // New or deleted facts need different styling
        $styles = [];
        if ($fact->isPendingAddition()) {
            $styles[] = 'wt-new';
        }
        if ($fact->isPendingDeletion()) {
            $styles[] = 'wt-old';
        }

        // Event of close relative
        if ($tag === 'EVEN' && $value === 'CLOSE_RELATIVE') {
            $styles[] = 'wt-relation-fact collapse';
        }

        // Event of close associates
        if ($id === 'asso') {
            $styles[] = 'wt-relation-fact collapse';
        }

        // historical facts
        if ($id === 'histo') {
            $styles[] = 'wt-historic-fact collapse';
        }

        // Use marriage type as the label.  e.g. "Civil partnership"
        if ($tag === 'MARR') {
            $label = $fact->label();
            $type  = '';
        }

        echo '<tr class="', implode(' ', $styles), '">';
        echo '<th scope="row">';
        echo $label;

        if ($id !== 'histo' && $id !== 'asso' && $fact->canEdit()) {
            echo '<div class="editfacts nowrap">';
            echo view('edit/icon-fact-edit', ['fact' => $fact, 'url' => $record->url()]);
            echo view('edit/icon-fact-copy', ['fact' => $fact]);
            echo view('edit/icon-fact-delete', ['fact' => $fact]);
            echo '</div>';
        }

        if ($tree->getPreference('SHOW_FACT_ICONS')) {
            echo '<span class="wt-fact-icon wt-fact-icon-' . $tag . '" title="' . strip_tags($label) . '"></span>';
        }

        echo '</th>';
        echo '<td>';

        // Event from another record?
        if ($parent !== $record) {
            if ($parent instanceof Family) {
                foreach ($parent->spouses() as $spouse) {
                    if ($record !== $spouse) {
                        echo '<a href="', e($spouse->url()), '">', $spouse->fullName(), '</a> — ';
                    }
                }
                echo '<a href="', e($parent->url()), '">', I18N::translate('View this family'), '</a><br>';
            } elseif ($parent instanceof Individual) {
                echo '<a href="', e($parent->url()), '">', $parent->fullName(), '</a><br>';
            }
        }

        // Print the value of this fact/event
        // we handle ASSO later, in formatAssociateRelationship()
        if ($tag !== 'ASSO') {
            echo '<div class="field">', $element->value($value, $tree), '</div>';
        }

        // Print the type of this fact/event
        if ($type !== '' && $tag !== 'EVEN' && $tag !== 'FACT') {
            // Allow (custom) translations for other types
            $type = I18N::translate($type);
            echo GedcomTag::getLabelValue('TYPE', e($type));
        }

        // Print the date of this fact/event
        echo FunctionsPrint::formatFactDate($fact, $record, true, true);

        // Print the place of this fact/event
        echo '<div class="place">', FunctionsPrint::formatFactPlace($fact, true, true, true), '</div>';
        // A blank line between the primary attributes (value, date, place) and the secondary ones
        echo '<br>';

        $addr = $fact->attribute('ADDR');
        if ($addr !== '') {
            $addr = e($addr);
            if (str_contains($addr, "\n")) {
                $addr = '<span class="d-block" style="white-space: pre-wrap">' . $addr . '</span';
            }

            echo GedcomTag::getLabelValue($fact->tag() . ':ADDR', $addr);
        }

        // Print the associates of this fact/event
        if ($id !== 'asso') {
            echo self::formatAssociateRelationship($fact);
        }

        // Print any other "2 XXXX" attributes, in the order in which they appear.
        preg_match_all('/\n2 (' . Gedcom::REGEX_TAG . ') ?(.*)((\n[3-9].*)*)/', $fact->gedcom(), $l2_matches, PREG_SET_ORDER);

        foreach ($l2_matches as $l2_match) {
            switch ($l2_match[1]) {
                case 'DATE':
                case 'TIME':
                case 'AGE':
                case 'HUSB':
                case 'WIFE':
                case 'PLAC':
                case 'ADDR':
                case 'ALIA':
                case 'ASSO':
                case '_ASSO':
                case 'DESC':
                case 'RELA':
                case 'STAT':
                case 'TEMP':
                case 'TYPE':
                case 'FAMS':
                case 'CONT':
                    // These were already shown at the beginning
                    break;
                case 'NOTE':
                case 'OBJE':
                case 'SOUR':
                    // These will be shown at the end
                    break;
                default:
                    $subtag = $record::RECORD_TYPE . ':' . $tag . ':' . $l2_match[1];
                    $element = Registry::elementFactory()->make($subtag);

                    if ($element instanceof UnknownElement && $hide_errors) {
                        break;
                    }

                    echo $element->labelValue($l2_match[2], $tree);

                    preg_match_all('/\n3 (' . Gedcom::REGEX_TAG . ') ?(.*)((\n[4-9].*)*)/', $l2_match[3], $l3_matches, PREG_SET_ORDER);

                    foreach ($l3_matches as $l3_match) {
                        $subsubtag = $subtag . ':' . $l3_match[1];
                        $element   = Registry::elementFactory()->make($subsubtag);

                        if ($element instanceof UnknownElement && $hide_errors) {
                            continue;
                        }

                        echo $element->labelValue($l3_match[2], $tree);

                        preg_match_all('/\n4 (' . Gedcom::REGEX_TAG . ') ?(.*)((\n[5-9].*)*)/', $l3_match[3], $l4_matches, PREG_SET_ORDER);

                        foreach ($l4_matches as $l4_match) {
                            $subsubsubtag = $subsubtag . ':' . $l4_match[1];
                            $element = Registry::elementFactory()->make($subsubsubtag);

                            if ($element instanceof UnknownElement && $hide_errors) {
                                continue;
                            }

                            echo $element->labelValue($l4_match[2], $tree);

                            preg_match_all('/\n5 (' . Gedcom::REGEX_TAG . ') ?(.*)((\n[6-9].*)*)/', $l4_match[3], $l5_matches, PREG_SET_ORDER);

                            foreach ($l5_matches as $l5_match) {
                                $subsubsubsubtag = $subsubsubtag . ':' . $l5_match[1];
                                $element = Registry::elementFactory()->make($subsubsubsubtag);

                                if ($element instanceof UnknownElement && $hide_errors) {
                                    continue;
                                }

                                echo $element->labelValue($l5_match[2], $tree);
                            }
                        }
                    }
                    break;
            }
        }
        echo self::printFactSources($tree, $fact->gedcom(), 2);
        echo FunctionsPrint::printFactNotes($tree, $fact->gedcom(), 2);
        self::printMediaLinks($tree, $fact->gedcom(), 2);
        echo '</td></tr>';
    }

    /**
     * Print the associations from the associated individuals in $event to the individuals in $record
     *
     * @param Fact $event
     *
     * @return string
     */
    private static function formatAssociateRelationship(Fact $event): string
    {
        $parent = $event->record();
        // To whom is this record an assocate?
        if ($parent instanceof Individual) {
            // On an individual page, we just show links to the person
            $associates = [$parent];
        } elseif ($parent instanceof Family) {
            // On a family page, we show links to both spouses
            $associates = $parent->spouses();
        } else {
            // On other pages, it does not make sense to show associates
            return '';
        }

        preg_match_all('/^1 ASSO @(' . Gedcom::REGEX_XREF . ')@((\n[2-9].*)*)/', $event->gedcom(), $amatches1, PREG_SET_ORDER);
        preg_match_all('/\n2 _?ASSO @(' . Gedcom::REGEX_XREF . ')@((\n[3-9].*)*)/', $event->gedcom(), $amatches2, PREG_SET_ORDER);

        $html = '';
        // For each ASSO record
        foreach (array_merge($amatches1, $amatches2) as $amatch) {
            $person = Registry::individualFactory()->make($amatch[1], $event->record()->tree());
            if ($person && $person->canShowName()) {
                // Is there a "RELA" tag
                if (preg_match('/\n([23]) RELA (.+)/', $amatch[2], $rmatch)) {
                    if ($rmatch[1] === '2') {
                        $base_tag = $event->record()->tag();
                    } else {
                        $base_tag = $event->tag();
                    }
                    // Use the supplied relationship as a label
                    $label = Registry::elementFactory()->make($base_tag . ':_ASSO:RELA')->value($rmatch[2], $parent->tree());
                } elseif (preg_match('/^1 _?ASSO/', $event->gedcom())) {
                    // Use a default label
                    $label = Registry::elementFactory()->make($event->tag())->label();
                } else {
                    // Use a default label
                    $label = Registry::elementFactory()->make($event->tag() . ':_ASSO')->label();
                }

                if ($person->getBirthDate()->isOK() && $event->date()->isOK()) {
                    $age = new Age($person->getBirthDate(), $event->date());
                    switch ($person->sex()) {
                        case 'M':
                            $age_text = ' ' . I18N::translateContext('Male', '(aged %s)', (string) $age);
                            break;
                        case 'F':
                            $age_text = ' ' . I18N::translateContext('Female', '(aged %s)', (string) $age);
                            break;
                        default:
                            $age_text = ' ' . I18N::translate('(aged %s)', (string) $age);
                            break;
                    }
                } else {
                    $age_text = '';
                }

                $values = ['<a href="' . e($person->url()) . '">' . $person->fullName() . '</a>' . $age_text];

                $module = app(ModuleService::class)->findByComponent(ModuleChartInterface::class, $person->tree(), Auth::user())->first(static function (ModuleInterface $module) {
                    return $module instanceof RelationshipsChartModule;
                });

                if ($module instanceof RelationshipsChartModule) {
                    foreach ($associates as $associate) {
                        $relationship_name = Functions::getCloseRelationshipName($associate, $person);
                        if ($relationship_name === '') {
                            $relationship_name = GedcomTag::getLabel('RELA');
                        }

                        if ($parent instanceof Family) {
                            // For family ASSO records (e.g. MARR), identify the spouse with a sex icon
                            $relationship_name .= '<small>' . view('icons/sex', ['sex' => $associate->sex()]) . '</small>';
                        }

                        $values[] = '<a href="' . $module->chartUrl($associate, ['xref2' => $person->xref()]) . '" rel="nofollow">' . $relationship_name . '</a>';
                    }
                }
                $value = implode(' — ', $values);

                // Use same markup as GedcomTag::getLabelValue()
                $asso = I18N::translate('<span class="label">%1$s:</span> <span class="field" dir="auto">%2$s</span>', $label, $value);
            } elseif (!$person && Auth::isEditor($event->record()->tree())) {
                $asso = GedcomTag::getLabelValue('ASSO', '<span class="error">' . $amatch[1] . '</span>');
            } else {
                $asso = '';
            }
            $html .= '<div class="fact_ASSO">' . $asso . '</div>';
        }

        return $html;
    }

    /**
     * print a source linked to a fact (2 SOUR)
     * this function is called by the FunctionsPrintFacts::print_fact function and other functions to
     * print any source information attached to the fact
     *
     * @param Tree   $tree
     * @param string $factrec The fact record to look for sources in
     * @param int    $level   The level to look for sources at
     *
     * @return string HTML text
     */
    public static function printFactSources(Tree $tree, string $factrec, int $level): string
    {
        $data   = '';
        $nlevel = $level + 1;

        // Systems not using source records
        // The old style is not supported when entering or editing sources, but may be found in imported trees.
        // Also, the old style sources allow histo.* files to use tree independent source citations, which
        // will display nicely when markdown is used.
        $ct = preg_match_all('/' . $level . ' SOUR (.*)((?:\n\d CONT.*)*)/', $factrec, $match, PREG_SET_ORDER);
        for ($j = 0; $j < $ct; $j++) {
            if (!str_contains($match[$j][1], '@')) {
                $source = e($match[$j][1] . preg_replace('/\n\d CONT ?/', "\n", $match[$j][2]));
                $data   .= '<div class="fact_SOUR"><span class="label">' . I18N::translate('Source') . ':</span> <span class="field" dir="auto">' . Filter::formatText($source, $tree) . '</span></div>';
            }
        }
        // Find source for each fact
        $ct    = preg_match_all("/$level SOUR @(.*)@/", $factrec, $match, PREG_SET_ORDER);
        $spos2 = 0;
        for ($j = 0; $j < $ct; $j++) {
            $sid    = $match[$j][1];
            $source = Registry::sourceFactory()->make($sid, $tree);
            if ($source) {
                if ($source->canShow()) {
                    $spos1 = strpos($factrec, "$level SOUR @" . $sid . '@', $spos2);
                    $spos2 = strpos($factrec, "\n$level", $spos1);
                    if (!$spos2) {
                        $spos2 = strlen($factrec);
                    }
                    $srec     = substr($factrec, $spos1, $spos2 - $spos1);
                    $lt       = preg_match_all("/$nlevel \w+/", $srec, $matches);
                    $data     .= '<div class="fact_SOUR">';
                    $id       = 'collapse-' . Uuid::uuid4()->toString();
                    $expanded = (bool) $tree->getPreference('EXPAND_SOURCES');
                    if ($lt > 0) {
                        $data .= '<a href="#' . e($id) . '" role="button" data-toggle="collapse" aria-controls="' . e($id) . '" aria-expanded="' . ($expanded ? 'true' : 'false') . '">';
                        $data .= view('icons/expand');
                        $data .= view('icons/collapse');
                        $data .= '</a>';
                    }
                    $data .= GedcomTag::getLabelValue('SOUR', '<a href="' . e($source->url()) . '">' . $source->fullName() . '</a>', null, 'span');
                    $data .= '</div>';

                    $data .= '<div id="' . e($id) . '" class="collapse ' . ($expanded ? 'show' : '') . '">';
                    $data .= self::printSourceStructure($tree, self::getSourceStructure($srec));
                    $data .= '<div class="indent">';
                    ob_start();
                    self::printMediaLinks($tree, $srec, $nlevel);
                    $data .= ob_get_clean();
                    $data .= FunctionsPrint::printFactNotes($tree, $srec, $nlevel);
                    $data .= '</div>';
                    $data .= '</div>';
                }
            } else {
                $data .= GedcomTag::getLabelValue('SOUR', '<span class="error">' . $sid . '</span>');
            }
        }

        return $data;
    }

    /**
     * Print the links to media objects
     *
     * @param Tree   $tree
     * @param string $factrec
     * @param int    $level
     *
     * @return void
     */
    public static function printMediaLinks(Tree $tree, string $factrec, int $level): void
    {
        $nlevel = $level + 1;
        if (preg_match_all("/$level OBJE @(.*)@/", $factrec, $omatch, PREG_SET_ORDER) === 0) {
            return;
        }
        $objectNum = 0;
        while ($objectNum < count($omatch)) {
            $media_id = $omatch[$objectNum][1];
            $media    = Registry::mediaFactory()->make($media_id, $tree);
            if ($media) {
                if ($media->canShow()) {
                    echo '<div class="d-flex align-items-center"><div class="p-1">';
                    foreach ($media->mediaFiles() as $media_file) {
                        echo $media_file->displayImage(100, 100, 'contain', []);
                    }
                    echo '</div>';
                    echo '<div>';
                    echo '<a href="', e($media->url()), '">', $media->fullName(), '</a>';
                    // NOTE: echo the notes of the media
                    echo '<p>';
                    echo FunctionsPrint::printFactNotes($tree, $media->gedcom(), 1);
                    //-- print spouse name for marriage events
                    echo FunctionsPrint::printFactNotes($tree, $media->gedcom(), $nlevel);
                    echo self::printFactSources($tree, $media->gedcom(), $nlevel);
                    echo '</div>'; //close div "media-display-title"
                    echo '</div>'; //close div "media-display"
                }
            } elseif ($tree->getPreference('HIDE_GEDCOM_ERRORS') === '1') {
                echo '<p class="alert alert-danger">', $media_id, '</p>';
            }
            $objectNum++;
        }
    }

    /**
     * Print a row for the sources tab on the individual page.
     *
     * @param Fact $fact
     * @param int  $level
     *
     * @return void
     */
    public static function printMainSources(Fact $fact, int $level): void
    {
        $factrec = $fact->gedcom();
        $parent  = $fact->record();
        $tree    = $fact->record()->tree();

        $nlevel = $level + 1;
        if ($fact->isPendingAddition()) {
            $styleadd = 'wt-new';
            $can_edit = $level === 1 && $fact->canEdit();
        } elseif ($fact->isPendingDeletion()) {
            $styleadd = 'wt-old';
            $can_edit = false;
        } else {
            $styleadd = '';
            $can_edit = $level === 1 && $fact->canEdit();
        }

        // -- find source for each fact
        preg_match_all('/(?:^|\n)(' . $level . ' SOUR (.*)(?:\n[' . $nlevel . '-9] .*)*)/', $fact->gedcom(), $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $srec   = $match[1];
            $sid    = $match[2];
            $source = Registry::sourceFactory()->make(trim($sid, '@'), $tree);
            // Allow access to "1 SOUR @non_existent_source@", so it can be corrected/deleted
            if (!$source || $source->canShow()) {
                if ($level > 1) {
                    echo '<tr class="wt-level-two-source collapse">';
                } else {
                    echo '<tr>';
                }
                echo '<th class="';
                if ($level > 1) {
                    echo ' rela';
                }
                echo ' ', $styleadd, '">';
                $factlines = explode("\n", $factrec); // 1 BIRT Y\n2 SOUR ...
                $factwords = explode(' ', $factlines[0]); // 1 BIRT Y
                $factname  = $factwords[1]; // BIRT
                if ($factname === 'EVEN' || $factname === 'FACT') {
                    // Add ' EVEN' to provide sensible output for an event with an empty TYPE record
                    $ct = preg_match('/2 TYPE (.*)/', $factrec, $ematch);
                    if ($ct > 0) {
                        $factname = trim($ematch[1]);
                        echo $factname;
                    } else {
                        echo GedcomTag::getLabel($factname);
                    }
                } elseif ($can_edit) {
                    echo '<a href="' . e(route(EditFactPage::class, [
                            'xref'    => $parent->xref(),
                            'fact_id' => $fact->id(),
                            'tree'    => $tree->name(),
                        ])) . '" title="', I18N::translate('Edit'), '">';
                    echo GedcomTag::getLabel($factname), '</a>';
                    echo '<div class="editfacts nowrap">';
                    if (preg_match('/^@.+@$/', $sid)) {
                        // Inline sources can't be edited. Attempting to save one will convert it
                        // into a link, and delete it.
                        // e.g. "1 SOUR my source" becomes "1 SOUR @my source@" which does not exist.
                        echo view('edit/icon-fact-edit', ['fact' => $fact]);
                        echo view('edit/icon-fact-copy', ['fact' => $fact]);
                    }
                    echo view('edit/icon-fact-delete', ['fact' => $fact]);
                } else {
                    echo GedcomTag::getLabel($factname);
                }
                echo '</th>';
                echo '<td class="', $styleadd, '">';
                if ($source) {
                    echo '<a href="', e($source->url()), '">', $source->fullName(), '</a>';
                    // 2 RESN tags. Note, there can be more than one, such as "privacy" and "locked"
                    if (preg_match_all("/\n2 RESN (.+)/", $factrec, $rmatches)) {
                        foreach ($rmatches[1] as $rmatch) {
                            echo '<br><span class="label">', GedcomTag::getLabel('RESN'), ':</span> <span class="field">';
                            switch ($rmatch) {
                                case 'none':
                                    // Note: "2 RESN none" is not valid gedcom, and the GUI will not let you add it.
                                    // However, webtrees privacy rules will interpret it as "show an otherwise private fact to public".
                                    echo '<i class="icon-resn-none"></i> ', I18N::translate('Show to visitors');
                                    break;
                                case 'privacy':
                                    echo '<i class="icon-resn-privacy"></i> ', I18N::translate('Show to members');
                                    break;
                                case 'confidential':
                                    echo '<i class="icon-resn-confidential"></i> ', I18N::translate('Show to managers');
                                    break;
                                case 'locked':
                                    echo '<i class="icon-resn-locked"></i> ', I18N::translate('Only managers can edit');
                                    break;
                                default:
                                    echo $rmatch;
                                    break;
                            }
                            echo '</span>';
                        }
                    }
                    $cs = preg_match("/$nlevel EVEN (.*)/", $srec, $cmatch);
                    if ($cs > 0) {
                        echo '<br><span class="label">', GedcomTag::getLabel('EVEN'), ' </span><span class="field">', $cmatch[1], '</span>';
                        $cs = preg_match('/' . ($nlevel + 1) . ' ROLE (.*)/', $srec, $cmatch);
                        if ($cs > 0) {
                            echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;<span class="label">', GedcomTag::getLabel('ROLE'), ' </span><span class="field">', $cmatch[1], '</span>';
                        }
                    }
                    echo self::printSourceStructure($tree, self::getSourceStructure($srec));
                    echo '<div class="indent">';
                    self::printMediaLinks($tree, $srec, $nlevel);
                    if ($nlevel === 2) {
                        self::printMediaLinks($tree, $source->gedcom(), 1);
                    }
                    echo FunctionsPrint::printFactNotes($tree, $srec, $nlevel);
                    if ($nlevel === 2) {
                        echo FunctionsPrint::printFactNotes($tree, $source->gedcom(), 1);
                    }
                    echo '</div>';
                } else {
                    echo $sid;
                }
                echo '</td></tr>';
            }
        }
    }

    /**
     * Print SOUR structure
     *  This function prints the input array of SOUR sub-records built by the
     *  getSourceStructure() function.
     *
     * @param Tree                $tree
     * @param string[]|string[][] $textSOUR
     *
     * @return string
     */
    public static function printSourceStructure(Tree $tree, array $textSOUR): string
    {
        $html = '';

        if ($textSOUR['PAGE'] !== '') {
            $html .= Registry::elementFactory()->make('INDI:SOUR:PAGE')->labelValue($textSOUR['PAGE'], $tree);
        }

        if ($textSOUR['EVEN'] !== '') {
            $html .= Registry::elementFactory()->make('INDI:SOUR:EVEN')->labelValue($textSOUR['EVEN'], $tree);

            if ($textSOUR['ROLE']) {
                $html .= Registry::elementFactory()->make('INDI:SOUR:EVEN:ROLE')->labelValue($textSOUR['ROLE'], $tree);
            }
        }

        if ($textSOUR['DATE'] !== '') {
            $html .= Registry::elementFactory()->make('INDI:SOUR:DATA:DATE')->labelValue($textSOUR['DATE'], $tree);
        }

        foreach ($textSOUR['TEXT'] as $text) {
            $html .= Registry::elementFactory()->make('INDI:SOUR:DATA:TEXT')->labelValue($text, $tree);
        }

        if ($textSOUR['QUAY'] !== '') {
            $html .= Registry::elementFactory()->make('INDI:SOUR:QUAY')->labelValue($textSOUR['QUAY'], $tree);
        }

        return '<div class="indent">' . $html . '</div>';
    }

    /**
     * Extract SOUR structure from the incoming Source sub-record
     * The output array is defined as follows:
     *  $textSOUR['PAGE'] = Source citation
     *  $textSOUR['EVEN'] = Event type
     *  $textSOUR['ROLE'] = Role in event
     *  $textSOUR['DATA'] = place holder (no text in this sub-record)
     *  $textSOUR['DATE'] = Entry recording date
     *  $textSOUR['TEXT'] = (array) Text from source
     *  $textSOUR['QUAY'] = Certainty assessment
     *
     * @param string $srec
     *
     * @return array<array<string>>
     */
    public static function getSourceStructure(string $srec): array
    {
        // Set up the output array
        $textSOUR = [
            'PAGE' => '',
            'EVEN' => '',
            'ROLE' => '',
            'DATE' => '',
            'TEXT' => [],
            'QUAY' => '',
        ];

        preg_match_all('/^\d (PAGE|EVEN|ROLE|DATE|TEXT|QUAY) ?(.*(\n\d CONT.*)*)$/m', $srec, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $tag   = $match[1];
            $value = $match[2];
            $value = preg_replace('/\n\d CONT ?/', "\n", $value);

            if ($tag === 'TEXT') {
                $textSOUR[$tag][] = $value;
            } else {
                $textSOUR[$tag] = $value;
            }
        }

        return $textSOUR;
    }

    /**
     * Print a row for the notes tab on the individual page.
     *
     * @param Fact $fact
     * @param int  $level
     *
     * @return void
     */
    public static function printMainNotes(Fact $fact, int $level): void
    {
        $factrec = $fact->gedcom();
        $parent  = $fact->record();
        $tree    = $parent->tree();

        if ($fact->isPendingAddition()) {
            $styleadd = 'wt-new ';
            $can_edit = $level === 1 && $fact->canEdit();
        } elseif ($fact->isPendingDeletion()) {
            $styleadd = 'wt-old ';
            $can_edit = false;
        } else {
            $styleadd = '';
            $can_edit = $level === 1 && $fact->canEdit();
        }

        $ct = preg_match_all("/$level NOTE (.*)/", $factrec, $match, PREG_SET_ORDER);
        for ($j = 0; $j < $ct; $j++) {
            // Note object, or inline note?
            if (preg_match("/$level NOTE @(.*)@/", $match[$j][0], $nmatch)) {
                $note = Registry::noteFactory()->make($nmatch[1], $tree);
                if ($note && !$note->canShow()) {
                    continue;
                }
            } else {
                $note = null;
            }

            if ($level >= 2) {
                echo '<tr class="wt-level-two-note collapse"><th scope="row" class="rela ', $styleadd, '">';
            } else {
                echo '<tr><th scope="row" class="', $styleadd, '">';
            }
            if ($can_edit) {
                if ($level < 2) {
                    if ($note instanceof Note) {
                        echo '<a href="' . e($note->url()) . '">';
                        echo GedcomTag::getLabel('SHARED_NOTE');
                        echo view('icons/note');
                        echo '</a>';
                    } else {
                        echo GedcomTag::getLabel('NOTE');
                    }
                    echo '<div class="editfacts nowrap">';
                    echo view('edit/icon-fact-edit', ['fact' => $fact]);
                    echo view('edit/icon-fact-copy', ['fact' => $fact]);
                    echo view('edit/icon-fact-delete', ['fact' => $fact]);
                    echo '</div>';
                }
            } else {
                if ($level < 2) {
                    if ($note) {
                        echo GedcomTag::getLabel('SHARED_NOTE');
                    } else {
                        echo GedcomTag::getLabel('NOTE');
                    }
                }
                $factlines = explode("\n", $factrec); // 1 BIRT Y\n2 NOTE ...
                $factwords = explode(' ', $factlines[0]); // 1 BIRT Y
                $factname  = $factwords[1]; // BIRT
                if ($factname === 'EVEN' || $factname === 'FACT') {
                    // Add ' EVEN' to provide sensible output for an event with an empty TYPE record
                    $ct = preg_match('/2 TYPE (.*)/', $factrec, $ematch);
                    if ($ct > 0) {
                        $factname = trim($ematch[1]);
                        echo $factname;
                    } else {
                        echo GedcomTag::getLabel($factname);
                    }
                } elseif ($factname !== 'NOTE') {
                    // Note is already printed
                    echo GedcomTag::getLabel($factname);
                    if ($note) {
                        echo '<a class="btn btn-link" href="' . e($note->url()) . '" title="' . I18N::translate('View') . '"><span class="sr-only">' . I18N::translate('View') . '</span></a>';
                    }
                }
            }
            echo '</th>';
            if ($note) {
                // Note objects
                $text = Filter::formatText($note->getNote(), $tree);
            } else {
                // Inline notes
                $nrec = Functions::getSubRecord($level, "$level NOTE", $factrec, $j + 1);
                $text = $match[$j][1] . Functions::getCont($level + 1, $nrec);
                $text = Filter::formatText($text, $tree);
            }

            echo '<td class="', $styleadd, ' wrap">';
            echo $text;

            // 2 RESN tags. Note, there can be more than one, such as "privacy" and "locked"
            if (preg_match_all("/\n2 RESN (.+)/", $factrec, $rmatches)) {
                foreach ($rmatches[1] as $rmatch) {
                    echo '<br><span class="label">', GedcomTag::getLabel('RESN'), ':</span> <span class="field">';
                    switch ($rmatch) {
                        case 'none':
                            // Note: "2 RESN none" is not valid gedcom, and the GUI will not let you add it.
                            // However, webtrees privacy rules will interpret it as "show an otherwise private fact to public".
                            echo '<i class="icon-resn-none"></i> ', I18N::translate('Show to visitors');
                            break;
                        case 'privacy':
                            echo '<i class="icon-resn-privacy"></i> ', I18N::translate('Show to members');
                            break;
                        case 'confidential':
                            echo '<i class="icon-resn-confidential"></i> ', I18N::translate('Show to managers');
                            break;
                        case 'locked':
                            echo '<i class="icon-resn-locked"></i> ', I18N::translate('Only managers can edit');
                            break;
                        default:
                            echo $rmatch;
                            break;
                    }
                    echo '</span>';
                }
            }
            echo '</td></tr>';
        }
    }

    /**
     * Print a row for the media tab on the individual page.
     *
     * @param Fact $fact
     * @param int  $level
     *
     * @return void
     */
    public static function printMainMedia(Fact $fact, int $level): void
    {
        $tree = $fact->record()->tree();

        if ($fact->isPendingAddition()) {
            $styleadd = 'wt-new';
        } elseif ($fact->isPendingDeletion()) {
            $styleadd = 'wt-old';
        } else {
            $styleadd = '';
        }

        // -- find source for each fact
        preg_match_all('/(?:^|\n)' . $level . ' OBJE @(.*)@/', $fact->gedcom(), $matches);
        foreach ($matches[1] as $xref) {
            $media = Registry::mediaFactory()->make($xref, $tree);
            // Allow access to "1 OBJE @non_existent_source@", so it can be corrected/deleted
            if (!$media instanceof Media || $media->canShow()) {
                echo '<tr class="', $styleadd, '">';
                echo '<th scope="row">';
                echo $fact->label();

                if ($level === 1 && $fact->canEdit()) {
                    echo '<div class="editfacts nowrap">';
                    echo view('edit/icon-fact-copy', ['fact' => $fact]);
                    echo view('edit/icon-fact-delete', ['fact' => $fact]);
                    echo '</div>';
                }

                echo '</th>';
                echo '<td>';
                if ($media instanceof Media) {
                    foreach ($media->mediaFiles() as $media_file) {
                        echo '<div>';
                        echo $media_file->displayImage(100, 100, 'contain', []);
                        echo '<br>';
                        echo '<a href="' . e($media->url()) . '"> ';
                        echo '<em>';
                        echo e($media_file->title() ?: $media_file->filename());
                        echo '</em>';
                        echo '</a>';
                        echo '</div>';
                    }

                    echo FunctionsPrint::printFactNotes($tree, $media->gedcom(), 1);
                    echo self::printFactSources($tree, $media->gedcom(), 1);
                } else {
                    echo $xref;
                }
                echo '</td></tr>';
            }
        }
    }
}
