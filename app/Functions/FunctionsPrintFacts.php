<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
use Fisharebest\Webtrees\FontAwesome;
use Fisharebest\Webtrees\GedcomCode\GedcomCodeAdop;
use Fisharebest\Webtrees\GedcomCode\GedcomCodeQuay;
use Fisharebest\Webtrees\GedcomCode\GedcomCodeRela;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Ramsey\Uuid\Uuid;

/**
 * Class FunctionsPrintFacts - common functions
 */
class FunctionsPrintFacts
{
    /**
     * Print a fact record, for the individual/family/source/repository/etc. pages.
     *
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
    public static function printFact(Fact $fact, GedcomRecord $record)
    {
        // Keep a track of children and grandchildren, so we can display their birth order "#1", "#2", etc.
        static $children = [], $grandchildren = [];

        $parent = $fact->getParent();
        $tree   = $parent->getTree();

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
            $label_person = $fact->getParent()->getSpouse($record);
        } else {
            // Individual event
            $label_person = $parent;
        }

        // New or deleted facts need different styling
        $styleadd = '';
        if ($fact->isPendingAddition()) {
            $styleadd = 'new';
        }
        if ($fact->isPendingDeletion()) {
            $styleadd = 'old';
        }

        // Event of close relative
        if (preg_match('/^_[A-Z_]{3,5}_[A-Z0-9]{4}$/', $fact->getTag())) {
            $styleadd = trim($styleadd . ' wt-relation-fact collapse');
        }

        // Event of close associates
        if ($fact->getFactId() == 'asso') {
            $styleadd = trim($styleadd . ' wt-relation-fact collapse');
        }

        // historical facts
        if ($fact->getFactId() == 'histo') {
            $styleadd = trim($styleadd . ' wt-historic-fact collapse');
        }

        // Does this fact have a type?
        if (preg_match('/\n2 TYPE (.+)/', $fact->getGedcom(), $match)) {
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
                    $label = $fact->getLabel();
                }
                break;
            case 'MARR':
                // This is a hack for a proprietory extension. Is it still used/needed?
                $utype = strtoupper($type);
                if ($utype == 'CIVIL' || $utype == 'PARTNERS' || $utype == 'RELIGIOUS') {
                    $label = GedcomTag::getLabel('MARR_' . $utype, $label_person);
                    $type  = ''; // Do not print this again
                } else {
                    $label = $fact->getLabel();
                }
                break;
            default:
                // Normal fact/event
                $label = $fact->getLabel();
                break;
        }

        echo '<tr class="', $styleadd, '">';
        echo '<th scope="row">';

        if ($tree->getPreference('SHOW_FACT_ICONS')) {
            echo Theme::theme()->icon($fact), ' ';
        }

        switch ($fact->getTag()) {
            case '_BIRT_CHIL':
                $children[$fact->getParent()->getXref()] = true;
                /* I18N: Abbreviation for "number %s" */
                $label .= '<br>' . I18N::translate('#%s', count($children));
                break;
            case '_BIRT_GCHI':
            case '_BIRT_GCH1':
            case '_BIRT_GCH2':
                $grandchildren[$fact->getParent()->getXref()] = true;
                /* I18N: Abbreviation for "number %s" */
                $label .= '<br>' . I18N::translate('#%s', count($grandchildren));
                break;
        }

        if ($fact->getFactId() != 'histo' && $fact->canEdit()) {
            ?>
            <?= $label ?>
            <div class="editfacts">
                <?= FontAwesome::linkIcon('edit', I18N::translate('Edit'), [
                    'class' => 'btn btn-link',
                    'href'  => route('edit-fact', [
                        'xref'    => $parent->getXref(),
                        'fact_id' => $fact->getFactId(),
                        'ged'     => $tree->getName(),
                    ]),
                ]) ?>
                <?= FontAwesome::linkIcon('copy', I18N::translate('Copy'), [
                    'class'   => 'btn btn-link',
                    'href'    => '#',
                    'onclick' => 'return copy_fact("' . e($tree->getName()) . '", "' . e($parent->getXref()) . '", "' . $fact->getFactId() . '");',
                ]) ?>
                <?= FontAwesome::linkIcon('delete', I18N::translate('Delete'), [
                    'class'   => 'btn btn-link',
                    'href'    => '#',
                    'onclick' => 'return delete_fact("' . I18N::translate('Are you sure you want to delete this fact?') . '", "' . e($tree->getName()) . '", "' . e($parent->getXref()) . '", "' . $fact->getFactId() . '");',
                ]) ?>
            </div>
            <?php
        } else {
            echo $label;
        }

        echo '</th>';
        echo '<td class="', $styleadd, '">';

        // Event from another record?
        if ($parent !== $record) {
            if ($parent instanceof Family) {
                foreach ($parent->getSpouses() as $spouse) {
                    if ($record !== $spouse) {
                        echo '<a href="', e($spouse->url()), '">', $spouse->getFullName(), '</a> — ';
                    }
                }
                echo '<a href="', e($parent->url()), '">', I18N::translate('View this family'), '</a><br>';
            } elseif ($parent instanceof Individual) {
                echo '<a href="', e($parent->url()), '">', $parent->getFullName(), '</a><br>';
            }
        }

        // Print the value of this fact/event
        switch ($fact->getTag()) {
            case 'ADDR':
                echo $fact->getValue();
                break;
            case 'AFN':
                echo '<div class="field"><a href="https://familysearch.org/search/tree/results#count=20&query=afn:', rawurlencode($fact->getValue()), '">', e($fact->getValue()), '</a></div>';
                break;
            case 'ASSO':
                // we handle this later, in format_asso_rela_record()
                break;
            case 'EMAIL':
            case 'EMAI':
            case '_EMAIL':
                echo '<div class="field"><a href="mailto:', e($fact->getValue()), '">', e($fact->getValue()), '</a></div>';
                break;
            case 'RESN':
                echo '<div class="field">';
                switch ($fact->getValue()) {
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
                        echo e($fact->getValue());
                        break;
                }
                echo '</div>';
                break;
            case 'PUBL': // Publication details might contain URLs.
                echo '<div class="field">', Filter::expandUrls($fact->getValue(), $tree), '</div>';
                break;
            case 'REPO':
                $repository = $fact->getTarget();
                if ($repository !== null) {
                    echo '<div><a class="field" href="', e($repository->url()), '">', $repository->getFullName(), '</a></div>';
                } else {
                    echo '<div class="error">', e($fact->getValue()), '</div>';
                }
                break;
            case 'URL':
            case '_URL':
            case 'WWW':
                echo '<div class="field"><a href="', e($fact->getValue()), '">', e($fact->getValue()), '</a></div>';
                break;
            case 'TEXT': // 0 SOUR / 1 TEXT
                echo '<div class="field">', nl2br(e($fact->getValue()), false), '</div>';
                break;
            default:
                // Display the value for all other facts/events
                switch ($fact->getValue()) {
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
                        if (preg_match('/^@(' . WT_REGEX_XREF . ')@$/', $fact->getValue(), $match)) {
                            $target = GedcomRecord::getInstance($match[1], $tree);
                            if ($target) {
                                echo '<div><a href="', e($target->url()), '">', $target->getFullName(), '</a></div>';
                            } else {
                                echo '<div class="error">', e($fact->getValue()), '</div>';
                            }
                        } else {
                            echo '<div class="field"><span dir="auto">', e($fact->getValue()), '</span></div>';
                        }
                        break;
                }
                break;
        }

        // Print the type of this fact/event
        if ($type) {
            $utype = strtoupper($type);
            // Events of close relatives, e.g. _MARR_CHIL
            if (substr($fact->getTag(), 0, 6) == '_MARR_' && ($utype == 'CIVIL' || $utype == 'PARTNERS' || $utype == 'RELIGIOUS')) {
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

        $addr = $fact->getAttribute('ADDR');
        if ($addr !== '') {
            echo GedcomTag::getLabelValue('ADDR', $addr);
        }

        // Print the associates of this fact/event
        if ($fact->getFactId() !== 'asso') {
            echo self::formatAssociateRelationship($fact);
        }

        // Print any other "2 XXXX" attributes, in the order in which they appear.
        preg_match_all('/\n2 (' . WT_REGEX_TAG . ') (.+)/', $fact->getGedcom(), $matches, PREG_SET_ORDER);
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
                    if (count($events) == 1) {
                        echo GedcomTag::getLabelValue('EVEN', $event);
                    } else {
                        echo GedcomTag::getLabelValue('EVEN', implode(I18N::$list_separator, $events));
                    }
                    if (preg_match('/\n3 DATE (.+)/', $fact->getGedcom(), $date_match)) {
                        $date = new Date($date_match[1]);
                        echo GedcomTag::getLabelValue('DATE', $date->display());
                    }
                    if (preg_match('/\n3 PLAC (.+)/', $fact->getGedcom(), $plac_match)) {
                        echo GedcomTag::getLabelValue('PLAC', $plac_match[1]);
                    }
                    break;
                case 'FAMC': // 0 INDI / 1 ADOP / 2 FAMC / 3 ADOP
                    $family = Family::getInstance(str_replace('@', '', $match[2]), $tree);
                    if ($family) {
                        echo GedcomTag::getLabelValue('FAM', '<a href="' . e($family->url()) . '">' . $family->getFullName() . '</a>');
                        if (preg_match('/\n3 ADOP (HUSB|WIFE|BOTH)/', $fact->getGedcom(), $match)) {
                            echo GedcomTag::getLabelValue('ADOP', GedcomCodeAdop::getValue($match[1], $label_person));
                        }
                    } else {
                        echo GedcomTag::getLabelValue('FAM', '<span class="error">' . $match[2] . '</span>');
                    }
                    break;
                case '_WT_USER':
                    $user = User::findByIdentifier($match[2]); // may not exist
                    if ($user) {
                        echo GedcomTag::getLabelValue('_WT_USER', '<span dir="auto">' . e($user->getRealName()) . '</span>');
                    } else {
                        echo GedcomTag::getLabelValue('_WT_USER', e($match[2]));
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
                    if (preg_match('/\n3 TYPE (.+)/', $fact->getGedcom(), $type_match)) {
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
                        if (preg_match('/^@(' . WT_REGEX_XREF . ')@$/', $match[2], $xmatch)) {
                            // Links
                            $linked_record = GedcomRecord::getInstance($xmatch[1], $tree);
                            if ($linked_record) {
                                $link = '<a href="' . e($linked_record->url()) . '">' . $linked_record->getFullName() . '</a>';
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
        echo self::printFactSources($tree, $fact->getGedcom(), 2);
        echo FunctionsPrint::printFactNotes($tree, $fact->getGedcom(), 2);
        self::printMediaLinks($tree, $fact->getGedcom(), 2);
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
        $parent = $event->getParent();
        // To whom is this record an assocate?
        if ($parent instanceof Individual) {
            // On an individual page, we just show links to the person
            $associates = [$parent];
        } elseif ($parent instanceof Family) {
            // On a family page, we show links to both spouses
            $associates = $parent->getSpouses();
        } else {
            // On other pages, it does not make sense to show associates
            return '';
        }

        preg_match_all('/^1 ASSO @(' . WT_REGEX_XREF . ')@((\n[2-9].*)*)/', $event->getGedcom(), $amatches1, PREG_SET_ORDER);
        preg_match_all('/\n2 _?ASSO @(' . WT_REGEX_XREF . ')@((\n[3-9].*)*)/', $event->getGedcom(), $amatches2, PREG_SET_ORDER);

        $html = '';
        // For each ASSO record
        foreach (array_merge($amatches1, $amatches2) as $amatch) {
            $person = Individual::getInstance($amatch[1], $event->getParent()->getTree());
            if ($person && $person->canShowName()) {
                // Is there a "RELA" tag
                if (preg_match('/\n[23] RELA (.+)/', $amatch[2], $rmatch)) {
                    // Use the supplied relationship as a label
                    $label = GedcomCodeRela::getValue($rmatch[1], $person);
                } else {
                    // Use a default label
                    $label = GedcomTag::getLabel('ASSO', $person);
                }

                $values = ['<a href="' . e($person->url()) . '">' . $person->getFullName() . '</a>'];
                foreach ($associates as $associate) {
                    $relationship_name = Functions::getCloseRelationshipName($associate, $person);
                    if (!$relationship_name) {
                        $relationship_name = GedcomTag::getLabel('RELA');
                    }

                    if ($parent instanceof Family) {
                        // For family ASSO records (e.g. MARR), identify the spouse with a sex icon
                        $relationship_name .= $associate->getSexImage();
                    }

                    $values[] = '<a href="' . e(route('relationships', [
                            'xref1' => $associate->getXref(),
                            'xref2' => $person->getXref(),
                            'ged'   => $person->getTree()->getName(),
                        ])) . '" rel="nofollow">' . $relationship_name . '</a>';
                }
                $value = implode(' — ', $values);

                // Use same markup as GedcomTag::getLabelValue()
                $asso = I18N::translate('<span class="label">%1$s:</span> <span class="field" dir="auto">%2$s</span>', $label, $value);
            } elseif (!$person && Auth::isEditor($event->getParent()->getTree())) {
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
     *
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
                $data .= '<div class="fact_SOUR"><span class="label">' . I18N::translate('Source') . ':</span> <span class="field" dir="auto">' . Filter::formatText($source, $tree) . '</span></div>';
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
                    $data .= '<div class="fact_SOUR">';
                    $elementID = Uuid::uuid4()->toString();
                    if ($tree->getPreference('EXPAND_SOURCES')) {
                        $plusminus = 'icon-minus';
                    } else {
                        $plusminus = 'icon-plus';
                    }
                    if ($lt > 0) {
                        $data .= '<a href="#" onclick="return expand_layer(\'' . $elementID . '\');"><i id="' . $elementID . '_img" class="' . $plusminus . '"></i></a> ';
                    }
                    $data .= GedcomTag::getLabelValue('SOUR', '<a href="' . e($source->url()) . '">' . $source->getFullName() . '</a>', null, 'span');
                    $data .= '</div>';

                    $data .= "<div id=\"$elementID\"";
                    if ($tree->getPreference('EXPAND_SOURCES')) {
                        $data .= ' style="display:block"';
                    }
                    $data .= ' class="source_citations">';
                    // PUBL
                    $publ = $source->getFirstFact('PUBL');
                    if ($publ) {
                        $data .= GedcomTag::getLabelValue('PUBL', $publ->getValue());
                    }
                    $data .= self::printSourceStructure($tree, self::getSourceStructure($srec));
                    $data .= '<div class="indent">';
                    ob_start();
                    self::printMediaLinks($tree, $srec, $nlevel);
                    $data .= ob_get_clean();
                    $data .= FunctionsPrint::printFactNotes($tree, $srec, $nlevel);
                    $data .= '</div>';
                    $data .= '</div>';
                } else {
                    // Here we could show that we do actually have sources for this data,
                    // but not the details. For example “Sources: ”.
                    // But not by default, based on user feedback.
                    // https://webtrees.net/index.php/en/forum/3-help-for-beta-and-svn-versions/27002-source-media-privacy-issue
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
    public static function printMediaLinks(Tree $tree, $factrec, $level)
    {
        $nlevel = $level + 1;
        if (preg_match_all("/$level OBJE @(.*)@/", $factrec, $omatch, PREG_SET_ORDER) == 0) {
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
                    echo '<a href="', e($media->url()), '">', $media->getFullName(), '</a>';
                    // NOTE: echo the notes of the media
                    echo '<p>';
                    echo FunctionsPrint::printFactNotes($tree, $media->getGedcom(), 1);
                    $ttype = preg_match('/' . ($nlevel + 1) . ' TYPE (.*)/', $media->getGedcom(), $match);
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
                            echo $spouse->getFullName();
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
                    echo FunctionsPrint::printFactNotes($tree, $media->getGedcom(), $nlevel);
                    echo self::printFactSources($tree, $media->getGedcom(), $nlevel);
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
    public static function printMainSources(Fact $fact, $level)
    {
        $factrec = $fact->getGedcom();
        $parent  = $fact->getParent();
        $tree    = $fact->getParent()->getTree();

        $nlevel = $level + 1;
        if ($fact->isPendingAddition()) {
            $styleadd = 'new';
            $can_edit = $level == 1 && $fact->canEdit();
        } elseif ($fact->isPendingDeletion()) {
            $styleadd = 'old';
            $can_edit = false;
        } else {
            $styleadd = '';
            $can_edit = $level == 1 && $fact->canEdit();
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
                    echo '<tr class="row_sour2">';
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
                if ($factname == 'EVEN' || $factname == 'FACT') {
                    // Add ' EVEN' to provide sensible output for an event with an empty TYPE record
                    $ct = preg_match('/2 TYPE (.*)/', $factrec, $ematch);
                    if ($ct > 0) {
                        $factname = trim($ematch[1]);
                        echo $factname;
                    } else {
                        echo GedcomTag::getLabel($factname, $parent);
                    }
                } elseif ($can_edit) {
                    echo '<a href="' . e(route('edit-fact', [
                            'xref'    => $parent->getXref(),
                            'fact_id' => $fact->getFactId(),
                            'ged'     => $tree->getName(),
                        ])) . '" title="', I18N::translate('Edit'), '">';
                    if ($tree->getPreference('SHOW_FACT_ICONS')) {
                        if ($level == 1) {
                            echo '<i class="icon-source"></i> ';
                        }
                    }
                    echo GedcomTag::getLabel($factname, $parent), '</a>';
                    echo '<div class="editfacts">';
                    if (preg_match('/^@.+@$/', $match[$j][2])) {
                        // Inline sources can't be edited. Attempting to save one will convert it
                        // into a link, and delete it.
                        // e.g. "1 SOUR my source" becomes "1 SOUR @my source@" which does not exist.
                        echo FontAwesome::linkIcon('edit', I18N::translate('Edit'), [
                            'class' => 'btn btn-link',
                            'href'  => route('edit-fact', [
                                'xref'    => $parent->getXref(),
                                'fact_id' => $fact->getFactId(),
                                'ged'     => $tree->getName(),
                            ]),
                        ]);
                        echo FontAwesome::linkIcon('copy', I18N::translate('Copy'), [
                            'class'   => 'btn btn-link',
                            'href'    => '#',
                            'onclick' => 'return copy_fact("' . e($tree->getName()) . '", "' . e($parent->getXref()) . '", "' . $fact->getFactId() . '");',
                        ]);
                    }
                    echo FontAwesome::linkIcon('delete', I18N::translate('Delete'), [
                        'class'   => 'btn btn-link',
                        'href'    => '#',
                        'onclick' => 'return delete_fact("' . I18N::translate('Are you sure you want to delete this fact?') . '", "' . e($tree->getName()) . '", "' . e($parent->getXref()) . '", "' . $fact->getFactId() . '");',
                    ]);
                } else {
                    echo GedcomTag::getLabel($factname, $parent);
                }
                echo '</th>';
                echo '<td class="', $styleadd, '">';
                if ($source) {
                    echo '<a href="', e($source->url()), '">', $source->getFullName(), '</a>';
                    // PUBL
                    $publ = $source->getFirstFact('PUBL');
                    if ($publ) {
                        echo GedcomTag::getLabelValue('PUBL', $publ->getValue());
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
                    if ($nlevel == 2) {
                        self::printMediaLinks($tree, $source->getGedcom(), 1);
                    }
                    echo FunctionsPrint::printFactNotes($tree, $srec, $nlevel);
                    if ($nlevel == 2) {
                        echo FunctionsPrint::printFactNotes($tree, $source->getGedcom(), 1);
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
     * @param Tree     $tree
     * @param string[] $textSOUR
     *
     * @return string
     */
    public static function printSourceStructure(Tree $tree, array $textSOUR): string
    {
        $html = '';

        if ($textSOUR['PAGE']) {
            $html .= GedcomTag::getLabelValue('PAGE', Filter::expandUrls($textSOUR['PAGE'], $tree));
        }

        if ($textSOUR['EVEN']) {
            $html .= GedcomTag::getLabelValue('EVEN', e($textSOUR['EVEN']));
            if ($textSOUR['ROLE']) {
                $html .= GedcomTag::getLabelValue('ROLE', e($textSOUR['ROLE']));
            }
        }

        if ($textSOUR['DATE'] || count($textSOUR['TEXT'])) {
            if ($textSOUR['DATE']) {
                $date = new Date($textSOUR['DATE']);
                $html .= GedcomTag::getLabelValue('DATA:DATE', $date->display());
            }
            foreach ($textSOUR['TEXT'] as $text) {
                $html .= GedcomTag::getLabelValue('TEXT', Filter::formatText($text, $tree));
            }
        }

        if ($textSOUR['QUAY'] != '') {
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
     * @return string[]
     */
    public static function getSourceStructure($srec): array
    {
        // Set up the output array
        $textSOUR = [
            'PAGE' => '',
            'EVEN' => '',
            'ROLE' => '',
            'DATA' => '',
            'DATE' => '',
            'TEXT' => [],
            'QUAY' => '',
        ];

        if ($srec) {
            $subrecords = explode("\n", $srec);
            for ($i = 0; $i < count($subrecords); $i++) {
                $tag  = substr($subrecords[$i], 2, 4);
                $text = substr($subrecords[$i], 7);
                $i++;
                for (; $i < count($subrecords); $i++) {
                    $nextTag = substr($subrecords[$i], 2, 4);
                    if ($nextTag != 'CONT') {
                        $i--;
                        break;
                    }
                    if ($nextTag == 'CONT') {
                        $text .= "\n";
                    }

                    if (substr($subrecords[$i], 7) !== false) {
                        $text .= rtrim(substr($subrecords[$i], 7));
                    }
                }
                if ($tag == 'TEXT') {
                    $textSOUR[$tag][] = $text;
                } else {
                    $textSOUR[$tag] = $text;
                }
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
    public static function printMainNotes(Fact $fact, $level)
    {
        $factrec = $fact->getGedcom();
        $parent  = $fact->getParent();
        $tree    = $parent->getTree();

        if ($fact->isPendingAddition()) {
            $styleadd = ' new';
            $can_edit = $level == 1 && $fact->canEdit();
        } elseif ($fact->isPendingDeletion()) {
            $styleadd = ' old';
            $can_edit = false;
        } else {
            $styleadd = '';
            $can_edit = $level == 1 && $fact->canEdit();
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
                echo '<tr class="row_note2"><th scope="row" class="rela ', $styleadd, ' width20">';
            } else {
                echo '<tr><th scope="row" class="', $styleadd, ' width20">';
            }
            if ($can_edit) {
                if ($level < 2) {
                    if ($note instanceof Note) {
                        echo GedcomTag::getLabel('SHARED_NOTE');
                        echo FontAwesome::linkIcon('note', I18N::translate('View'), [
                            'class' => 'btn btn-link',
                            'href'  => $note->url(),
                        ]);
                    } else {
                        echo GedcomTag::getLabel('NOTE');
                    }
                    echo '<div class="editfacts">';
                    echo FontAwesome::linkIcon('edit', I18N::translate('Edit'), [
                        'class' => 'btn btn-link',
                        'href'  => route('edit-fact', [
                            'xref'    => $parent->getXref(),
                            'fact_id' => $fact->getFactId(),
                            'ged'     => $tree->getName(),
                        ]),
                    ]);
                    echo FontAwesome::linkIcon('copy', I18N::translate('Copy'), [
                        'class'   => 'btn btn-link',
                        'href'    => '#',
                        'onclick' => 'return copy_fact("' . e($tree->getName()) . '", "' . e($parent->getXref()) . '", "' . $fact->getFactId() . '");',
                    ]);
                    echo FontAwesome::linkIcon('delete', I18N::translate('Delete'), [
                        'class'   => 'btn btn-link',
                        'href'    => '#',
                        'onclick' => 'return delete_fact("' . I18N::translate('Are you sure you want to delete this fact?') . '", "' . e($tree->getName()) . '", "' . e($parent->getXref()) . '", "' . $fact->getFactId() . '");',
                    ]);
                    echo '</div>';
                }
            } else {
                if ($level < 2) {
                    if ($tree->getPreference('SHOW_FACT_ICONS')) {
                        echo '<i class="icon-note"></i> ';
                    }
                    if ($note) {
                        echo GedcomTag::getLabel('SHARED_NOTE');
                    } else {
                        echo GedcomTag::getLabel('NOTE');
                    }
                }
                $factlines = explode("\n", $factrec); // 1 BIRT Y\n2 NOTE ...
                $factwords = explode(' ', $factlines[0]); // 1 BIRT Y
                $factname  = $factwords[1]; // BIRT
                if ($factname == 'EVEN' || $factname == 'FACT') {
                    // Add ' EVEN' to provide sensible output for an event with an empty TYPE record
                    $ct = preg_match('/2 TYPE (.*)/', $factrec, $ematch);
                    if ($ct > 0) {
                        $factname = trim($ematch[1]);
                        echo $factname;
                    } else {
                        echo GedcomTag::getLabel($factname, $parent);
                    }
                } elseif ($factname != 'NOTE') {
                    // Note is already printed
                    echo GedcomTag::getLabel($factname, $parent);
                    if ($note) {
                        echo FontAwesome::linkIcon('note', I18N::translate('View'), [
                            'class' => 'btn btn-link',
                            'href'  => $note->url(),
                        ]);
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
    public static function printMainMedia(Fact $fact, $level)
    {
        $factrec = $fact->getGedcom();
        $parent  = $fact->getParent();
        $tree    = $parent->getTree();

        if ($fact->isPendingAddition()) {
            $styleadd = 'new';
            $can_edit = $level == 1 && $fact->canEdit();
        } elseif ($fact->isPendingDeletion()) {
            $styleadd = 'old';
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
                if ($factname == 'EVEN' || $factname == 'FACT') {
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
                    echo '<div class="editfacts">';
                    echo FontAwesome::linkIcon('copy', I18N::translate('Copy'), [
                        'class'   => 'btn btn-link',
                        'href'    => '#',
                        'onclick' => 'return copy_fact("' . e($tree->getName()) . '", "' . e($parent->getXref()) . '", "' . $fact->getFactId() . '");',
                    ]);
                    echo FontAwesome::linkIcon('delete', I18N::translate('Delete'), [
                        'class'   => 'btn btn-link',
                        'href'    => '#',
                        'onclick' => 'return delete_fact("' . I18N::translate('Are you sure you want to delete this fact?') . '", "' . e($tree->getName()) . '", "' . e($parent->getXref()) . '", "' . $fact->getFactId() . '");',
                    ]);
                    echo '</div>';
                } else {
                    echo GedcomTag::getLabel($factname, $parent);
                }
                echo '</th>';
                echo '<td class="', $styleadd, '">';
                if ($media) {
                    echo '<span class="field">';
                    foreach ($media->mediaFiles() as $media_file) {
                        echo $media_file->displayImage(100, 100, 'contain', []);
                    }
                    echo '<a href="' . e($media->url()) . '"> ';
                    echo '<em>';
                    foreach ($media->getAllNames() as $name) {
                        if ($name['type'] != 'TITL') {
                            echo '<br>';
                        }
                        echo $name['full'];
                    }
                    echo '</em>';
                    echo '</a>';
                    echo '</span>';

                    echo FunctionsPrint::printFactNotes($tree, $media->getGedcom(), 1);
                    echo self::printFactSources($tree, $media->getGedcom(), 1);
                } else {
                    echo $xref;
                }
                echo '</td></tr>';
            }
        }
    }
}
