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
namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\Tree;
use Rhumsaa\Uuid\Uuid;

/**
 * Class RecentChangesModule
 */
class RecentChangesModule extends AbstractModule implements ModuleBlockInterface
{
    const DEFAULT_BLOCK      = '1';
    const DEFAULT_DAYS       = 7;
    const DEFAULT_HIDE_EMPTY = '0';
    const DEFAULT_SHOW_USER  = '1';
    const DEFAULT_SORT_STYLE = 'date_desc';
    const DEFAULT_INFO_STYLE = 'table';
    const MAX_DAYS           = 90;

    /** {@inheritdoc} */
    public function getTitle()
    {
        return /* I18N: Name of a module */ I18N::translate('Recent changes');
    }

    /** {@inheritdoc} */
    public function getDescription()
    {
        return /* I18N: Description of the “Recent changes” module */ I18N::translate('A list of records that have been updated recently.');
    }

    /** {@inheritdoc} */
    public function getBlock($block_id, $template = true, $cfg = array())
    {
        global $ctype, $WT_TREE;

        $days       = $this->getBlockSetting($block_id, 'days', self::DEFAULT_DAYS);
        $infoStyle  = $this->getBlockSetting($block_id, 'infoStyle', self::DEFAULT_INFO_STYLE);
        $sortStyle  = $this->getBlockSetting($block_id, 'sortStyle', self::DEFAULT_SORT_STYLE);
        $show_user  = $this->getBlockSetting($block_id, 'show_user', self::DEFAULT_SHOW_USER);
        $block      = $this->getBlockSetting($block_id, 'block', self::DEFAULT_BLOCK);
        $hide_empty = $this->getBlockSetting($block_id, 'hide_empty', self::DEFAULT_HIDE_EMPTY);

        foreach (array('days', 'infoStyle', 'sortStyle', 'hide_empty', 'show_user', 'block') as $name) {
            if (array_key_exists($name, $cfg)) {
                $$name = $cfg[$name];
            }
        }

        $records = $this->getRecentChanges($WT_TREE, WT_CLIENT_JD - $days);

        if (empty($records) && $hide_empty) {
            return '';
        }

        // Print block header
        $id    = $this->getName() . $block_id;
        $class = $this->getName() . '_block';

        if ($ctype === 'gedcom' && Auth::isManager($WT_TREE) || $ctype === 'user' && Auth::check()) {
            $title = '<a class="icon-admin" title="' . I18N::translate('Preferences') . '" href="block_edit.php?block_id=' . $block_id . '&amp;ged=' . $WT_TREE->getNameHtml() . '&amp;ctype=' . $ctype . '"></a>';
        } else {
            $title = '';
        }
        $title .= /* I18N: title for list of recent changes */ I18N::plural('Changes in the last %s day', 'Changes in the last %s days', $days, I18N::number($days));

        $content = '';
        // Print block content
        if (count($records) == 0) {
            $content .= I18N::plural('There have been no changes within the last %s day.', 'There have been no changes within the last %s days.', $days, I18N::number($days));
        } else {
            switch ($infoStyle) {
                case 'list':
                    $content .= $this->changesList($records, $sortStyle, $show_user);
                    break;
                case 'table':
                    $content .= $this->changesTable($records, $sortStyle, $show_user);
                    break;
            }
        }

        if ($template) {
            if ($block) {
                $class .= ' small_inner_block';
            }

            return Theme::theme()->formatBlock($id, $title, $class, $content);
        } else {
            return $content;
        }
    }

    /** {@inheritdoc} */
    public function loadAjax()
    {
        return true;
    }

    /** {@inheritdoc} */
    public function isUserBlock()
    {
        return true;
    }

    /** {@inheritdoc} */
    public function isGedcomBlock()
    {
        return true;
    }

    /** {@inheritdoc} */
    public function configureBlock($block_id)
    {
        if (Filter::postBool('save') && Filter::checkCsrf()) {
            $this->setBlockSetting($block_id, 'days', Filter::postInteger('days', 1, self::MAX_DAYS));
            $this->setBlockSetting($block_id, 'infoStyle', Filter::post('infoStyle', 'list|table'));
            $this->setBlockSetting($block_id, 'sortStyle', Filter::post('sortStyle', 'name|date_asc|date_desc'));
            $this->setBlockSetting($block_id, 'show_user', Filter::postBool('show_user'));
            $this->setBlockSetting($block_id, 'hide_empty', Filter::postBool('hide_empty'));
            $this->setBlockSetting($block_id, 'block', Filter::postBool('block'));
        }

        $days       = $this->getBlockSetting($block_id, 'days', self::DEFAULT_DAYS);
        $infoStyle  = $this->getBlockSetting($block_id, 'infoStyle', self::DEFAULT_INFO_STYLE);
        $sortStyle  = $this->getBlockSetting($block_id, 'sortStyle', self::DEFAULT_SORT_STYLE);
        $show_user  = $this->getBlockSetting($block_id, 'show_user', self::DEFAULT_SHOW_USER);
        $block      = $this->getBlockSetting($block_id, 'block', self::DEFAULT_BLOCK);
        $hide_empty = $this->getBlockSetting($block_id, 'hide_empty', self::DEFAULT_HIDE_EMPTY);

        echo '<tr><td class="descriptionbox wrap width33">';
        echo I18N::translate('Number of days to show');
        echo '</td><td class="optionbox">';
        echo '<input type="text" name="days" size="2" value="', $days, '">';
        echo ' <em>', I18N::plural('maximum %s day', 'maximum %s days', I18N::number(self::MAX_DAYS), I18N::number(self::MAX_DAYS)), '</em>';
        echo '</td></tr>';

        echo '<tr><td class="descriptionbox wrap width33">';
        echo I18N::translate('Presentation style');
        echo '</td><td class="optionbox">';
        echo FunctionsEdit::selectEditControl('infoStyle', array('list' => I18N::translate('list'), 'table' => I18N::translate('table')), null, $infoStyle, '');
        echo '</td></tr>';

        echo '<tr><td class="descriptionbox wrap width33">';
        echo I18N::translate('Sort order');
        echo '</td><td class="optionbox">';
        echo FunctionsEdit::selectEditControl('sortStyle', array(
            'name'      => /* I18N: An option in a list-box */ I18N::translate('sort by name'),
            'date_asc'  => /* I18N: An option in a list-box */ I18N::translate('sort by date, oldest first'),
            'date_desc' => /* I18N: An option in a list-box */ I18N::translate('sort by date, newest first'),
        ), null, $sortStyle, '');
        echo '</td></tr>';

        echo '<tr><td class="descriptionbox wrap width33">';
        echo /* I18N: label for a yes/no option */ I18N::translate('Show the user who made the change');
        echo '</td><td class="optionbox">';
        echo FunctionsEdit::editFieldYesNo('show_user', $show_user);
        echo '</td></tr>';

        echo '<tr><td class="descriptionbox wrap width33">';
        echo /* I18N: label for a yes/no option */ I18N::translate('Add a scrollbar when block contents grow');
        echo '</td><td class="optionbox">';
        echo FunctionsEdit::editFieldYesNo('block', $block);
        echo '</td></tr>';

        echo '<tr><td class="descriptionbox wrap width33">';
        echo I18N::translate('Should this block be hidden when it is empty');
        echo '</td><td class="optionbox">';
        echo FunctionsEdit::editFieldYesNo('hide_empty', $hide_empty);
        echo '</td></tr>';
        echo '<tr><td colspan="2" class="optionbox wrap">';
        echo '<span class="error">', I18N::translate('If you hide an empty block, you will not be able to change its configuration until it becomes visible by no longer being empty.'), '</span>';
        echo '</td></tr>';
    }

    /**
     * Find records that have changed since a given julian day
     *
     * @param Tree $tree Changes for which tree
     * @param int  $jd   Julian day
     *
     * @return GedcomRecord[] List of records with changes
     */
    private function getRecentChanges(Tree $tree, $jd)
    {
        $sql =
            "SELECT d_gid FROM `##dates`" .
            " WHERE d_fact='CHAN' AND d_julianday1 >= :jd AND d_file = :tree_id";

        $vars = array(
            'jd'      => $jd,
            'tree_id' => $tree->getTreeId(),
        );

        $xrefs = Database::prepare($sql)->execute($vars)->fetchOneColumn();

        $records = array();
        foreach ($xrefs as $xref) {
            $record = GedcomRecord::getInstance($xref, $tree);
            if ($record->canShow()) {
                $records[] = $record;
            }
        }

        return $records;
    }

    /**
     * Format a table of events
     *
     * @param GedcomRecord[] $records
     * @param string         $sort
     * @param bool           $show_user
     *
     * @return string
     */
    private function changesList(array $records, $sort, $show_user)
    {
        switch ($sort) {
            case 'name':
                uasort($records, array('self', 'sortByNameAndChangeDate'));
                break;
            case 'date_asc':
                uasort($records, array('self', 'sortByChangeDateAndName'));
                $records = array_reverse($records);
                break;
            case 'date_desc':
                uasort($records, array('self', 'sortByChangeDateAndName'));
        }

        $html = '';
        foreach ($records as $record) {
            $html .= '<a href="' . $record->getHtmlUrl() . '" class="list_item name2">' . $record->getFullName() . '</a>';
            $html .= '<div class="indent" style="margin-bottom: 5px;">';
            if ($record instanceof Individual) {
                if ($record->getAddName()) {
                    $html .= '<a href="' . $record->getHtmlUrl() . '" class="list_item">' . $record->getAddName() . '</a>';
                }
            }

            // The timestamp may be missing or private.
            $timestamp = $record->lastChangeTimestamp();
            if ($timestamp !== '') {
                if ($show_user) {
                    $html .= /* I18N: [a record was] Changed on <date/time> by <user> */
                        I18N::translate('Changed on %1$s by %2$s', $timestamp, Filter::escapeHtml($record->lastChangeUser()));
                } else {
                    $html .= /* I18N: [a record was] Changed on <date/time> */
                        I18N::translate('Changed on %1$s', $timestamp);
                }
            }
            $html .= '</div>';
        }

        return $html;
    }

    /**
     * Format a table of events
     *
     * @param GedcomRecord[] $records
     * @param string         $sort
     * @param bool           $show_user
     *
     * @return string
     */
    private function changesTable($records, $sort, $show_user)
    {
        global $controller;

        $table_id = 'table-chan-' . Uuid::uuid4(); // lists requires a unique ID in case there are multiple lists per page

        switch ($sort) {
            case 'name':
            default:
                $aaSorting = "[1,'asc'], [2,'desc']";
                break;
            case 'date_asc':
                $aaSorting = "[2,'asc'], [1,'asc']";
                break;
            case 'date_desc':
                $aaSorting = "[2,'desc'], [1,'asc']";
                break;
        }

        $html = '';
        $controller
            ->addExternalJavascript(WT_JQUERY_DATATABLES_JS_URL)
            ->addInlineJavascript('
				jQuery("#' . $table_id . '").dataTable({
					dom: \'t\',
					paging: false,
					autoWidth:false,
					lengthChange: false,
					filter: false,
					' . I18N::datatablesI18N() . ',
					jQueryUI: true,
					sorting: [' . $aaSorting . '],
					columns: [
						{ sortable: false, class: "center" },
						null,
						null,
						{ visible: ' . ($show_user ? 'true' : 'false') . ' }
					]
				});
			');

        $html .= '<table id="' . $table_id . '" class="width100">';
        $html .= '<thead><tr>';
        $html .= '<th></th>';
        $html .= '<th>' . I18N::translate('Record') . '</th>';
        $html .= '<th>' . GedcomTag::getLabel('CHAN') . '</th>';
        $html .= '<th>' . GedcomTag::getLabel('_WT_USER') . '</th>';
        $html .= '</tr></thead><tbody>';

        foreach ($records as $record) {
            $html .= '<tr><td>';
            switch ($record::RECORD_TYPE) {
                case 'INDI':
                    $html .= $record->getSexImage('small');
                    break;
                case 'FAM':
                    $html .= '<i class="icon-button_family"></i>';
                    break;
                case 'OBJE':
                    $html .= '<i class="icon-button_media"></i>';
                    break;
                case 'NOTE':
                    $html .= '<i class="icon-button_note"></i>';
                    break;
                case 'SOUR':
                    $html .= '<i class="icon-button_source"></i>';
                    break;
                case 'REPO':
                    $html .= '<i class="icon-button_repository"></i>';
                    break;
            }
            $html .= '</td>';
            $html .= '<td data-sort="' . Filter::escapeHtml($record->getSortName()) . '">';
            $html .= '<a href="' . $record->getHtmlUrl() . '">' . $record->getFullName() . '</a>';
            $addname = $record->getAddName();
            if ($addname) {
                $html .= '<div class="indent"><a href="' . $record->getHtmlUrl() . '">' . $addname . '</a></div>';
            }
            $html .= '</td>';
            $html .= '<td data-sort="' . $record->lastChangeTimestamp(true) . '">' . $record->lastChangeTimestamp() . '</td>';
            $html .= '<td>' . Filter::escapeHtml($record->lastChangeUser()) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        return $html;
    }

    /**
     * Sort the records by (1) last change date and (2) name
     *
     * @param GedcomRecord $a
     * @param GedcomRecord $b
     *
     * @return int
     */
    private static function sortByChangeDateAndName(GedcomRecord $a, GedcomRecord $b)
    {
        return $b->lastChangeTimestamp(true) - $a->lastChangeTimestamp(true) ?: GedcomRecord::compare($a, $b);
    }

    /**
     * Sort the records by (1) name and (2) last change date
     *
     * @param GedcomRecord $a
     * @param GedcomRecord $b
     *
     * @return int
     */
    private static function sortByNameAndChangeDate(GedcomRecord $a, GedcomRecord $b)
    {
        return GedcomRecord::compare($a, $b) ?: $b->lastChangeTimestamp(true) - $a->lastChangeTimestamp(true);
    }
}
