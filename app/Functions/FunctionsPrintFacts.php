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

namespace Fisharebest\Webtrees\Functions;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomCode\GedcomCodeAdop;
use Fisharebest\Webtrees\GedcomCode\GedcomCodeLang;
use Fisharebest\Webtrees\GedcomCode\GedcomCodeQuay;
use Fisharebest\Webtrees\GedcomCode\GedcomCodeRela;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\Http\RequestHandlers\EditFact;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Module\ModuleChartInterface;
use Fisharebest\Webtrees\Module\ModuleInterface;
use Fisharebest\Webtrees\Module\RelationshipsChartModule;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Submitter;
use Fisharebest\Webtrees\Tree;
use Ramsey\Uuid\Uuid;

use function e;
use function view;

use const PREG_SET_ORDER;

/**
 * Class FunctionsPrintFacts - common functions
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
        // Keep a track of children and grandchildren, so we can display their birth order "#1", "#2", etc.
        static $children = [], $grandchildren = [];

        $parent = $fact->record();
        $tree   = $parent->tree();

        // Some facts don't get printed here ...
        switch ($fact->getTag()) {
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
                // These links are used internally to record the sort order.
                return;
            default:
                // Hide unrecognized/custom tags?
                if ($tree->getPreference('HIDE_GEDCOM_ERRORS') === '0' && !GedcomTag::isTag($fact->getTag())) {
                    return;
                }
                break;
        }

        // Who is this fact about? Need it to translate fact label correctly
        if ($parent instanceof Family && $record instanceof Individual) {
            // Family event
            $label_person = $parent->spouse($record);
        } else {
            // Individual event
            $label_person = $parent;
        }

        // New or deleted facts need different styling
        $styleadd = '';
        if ($fact->isPendingAddition()) {
            $styleadd = 'wt-new';
        }
        if ($fact->isPendingDeletion()) {
            $styleadd = 'wt-old';
        }

        // Event of close relative
        if (preg_match('/^_[A-Z_]{3,5}_[A-Z0-9]{4}$/', $fact->getTag())) {
            $styleadd = trim($styleadd . ' wt-relation-fact collapse');
        }

        // Event of close associates
        if ($fact->id() === 'asso') {
            $styleadd = trim($styleadd . ' wt-relation-fact collapse');
        }

        // historical facts
        if ($fact->id() === 'histo') {
            $styleadd = trim($styleadd . ' wt-historic-fact collapse');
        }

        // Does this fact have a type?
        if (preg_match('/\n2 TYPE (.+)/', $fact->gedcom(), $match)) {
            $type = $match[1];
        } else {
            $type = '';
        }

        switch ($fact->getTag()) {
            case 'EVEN':
            case 'FACT':
                if (GedcomTag::isTag($type)) {
                    // Some users (just Meliza?) use "1 EVEN/2 TYPE BIRT". Translate the TYPE.
                    $label = GedcomTag::getLabel($type, $label_person);
                    $type  = ''; // Do not print this again
                } elseif ($type) {
                    // We don't have a translation for $type - but a custom translation might exist.
                    $label = I18N::translate(e($type));
                    $type  = ''; // Do not print this again
                } else {
                    // An unspecified fact/event
                    $label = $fact->label();
                }
                break;
            case 'MARR':
                // This is a hack for a proprietory extension. Is it still used/needed?
                $utype = strtoupper($type);
                if ($utype === 'CIVIL' || $utype === 'PARTNERS' || $utype === 'RELIGIOUS') {
                    $label = GedcomTag::getLabel('MARR_' . $utype, $label_person);
                    $type  = ''; // Do not print this again
                } else {
                    $label = $fact->label();
                }
                break;
            default:
                // Normal fact/event
                $label = $fact->label();
                break;
        }

        echo '<tr class="', $styleadd, '">';
        echo '<th scope="row">';

        switch ($fact->getTag()) {
            case '_BIRT_CHIL':
                $children[$fact->record()->xref()] = true;
                /* I18N: Abbreviation for "number %s" */
                $label .= '<br>' . I18N::translate('#%s', I18N::number(count($children)));
                break;
            case '_BIRT_GCHI':
            case '_BIRT_GCH1':
            case '_BIRT_GCH2':
                $grandchildren[$fact->record()->xref()] = true;
                /* I18N: Abbreviation for "number %s" */
                $label .= '<br>' . I18N::translate('#%s', I18N::number(count($grandchildren)));
                break;
        }

        echo $label;

        if ($fact->id() !== 'histo' && $fact->id() !== 'asso' && $fact->canEdit()) {
            echo '<div class="editfacts nowrap">';
            echo view('edit/icon-fact-edit', ['fact' => $fact]);
            echo view('edit/icon-fact-copy', ['fact' => $fact]);
            echo view('edit/icon-fact-delete', ['fact' => $fact]);
            echo '</div>';
        }

        if ($tree->getPreference('SHOW_FACT_ICONS')) {
            echo '<span class="wt-fact-icon wt-fact-icon-' . $fact->getTag() . '" title="' . strip_tags(GedcomTag::getLabel($fact->getTag())) . '"></span>';
        }

        echo '</th>';
        echo '<td class="', $styleadd, '">';

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
        switch ($fact->getTag()) {
            case 'ADDR':
                echo $fact->value();
                break;
            case 'AFN':
                echo '<div class="field"><a href="https://familysearch.org/search/tree/results#count=20&query=afn:', rawurlencode($fact->value()), '">', e($fact->value()), '</a></div>';
                break;
            case 'ASSO':
                // we handle this later, in format_asso_rela_record()
                break;
            case 'EMAIL':
            case 'EMAI':
            case '_EMAIL':
                echo '<div class="field"><a href="mailto:', e($fact->value()), '">', e($fact->value()), '</a></div>';
                break;
            case 'LANG':
                echo GedcomCodeLang::getValue($fact->value());
                break;
            case 'RESN':
                echo '<div class="field">';
                switch ($fact->value()) {
                    case 'none':
                        // Note: "1 RESN none" is not valid gedcom.
                        // However, webtrees privacy rules will interpret it as "show an otherwise private record to public".
                        echo '<i class="icon-resn-none"></i> ', I18N::translate('Show to visitors');
                        break;
                    case 'privacy':
                        echo '<i class="icon-class-none"></i> ', I18N::translate('Show to members');
                        break;
                    case 'confidential':
                        echo '<i class="icon-confidential-none"></i> ', I18N::translate('Show to managers');
                        break;
                    case 'locked':
                        echo '<i class="icon-locked-none"></i> ', I18N::translate('Only managers can edit');
                        break;
                    default:
                        echo e($fact->value());
                        break;
                }
                echo '</div>';
                break;
            case 'PUBL': // Publication details might contain URLs.
                echo '<div class="field">', Filter::expandUrls($fact->value(), $tree), '</div>';
                break;
            case 'REPO':
                $repository = $fact->target();
                if ($repository instanceof Repository) {
                    echo '<div><a class="field" href="', e($repository->url()), '">', $repository->fullName(), '</a></div>';
                } else {
                    echo '<div class="error">', e($fact->value()), '</div>';
                }
                break;
            case 'SUBM':
                $submitter = $fact->target();
                if ($submitter instanceof Submitter) {
                    echo '<div><a class="field" href="', e($submitter->url()), '">', $submitter->fullName(), '</a></div>';
                } else {
                    echo '<div class="error">', e($fact->value()), '</div>';
                }
                break;
            case 'URL':
            case '_URL':
            case 'WWW':
                echo '<div class="field"><a href="', e($fact->value()), '">', e($fact->value()), '</a></div>';
                break;
            case 'TEXT': // 0 SOUR / 1 TEXT
                echo '<div class="field">', nl2br(e($fact->value()), false), '</div>';
                break;
            default:
                // Display the value for all other facts/events
                switch ($fact->value()) {
                    case '':
                        // Nothing to display
                        break;
                    case 'N':
                        // Not valid GEDCOM
                        echo '<div class="field">', I18N::translate('No'), '</div>';
                        break;
                    case 'Y':
                        // Do not display "Yes".
                        break;
                    default:
                        if (preg_match('/^@(' . Gedcom::REGEX_XREF . ')@$/', $fact->value(), $match)) {
                            $target = GedcomRecord::getInstance($match[1], $tree);
                            if ($target) {
                                echo '<div><a href="', e($target->url()), '">', $target->fullName(), '</a></div>';
                            } else {
                                echo '<div class="error">', e($fact->value()), '</div>';
                            }
                        } else {
                            echo '<div class="field"><span dir="auto">', e($fact->value()), '</span></div>';
                        }
                        break;
                }
                break;
        }

        // Print the type of this fact/event
        if ($type) {
            $utype = strtoupper($type);
            // Events of close relatives, e.g. _MARR_CHIL
            if (substr($fact->getTag(), 0, 6) === '_MARR_' && ($utype === 'CIVIL' || $utype === 'PARTNERS' || $utype === 'RELIGIOUS')) {
                // Translate MARR/TYPE using the code that supports MARR_CIVIL, etc. tags
                $type = GedcomTag::getLabel('MARR_' . $utype);
            } else {
                // Allow (custom) translations for other types
                $type = I18N::translate($type);
            }
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
            echo GedcomTag::getLabelValue('ADDR', $addr);
        }

        // Print the associates of this fact/event
        if ($fact->id() !== 'asso') {
            echo self::formatAssociateRelationship($fact);
        }

        // Print any other "2 XXXX" attributes, in the order in which they appear.
        preg_match_all('/\n2 (' . Gedcom::REGEX_TAG . ') (.+)/', $fact->gedcom(), $matches, PREG_SET_ORDER);
        
        //0 SOUR / 1 DATA / 2 EVEN / 3 DATE and 3 PLAC must be collected separately
        preg_match_all('/\n2 EVEN .*((\n[3].*)*)/', $fact->gedcom(), $evenMatches, PREG_SET_ORDER);
        $currentEvenMatch = 0;
        
        foreach ($matches as $match) {
            switch ($match[1]) {
                case 'DATE':
                case 'TIME':
                case 'AGE':
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
                case '_UID':
                case 'RIN':
                    // These don't belong at level 2, so do not display them.
                    // They are only shown when editing.
                    break;
                case 'EVEN': // 0 SOUR / 1 DATA / 2 EVEN / 3 DATE / 3 PLAC
                    $events = [];
                    foreach (preg_split('/ *, */', $match[2]) as $event) {
                        $events[] = GedcomTag::getLabel($event);
                    }
                    echo GedcomTag::getLabelValue('EVEN', implode(I18N::$list_separator, $events));
                    
                    if (preg_match('/\n3 DATE (.+)/', $evenMatches[$currentEvenMatch][0], $date_match)) {
                        $date = new Date($date_match[1]);
                        echo GedcomTag::getLabelValue('DATE', $date->display());
                    }
                    if (preg_match('/\n3 PLAC (.+)/', $evenMatches[$currentEvenMatch][0], $plac_match)) {
                        echo GedcomTag::getLabelValue('PLAC', $plac_match[1]);
                    }
                    $currentEvenMatch++;
                    
                    break;
                case 'FAMC': // 0 INDI / 1 ADOP / 2 FAMC / 3 ADOP
                    $family = Family::getInstance(str_replace('@', '', $match[2]), $tree);
                    if ($family) {
                        echo GedcomTag::getLabelValue('FAM', '<a href="' . e($family->url()) . '">' . $family->fullName() . '</a>');
                        if (preg_match('/\n3 ADOP (HUSB|WIFE|BOTH)/', $fact->gedcom(), $adop_match)) {
                            echo GedcomTag::getLabelValue('ADOP', GedcomCodeAdop::getValue($adop_match[1], $label_person));
                        }
                    } else {
                        echo GedcomTag::getLabelValue('FAM', '<span class="error">' . $match[2] . '</span>');
                    }
                    break;
                case '_WT_USER':
                    if (Auth::check()) {
                        $user = (new UserService())->findByIdentifier($match[2]); // may not exist
                        if ($user) {
                            echo GedcomTag::getLabelValue('_WT_USER', '<span dir="auto">' . e($user->realName()) . '</span>');
                        } else {
                            echo GedcomTag::getLabelValue('_WT_USER', e($match[2]));
                        }
                    }
                    break;
                case 'RESN':
                    switch ($match[2]) {
                        case 'none':
                            // Note: "2 RESN none" is not valid gedcom.
                            // However, webtrees privacy rules will interpret it as "show an otherwise private fact to public".
                            echo GedcomTag::getLabelValue('RESN', '<i class="icon-resn-none"></i> ' . I18N::translate('Show to visitors'));
                            break;
                        case 'privacy':
                            echo GedcomTag::getLabelValue('RESN', '<i class="icon-resn-privacy"></i> ' . I18N::translate('Show to members'));
                            break;
                        case 'confidential':
                            echo GedcomTag::getLabelValue('RESN', '<i class="icon-resn-confidential"></i> ' . I18N::translate('Show to managers'));
                            break;
                        case 'locked':
                            echo GedcomTag::getLabelValue('RESN', '<i class="icon-resn-locked"></i> ' . I18N::translate('Only managers can edit'));
                            break;
                        default:
                            echo GedcomTag::getLabelValue('RESN', e($match[2]));
                            break;
                    }
                    break;
                case 'CALN':
                    echo GedcomTag::getLabelValue('CALN', Filter::expandUrls($match[2], $tree));
                    break;
                case 'FORM': // 0 OBJE / 1 FILE / 2 FORM / 3 TYPE
                    echo GedcomTag::getLabelValue('FORM', $match[2]);
                    if (preg_match('/\n3 TYPE (.+)/', $fact->gedcom(), $type_match)) {
                        echo GedcomTag::getLabelValue('TYPE', GedcomTag::getFileFormTypeValue($type_match[1]));
                    }
                    break;
                case 'URL':
                case '_URL':
                case 'WWW':
                    $link = '<a href="' . e($match[2]) . '">' . e($match[2]) . '</a>';
                    echo GedcomTag::getLabelValue($fact->getTag() . ':' . $match[1], $link);
                    break;
                default:
                    if ($tree->getPreference('HIDE_GEDCOM_ERRORS') === '1' || GedcomTag::isTag($match[1])) {
                        if (preg_match('/^@(' . Gedcom::REGEX_XREF . ')@$/', $match[2], $xmatch)) {
                            // Links
                            $linked_record = GedcomRecord::getInstance($xmatch[1], $tree);
                            if ($linked_record) {
                                $link = '<a href="' . e($linked_record->url()) . '">' . $linked_record->fullName() . '</a>';
                                echo GedcomTag::getLabelValue($fact->getTag() . ':' . $match[1], $link);
                            } else {
                                echo GedcomTag::getLabelValue($fact->getTag() . ':' . $match[1], e($match[2]));
                            }
                        } else {
                            // Non links
                            echo GedcomTag::getLabelValue($fact->getTag() . ':' . $match[1], e($match[2]));
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
            $person = Individual::getInstance($amatch[1], $event->record()->tree());
            if ($person && $person->canShowName()) {
                // Is there a "RELA" tag
                if (preg_match('/\n[23] RELA (.+)/', $amatch[2], $rmatch)) {
                    // Use the supplied relationship as a label
                    $label = GedcomCodeRela::getValue($rmatch[1], $person);
                } else {
                    // Use a default label
                    $label = GedcomTag::getLabel('ASSO', $person);
                }

                $values = ['<a href="' . e($person->url()) . '">' . $person->fullName() . '</a>'];

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
    public static function printFactSources(Tree $tree, $factrec, $level): string
    {
        $data   = '';
        $nlevel = $level + 1;

        // Systems not using source records
        // The old style is not supported when entering or editing sources, but may be found in imported trees.
        // Also, the old style sources allow histo.* files to use tree independent source citations, which
        // will display nicely when markdown is used.
        $ct = preg_match_all('/' . $level . ' SOUR (.*)((?:\n\d CONT.*)*)/', $factrec, $match, PREG_SET_ORDER);
        for ($j = 0; $j < $ct; $j++) {
            if (strpos($match[$j][1], '@') === false) {
                $source = e($match[$j][1] . preg_replace('/\n\d CONT ?/', "\n", $match[$j][2]));
                $data   .= '<div class="fact_SOUR"><span class="label">' . I18N::translate('Source') . ':</span> <span class="field" dir="auto">' . Filter::formatText($source, $tree) . '</span></div>';
            }
        }
        // Find source for each fact
        $ct    = preg_match_all("/$level SOUR @(.*)@/", $factrec, $match, PREG_SET_ORDER);
        $spos2 = 0;
        for ($j = 0; $j < $ct; $j++) {
            $sid    = $match[$j][1];
            $source = Source::getInstance($sid, $tree);
            if ($source) {
                if ($source->canShow()) {
                    $spos1 = strpos($factrec, "$level SOUR @" . $sid . '@', $spos2);
                    $spos2 = strpos($factrec, "\n$level", $spos1);
                    if (!$spos2) {
                        $spos2 = strlen($factrec);
                    }
                    $srec      = substr($factrec, $spos1, $spos2 - $spos1);
                    $lt        = preg_match_all("/$nlevel \w+/", $srec, $matches);
                    $data      .= '<div class="fact_SOUR">';
                    $elementID = Uuid::uuid4()->toString();
                    if ($tree->getPreference('EXPAND_SOURCES')) {
                        $plusminus = 'icon-minus';
                    } else {
                        $plusminus = 'icon-plus';
                    }
                    if ($lt > 0) {
                        $data .= '<a href="#" onclick="return expand_layer(\'' . $elementID . '\');"><i id="' . $elementID . '_img" class="' . $plusminus . '"></i></a> ';
                    }
                    $data .= GedcomTag::getLabelValue('SOUR', '<a href="' . e($source->url()) . '">' . $source->fullName() . '</a>', null, 'span');
                    $data .= '</div>';

                    $data .= "<div id=\"$elementID\"";
                    if ($tree->getPreference('EXPAND_SOURCES')) {
                        $data .= ' style="display:block"';
                    }
                    $data .= ' class="source_citations">';
                    // PUBL
                    $publ = $source->facts(['PUBL'])->first();
                    if ($publ instanceof Fact) {
                        $data .= GedcomTag::getLabelValue('PUBL', $publ->value());
                    }
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
    public static function printMediaLinks(Tree $tree, $factrec, $level): void
    {
        $nlevel = $level + 1;
        if (preg_match_all("/$level OBJE @(.*)@/", $factrec, $omatch, PREG_SET_ORDER) === 0) {
            return;
        }
        $objectNum = 0;
        while ($objectNum < count($omatch)) {
            $media_id = $omatch[$objectNum][1];
            $media    = Media::getInstance($media_id, $tree);
            if ($media) {
                if ($media->canShow()) {
                    if ($objectNum > 0) {
                        echo '<br class="media-separator" style="clear:both;">';
                    }
                    echo '<div class="media-display"><div class="media-display-image">';
                    foreach ($media->mediaFiles() as $media_file) {
                        echo $media_file->displayImage(100, 100, 'contain', []);
                    }
                    echo '</div>';
                    echo '<div class="media-display-title">';
                    echo '<a href="', e($media->url()), '">', $media->fullName(), '</a>';
                    // NOTE: echo the notes of the media
                    echo '<p>';
                    echo FunctionsPrint::printFactNotes($tree, $media->gedcom(), 1);
                    $ttype = preg_match('/' . ($nlevel + 1) . ' TYPE (.*)/', $media->gedcom(), $match);
                    if ($ttype > 0) {
                        $mediaType = GedcomTag::getFileFormTypeValue($match[1]);
                        echo '<span class="label">', I18N::translate('Type'), ': </span> <span class="field">', $mediaType, '</span>';
                    }
                    //-- print spouse name for marriage events
                    $ct = preg_match('/WT_SPOUSE: (.*)/', $factrec, $match);
                    if ($ct > 0) {
                        $spouse = Individual::getInstance($match[1], $tree);
                        if ($spouse) {
                            echo '<a href="', e($spouse->url()), '">';
                            echo $spouse->fullName();
                            echo '</a>';
                        }
                        $ct = preg_match('/WT_FAMILY_ID: (.*)/', $factrec, $match);
                        if ($ct > 0) {
                            $famid  = trim($match[1]);
                            $family = Family::getInstance($famid, $tree);
                            if ($family) {
                                if ($spouse) {
                                    echo ' - ';
                                }
                                echo '<a href="', e($family->url()), '">', I18N::translate('View this family'), '</a>';
                            }
                        }
                    }
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
        $ct    = preg_match_all("/($level SOUR (.+))/", $factrec, $match, PREG_SET_ORDER);
        $spos2 = 0;
        for ($j = 0; $j < $ct; $j++) {
            $sid   = trim($match[$j][2], '@');
            $spos1 = strpos($factrec, $match[$j][1], $spos2);
            $spos2 = strpos($factrec, "\n$level", $spos1);
            if (!$spos2) {
                $spos2 = strlen($factrec);
            }
            $srec   = substr($factrec, $spos1, $spos2 - $spos1);
            $source = Source::getInstance($sid, $tree);
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
                        echo GedcomTag::getLabel($factname, $parent);
                    }
                } elseif ($can_edit) {
                    echo '<a href="' . e(route(EditFact::class, [
                            'xref'    => $parent->xref(),
                            'fact_id' => $fact->id(),
                            'tree'    => $tree->name(),
                        ])) . '" title="', I18N::translate('Edit'), '">';
                    echo GedcomTag::getLabel($factname, $parent), '</a>';
                    echo '<div class="editfacts nowrap">';
                    if (preg_match('/^@.+@$/', $match[$j][2])) {
                        // Inline sources can't be edited. Attempting to save one will convert it
                        // into a link, and delete it.
                        // e.g. "1 SOUR my source" becomes "1 SOUR @my source@" which does not exist.
                        echo view('edit/icon-fact-edit', ['fact' => $fact]);
                        echo view('edit/icon-fact-copy', ['fact' => $fact]);
                    }
                    echo view('edit/icon-fact-delete', ['fact' => $fact]);
                } else {
                    echo GedcomTag::getLabel($factname, $parent);
                }
                echo '</th>';
                echo '<td class="', $styleadd, '">';
                if ($source) {
                    echo '<a href="', e($source->url()), '">', $source->fullName(), '</a>';
                    // PUBL
                    $publ = $source->facts(['PUBL'])->first();
                    if ($publ instanceof Fact) {
                        echo GedcomTag::getLabelValue('PUBL', $publ->value());
                    }
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
            $html .= GedcomTag::getLabelValue('PAGE', Filter::expandUrls($textSOUR['PAGE'], $tree));
        }

        if ($textSOUR['EVEN'] !== '') {
            $html .= GedcomTag::getLabelValue('EVEN', e($textSOUR['EVEN']));
            if ($textSOUR['ROLE']) {
                $html .= GedcomTag::getLabelValue('ROLE', e($textSOUR['ROLE']));
            }
        }

        if ($textSOUR['DATE'] !== '') {
            $date = new Date($textSOUR['DATE']);
            $html .= GedcomTag::getLabelValue('DATA:DATE', $date->display());
        }

        foreach ($textSOUR['TEXT'] as $text) {
            $html .= GedcomTag::getLabelValue('TEXT', Filter::formatText($text, $tree));
        }

        if ($textSOUR['QUAY'] !== '') {
            $html .= GedcomTag::getLabelValue('QUAY', GedcomCodeQuay::getValue($textSOUR['QUAY']));
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
     * @return string[]|string[][]
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
            $styleadd = ' new';
            $can_edit = $level === 1 && $fact->canEdit();
        } elseif ($fact->isPendingDeletion()) {
            $styleadd = ' old';
            $can_edit = false;
        } else {
            $styleadd = '';
            $can_edit = $level === 1 && $fact->canEdit();
        }

        $ct = preg_match_all("/$level NOTE (.*)/", $factrec, $match, PREG_SET_ORDER);
        for ($j = 0; $j < $ct; $j++) {
            // Note object, or inline note?
            if (preg_match("/$level NOTE @(.*)@/", $match[$j][0], $nmatch)) {
                $note = Note::getInstance($nmatch[1], $tree);
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
                        echo GedcomTag::getLabel($factname, $parent);
                    }
                } elseif ($factname !== 'NOTE') {
                    // Note is already printed
                    echo GedcomTag::getLabel($factname, $parent);
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

            echo '<td class="optionbox', $styleadd, ' wrap">';
            echo $text;

            // 2 RESN tags. Note, there can be more than one, such as "privacy" and "locked"
            if (preg_match_all("/\n2 RESN (.+)/", $factrec, $matches)) {
                foreach ($matches[1] as $match) {
                    echo '<br><span class="label">', GedcomTag::getLabel('RESN'), ':</span> <span class="field">';
                    switch ($match) {
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
                            echo $match;
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
    public static function printMainMedia(Fact $fact, $level): void
    {
        $factrec = $fact->gedcom();
        $parent  = $fact->record();
        $tree    = $parent->tree();

        if ($fact->isPendingAddition()) {
            $styleadd = 'wt-new';
            $can_edit = $level == 1 && $fact->canEdit();
        } elseif ($fact->isPendingDeletion()) {
            $styleadd = 'wt-old';
            $can_edit = false;
        } else {
            $styleadd = '';
            $can_edit = $level == 1 && $fact->canEdit();
        }

        // -- find source for each fact
        preg_match_all('/(?:^|\n)' . $level . ' OBJE @(.*)@/', $factrec, $matches);
        foreach ($matches[1] as $xref) {
            $media = Media::getInstance($xref, $tree);
            // Allow access to "1 OBJE @non_existent_source@", so it can be corrected/deleted
            if (!$media || $media->canShow()) {
                echo '<tr>';
                echo '<th scope="row" class="';
                if ($level > 1) {
                    echo 'rela ';
                }
                echo $styleadd, '">';
                preg_match("/^\d (\w*)/", $factrec, $factname);
                $factlines = explode("\n", $factrec); // 1 BIRT Y\n2 SOUR ...
                $factwords = explode(' ', $factlines[0]); // 1 BIRT Y
                $factname  = $factwords[1]; // BIRT
                if ($factname === 'EVEN' || $factname === 'FACT') {
                    // Add ' EVEN' to provide sensible output for an event with an empty TYPE record
                    $ct = preg_match('/2 TYPE (.*)/', $factrec, $ematch);
                    if ($ct > 0) {
                        $factname = $ematch[1];
                        echo $factname;
                    } else {
                        echo GedcomTag::getLabel($factname, $parent);
                    }
                } elseif ($can_edit) {
                    echo GedcomTag::getLabel($factname, $parent);
                    echo '<div class="editfacts nowrap">';
                    echo view('edit/icon-fact-copy', ['fact' => $fact]);
                    echo view('edit/icon-fact-delete', ['fact' => $fact]);
                    echo '</div>';
                } else {
                    echo GedcomTag::getLabel($factname, $parent);
                }
                echo '</th>';
                echo '<td class="', $styleadd, '">';
                if ($media) {
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
