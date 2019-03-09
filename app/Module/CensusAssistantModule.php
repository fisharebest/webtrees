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
namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Census\Census;
use Fisharebest\Webtrees\Census\CensusInterface;
use Fisharebest\Webtrees\Controller\SimpleController;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\Functions\FunctionsDb;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Soundex;

/**
 * Class CensusAssistantModule
 */
class CensusAssistantModule extends AbstractModule
{
    /** {@inheritdoc} */
    public function getTitle()
    {
        return /* I18N: Name of a module */ I18N::translate('Census assistant');
    }

    /** {@inheritdoc} */
    public function getDescription()
    {
        return /* I18N: Description of the “Census assistant” module */ I18N::translate('An alternative way to enter census transcripts and link them to individuals.');
    }

    /**
     * This is a general purpose hook, allowing modules to respond to routes
     * of the form module.php?mod=FOO&mod_action=BAR
     *
     * @param string $mod_action
     */
    public function modAction($mod_action)
    {
        switch ($mod_action) {
            case 'census_find':
                self::censusFind();
                break;
            case 'media_find':
                self::mediaFind();
                break;
            case 'media_query_3a':
                self::mediaQuery();
                break;
            default:
                http_response_code(404);
        }
    }

    /**
     * Find an individual.
     */
    private static function censusFind()
    {
        global $WT_TREE;

        $controller = new SimpleController;
        $filter     = Filter::get('filter');
        $action     = Filter::get('action');
        $census     = Filter::get('census');
        $census     = new $census;

        $controller
            ->restrictAccess($census instanceof CensusInterface)
            ->setPageTitle(I18N::translate('Find an individual'))
            ->pageHeader();

        echo '<table class="list_table width90" border="0">';
        echo '<tr><td style="padding: 10px;" class="facts_label03 width90">';
        echo I18N::translate('Find an individual');
        echo '</td>';
        echo '</table>';
        echo '<br>';

        if ($action == 'filter') {
            $filter       = trim($filter);
            $filter_array = explode(' ', preg_replace('/ {2,}/', ' ', $filter));

            // Output Individual for GEDFact Assistant ======================
            echo '<table class="list_table width90">';
            $myindilist = FunctionsDb::searchIndividualNames($filter_array, array($WT_TREE));
            if ($myindilist) {
                echo '<tr><td class="list_value_wrap"><ul>';
                usort($myindilist, '\Fisharebest\Webtrees\GedcomRecord::compare');
                foreach ($myindilist as $indi) {
                    echo '<li>';
                    echo '<a href="#" onclick="window.opener.appendCensusRow(\'' . Filter::escapeJs(self::censusTableRow($census, $indi, null)) . '\'); window.close();">';
                    echo '<b>' . $indi->getFullName() . '</b>';
                    echo '</a>';
                    echo $indi->formatFirstMajorFact(WT_EVENTS_BIRT, 1);
                    echo $indi->formatFirstMajorFact(WT_EVENTS_DEAT, 1);
                    echo '<hr>';
                    echo '</li>';
                }
                echo '</ul></td></tr>';
            } else {
                echo '<tr><td class="list_value_wrap">';
                echo I18N::translate('No results found.');
                echo '</td></tr>';
            }
            echo '<tr><td>';
            echo '<button onclick="window.close();">', I18N::translate('close'), '</button>';
            echo '</td></tr>';
            echo '</table>';
        }
    }

    /**
     * Find a media object.
     */
    private static function mediaFind()
    {
        global $WT_TREE;

        $controller = new SimpleController;
        $filter     = Filter::get('filter');
        $multiple   = Filter::getBool('multiple');

        $controller
            ->setPageTitle(I18N::translate('Find an individual'))
            ->pageHeader();

        ?>
        <script>
        function pasterow(id, name, gend, yob, age, bpl) {
            window.opener.opener.insertRowToTable(id, name, '', gend, '', yob, age, 'Y', '', bpl);
        }

        function pasteid(id, name, thumb) {
            if (thumb) {
                window.opener.paste_id(id, name, thumb);
                <?php if (!$multiple) { echo "window.close();"; } ?>
            } else {
            // GEDFact_assistant ========================
            if (window.opener.document.getElementById('addlinkQueue')) {
                window.opener.insertRowToTable(id, name);
            }
            window.opener.paste_id(id);
            if (window.opener.pastename) {
                window.opener.pastename(name);
            }
            <?php if (!$multiple) { echo "window.close();"; } ?>
            }
        }
        function checknames(frm) {
            if (document.forms[0].subclick) {
                button = document.forms[0].subclick.value;
            } else {
                button = "";
            }
            if (frm.filter.value.length < 2 && button !== "all") {
                alert("<?php echo I18N::translate('Please enter more than one character.'); ?>");
                frm.filter.focus();
                return false;
            }
            if (button=="all") {
                frm.filter.value = "";
            }
            return true;
        }
        </script>

        <?php
        echo '<div>';
        echo '<table class="list_table width90" border="0">';
        echo '<tr><td style="padding: 10px;" class="facts_label03 width90">'; // start column for find text header
        echo $controller->getPageTitle();
        echo '</td>';
        echo '</tr>';
        echo '</table>';
        echo '<br>';
        echo '<button onclick="window.close();">', I18N::translate('close'), '</button>';
        echo '<br>';

        $filter       = trim($filter);
        $filter_array = explode(' ', preg_replace('/ {2,}/', ' ', $filter));
        echo '<table class="tabs_table width90"><tr>';
        $myindilist = FunctionsDb::searchIndividualNames($filter_array, array($WT_TREE));
        if ($myindilist) {
            echo '<td class="list_value_wrap"><ul>';
            usort($myindilist, '\Fisharebest\Webtrees\GedcomRecord::compare');
            foreach ($myindilist as $indi) {
                $nam = Filter::escapeHtml($indi->getFullName());
                echo "<li><a href=\"#\" onclick=\"pasterow(
					'" . $indi->getXref() . "' ,
					'" . $nam . "' ,
					'" . $indi->getSex() . "' ,
					'" . $indi->getBirthYear() . "' ,
					'" . (1901 - $indi->getBirthYear()) . "' ,
					'" . $indi->getBirthPlace() . "'); return false;\">
					<b>" . $indi->getFullName() . "</b>&nbsp;&nbsp;&nbsp;";

                $born = GedcomTag::getLabel('BIRT');
                echo "</span><br><span class=\"list_item\">", $born, " ", $indi->getBirthYear(), "&nbsp;&nbsp;&nbsp;", $indi->getBirthPlace(), "</span></a></li>";
                echo "<hr>";
            }
            echo '</ul></td></tr><tr><td class="list_label">', I18N::translate('Total individuals: %s', count($myindilist)), '</tr></td>';
        } else {
            echo "<td class=\"list_value_wrap\">";
            echo I18N::translate('No results found.');
            echo "</td></tr>";
        }
        echo "</table>";
        echo '</div>';
    }

    /**
     * Search for a media object.
     */
    private static function mediaQuery()
    {
        global $WT_TREE;

        $iid2 = Filter::get('iid', WT_REGEX_XREF);

        $controller = new SimpleController;
        $controller
            ->setPageTitle(I18N::translate('Link to an existing media object'))
            ->pageHeader();

        $record = GedcomRecord::getInstance($iid2, $WT_TREE);
        if ($record) {
            $headjs = '';
            if ($record instanceof Family) {
                if ($record->getHusband()) {
                    $headjs = $record->getHusband()->getXref();
                } elseif ($record->getWife()) {
                    $headjs = $record->getWife()->getXref();
                }
            }
            ?>
            <script>
                function insertId() {
                    if (window.opener.document.getElementById('addlinkQueue')) {
                        // alert('Please move this alert window and examine the contents of the pop-up window, then click OK')
                        window.opener.insertRowToTable('<?php echo $record->getXref(); ?>', '<?php echo htmlspecialchars($record->getFullName()); ?>', '<?php echo $headjs; ?>');
                        window.close();
                    }
                }
            </script>
            <?php
        } else {
            ?>
            <script>
                function insertId() {
                    window.opener.alert('<?php echo $iid2; ?> - <?php echo I18N::translate('Not a valid individual, family, or source ID'); ?>');
                    window.close();
                }
            </script>
            <?php
        }
        ?>
        <script>window.onLoad = insertId();</script>
        <?php
    }

    /**
     * Convert custom markup into HTML
     *
     * @param Note $note
     *
     * @return string
     */
    public static function formatCensusNote(Note $note)
    {
        global $WT_TREE;

        if (preg_match('/(.*)((?:\n.*)*)\n\.start_formatted_area\.\n(.+)\n(.+(?:\n.+)*)\n.end_formatted_area\.((?:\n.*)*)/', $note->getNote(), $match)) {
            // This looks like a census-assistant shared note
            $title     = Filter::escapeHtml($match[1]);
            $preamble  = Filter::escapeHtml($match[2]);
            $header    = Filter::escapeHtml($match[3]);
            $data      = Filter::escapeHtml($match[4]);
            $postamble = Filter::escapeHtml($match[5]);

            // Get the column headers for the census to which this note refers
            // requires the fact place & date to match the specific census
            // censusPlace() (Soundex match) and censusDate() functions
            $fmt_headers   = array();
            $linkedRecords = array_merge($note->linkedIndividuals('NOTE'), $note->linkedFamilies('NOTE'));
            $firstRecord   = array_shift($linkedRecords);
            if ($firstRecord) {
                $countryCode = '';
                $date        = '';
                foreach ($firstRecord->getFacts('CENS') as $fact) {
                    if (trim($fact->getAttribute('NOTE'), '@') === $note->getXref()) {
                        $date        = $fact->getAttribute('DATE');
                        $place       = explode(',', strip_tags($fact->getPlace()->getFullName()));
                        $countryCode = Soundex::daitchMokotoff(array_pop($place));
                        break;
                    }
                }

                foreach (Census::allCensusPlaces() as $censusPlace) {
                    if (Soundex::compare($countryCode, Soundex::daitchMokotoff($censusPlace->censusPlace()))) {
                        foreach ($censusPlace->allCensusDates() as $census) {
                            if ($census->censusDate() == $date) {
                                foreach ($census->columns() as $column) {
                                    $abbrev = $column->abbreviation();
                                    if ($abbrev) {
                                        $description          = $column->title() ? $column->title() : I18N::translate('Description unavailable');
                                        $fmt_headers[$abbrev] = '<span title="' . $description . '">' . $abbrev . '</span>';
                                    }
                                }
                                break 2;
                            }
                        }
                    }
                }
            }
            // Substitute header labels and format as HTML
            $thead = '<tr><th>' . strtr(str_replace('|', '</th><th>', $header), $fmt_headers) . '</th></tr>';
            $thead = str_replace('.b.', '', $thead);

            // Format data as HTML
            $tbody = '';
            foreach (explode("\n", $data) as $row) {
                $tbody .= '<tr>';
                foreach (explode('|', $row) as $column) {
                    $tbody .= '<td>' . $column . '</td>';
                }
                $tbody .= '</tr>';
            }

            return
                $title . "\n" . // The newline allows the framework to expand the details and turn the first line into a link
                '<div class="markdown">' .
                '<p>' . $preamble . '</p>' .
                '<table>' .
                '<thead>' . $thead . '</thead>' .
                '<tbody>' . $tbody . '</tbody>' .
                '</table>' .
                '<p>' . $postamble . '</p>' .
                '</div>';
        } else {
            // Not a census-assistant shared note - apply default formatting
            return Filter::formatText($note->getNote(), $WT_TREE);
        }
    }

    /**
     * Generate an HTML row of data for the census header
     *
     * Add prefix cell (store XREF and drag/drop)
     * Add suffix cell (delete button)
     *
     * @param CensusInterface $census
     *
     * @return string
     */
    public static function censusTableHeader(CensusInterface $census)
    {
        $html = '';
        foreach ($census->columns() as $column) {
            $html .= '<th title="' . $column->title() . '">' . $column->abbreviation() . '</th>';
        }

        return '<tr><th hidden></th>' . $html . '<th></th></tr>';
    }

    /**
     * Generate an HTML row of data for the census
     *
     * Add prefix cell (store XREF and drag/drop)
     * Add suffix cell (delete button)
     *
     * @param CensusInterface $census
     *
     * @return string
     */
    public static function censusTableEmptyRow(CensusInterface $census)
    {
        return '<tr><td hidden></td>' . str_repeat('<td><input type="text"></td>', count($census->columns())) . '<td><a class="icon-remove" href="#" title="' . I18N::translate('Remove') . '"></a></td></tr>';
    }

    /**
     * Generate an HTML row of data for the census
     *
     * Add prefix cell (store XREF and drag/drop)
     * Add suffix cell (delete button)
     *
     * @param CensusInterface $census
     * @param Individual      $individual
     * @param Individual|null $head
     *
     * @return string
     */
    public static function censusTableRow(CensusInterface $census, Individual $individual, Individual $head = null)
    {
        $html = '';
        foreach ($census->columns() as $column) {
            $html .= '<td><input type="text" value="' . $column->generate($individual, $head) . '"></td>';
        }

        return '<tr><td hidden>' . $individual->getXref() . '</td>' . $html . '<td><a class="icon-remove" href="#" title="' . I18N::translate('Remove') . '"></a></td></tr>';
    }

    /**
     * Create a family on the census navigator.
     *
     * @param CensusInterface $census
     * @param Family          $family
     * @param Individual      $head
     *
     * @return string
     */
    public static function censusNavigatorFamily(CensusInterface $census, Family $family, Individual $head)
    {
        $headImg2 = '<i class="icon-button_head" title="' . I18N::translate('Head of household') . '"></i>';

        foreach ($family->getSpouses() as $spouse) {
            $menu = new Menu(Functions::getCloseRelationshipName($head, $spouse));
            foreach ($spouse->getChildFamilies() as $grandparents) {
                foreach ($grandparents->getSpouses() as $grandparent) {
                    $submenu = new Menu(
                        Functions::getCloseRelationshipName($head, $grandparent) . ' - ' . $grandparent->getFullName(),
                        '#',
                        '',
                        array('onclick' => 'return appendCensusRow("' . Filter::escapeJs(self::censusTableRow($census, $grandparent, $head)) . '");')
                    );
                    $submenu->addClass('submenuitem', '');
                    $menu->addSubmenu($submenu);
                    $menu->addClass('', 'submenu');
                }
            }

            ?>
            <tr>
                <td class="optionbox">
                    <?php echo $menu->getMenu(); ?>
                </td>
                <td class="facts_value nowrap">
                    <a href="#" onclick="return appendCensusRow('<?php echo Filter::escapeJs(self::censusTableRow($census, $spouse, $head)); ?>');">
                        <?php echo $spouse->getFullName(); ?>
                    </a>
                </td>
                <td class="facts_value">
                    <?php if ($head !== $spouse): ?>
                        <a href="edit_interface.php?action=addnewnote_assisted&amp;noteid=newnote&amp;xref=<?php echo $spouse->getXref(); ?>&amp;gedcom=<?php echo $spouse->getTree()->getNameUrl(); ?>&amp;census=<?php echo get_class($census); ?>">
                            <?php echo $headImg2; ?>
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php
        }

        foreach ($family->getChildren() as $child) {
            $menu = new Menu(Functions::getCloseRelationshipName($head, $child));
            foreach ($child->getSpouseFamilies() as $spouse_family) {
                foreach ($spouse_family->getSpouses() as $spouse_family_spouse) {
                    if ($spouse_family_spouse != $child) {
                        $submenu = new Menu(
                            Functions::getCloseRelationshipName($head, $spouse_family_spouse) . ' - ' . $spouse_family_spouse->getFullName(),
                            '#',
                            '',
                            array('onclick' => 'return appendCensusRow("' . Filter::escapeJs(self::censusTableRow($census, $spouse_family_spouse, $head)) . '");')
                        );
                        $submenu->addClass('submenuitem', '');
                        $menu->addSubmenu($submenu);
                        $menu->addClass('', 'submenu');
                    }
                }
                foreach ($spouse_family->getChildren() as $spouse_family_child) {
                    $submenu = new Menu(
                        Functions::getCloseRelationshipName($head, $spouse_family_child) . ' - ' . $spouse_family_child->getFullName(),
                        '#',
                        '',
                        array('onclick' => 'return appendCensusRow("' . Filter::escapeJs(self::censusTableRow($census, $spouse_family_child, $head)) . '");')
                    );
                    $submenu->addClass('submenuitem', '');
                    $menu->addSubmenu($submenu);
                    $menu->addClass('', 'submenu');
                }
            }

            ?>
            <tr>
                <td class="optionbox">
                    <?php echo $menu->getMenu(); ?>
                </td>
                <td class="facts_value">
                    <a href="#" onclick="return appendCensusRow('<?php echo Filter::escapeJs(self::censusTableRow($census, $child, $head)); ?>');">
                        <?php echo $child->getFullName(); ?>
                    </a>
                </td>
                <td class="facts_value">
                    <?php if ($head !== $child): ?>
                        <a href="edit_interface.php?action=addnewnote_assisted&amp;noteid=newnote&amp;xref=<?php echo $child->getXref(); ?>&amp;gedcom=<?php echo $child->getTree()->getNameUrl(); ?>&amp;census=<?php echo get_class($census); ?>">
                            <?php echo $headImg2; ?>
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php
        }
        echo '<tr><td><br></td></tr>';
    }
}
