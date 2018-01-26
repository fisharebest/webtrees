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
use Fisharebest\Webtrees\Bootstrap4;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\FontAwesome;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Tree;
use Ramsey\Uuid\Uuid;

/**
 * Class RecentChangesModule
 */
class RecentChangesModule extends AbstractModule implements ModuleBlockInterface {
	const DEFAULT_BLOCK      = '1';
	const DEFAULT_DAYS       = 7;
	const DEFAULT_HIDE_EMPTY = '0';
	const DEFAULT_SHOW_USER  = '1';
	const DEFAULT_SORT_STYLE = 'date_desc';
	const DEFAULT_INFO_STYLE = 'table';
	const MAX_DAYS           = 90;

	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */ I18N::translate('Recent changes');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Recent changes” module */ I18N::translate('A list of records that have been updated recently.');
	}

	/** {@inheritdoc} */
	public function getBlock($block_id, $template = true, $cfg = []): string {
		global $ctype, $WT_TREE;

		$days      = $this->getBlockSetting($block_id, 'days', self::DEFAULT_DAYS);
		$infoStyle = $this->getBlockSetting($block_id, 'infoStyle', self::DEFAULT_INFO_STYLE);
		$sortStyle = $this->getBlockSetting($block_id, 'sortStyle', self::DEFAULT_SORT_STYLE);
		$show_user = $this->getBlockSetting($block_id, 'show_user', self::DEFAULT_SHOW_USER);

		foreach (['days', 'infoStyle', 'sortStyle', 'show_user'] as $name) {
			if (array_key_exists($name, $cfg)) {
				$$name = $cfg[$name];
			}
		}

		$records = $this->getRecentChanges($WT_TREE, $days);

		$content = '';
		// Print block content
		if (empty($records)) {
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
			if ($ctype === 'gedcom' && Auth::isManager($WT_TREE)) {
				$config_url = route('tree-page-block-edit', ['block_id' => $block_id, 'ged' => $WT_TREE->getName()]);
			} elseif ($ctype === 'user' && Auth::check()) {
				$config_url = route('user-page-block-edit', ['block_id' => $block_id, 'ged' => $WT_TREE->getName()]);
			} else {
				$config_url = '';
			}

			return view('blocks/template', [
				'block'      => str_replace('_', '-', $this->getName()),
				'id'         => $block_id,
				'config_url' => $config_url,
				'title'      => I18N::plural('Changes in the last %s day', 'Changes in the last %s days', $days, I18N::number($days)),
				'content'    => $content,
			]);
		} else {
			return $content;
		}
	}

	/** {@inheritdoc} */
	public function loadAjax(): bool {
		return true;
	}

	/** {@inheritdoc} */
	public function isUserBlock(): bool {
		return true;
	}

	/** {@inheritdoc} */
	public function isGedcomBlock(): bool {
		return true;
	}

	/** {@inheritdoc} */
	public function configureBlock($block_id) {
		if (Filter::postBool('save') && Filter::checkCsrf()) {
			$this->setBlockSetting($block_id, 'days', Filter::postInteger('days', 1, self::MAX_DAYS));
			$this->setBlockSetting($block_id, 'infoStyle', Filter::post('infoStyle', 'list|table'));
			$this->setBlockSetting($block_id, 'sortStyle', Filter::post('sortStyle', 'name|date_asc|date_desc'));
			$this->setBlockSetting($block_id, 'show_user', Filter::postBool('show_user'));
		}

		$days      = $this->getBlockSetting($block_id, 'days', self::DEFAULT_DAYS);
		$infoStyle = $this->getBlockSetting($block_id, 'infoStyle', self::DEFAULT_INFO_STYLE);
		$sortStyle = $this->getBlockSetting($block_id, 'sortStyle', self::DEFAULT_SORT_STYLE);
		$show_user = $this->getBlockSetting($block_id, 'show_user', self::DEFAULT_SHOW_USER);

		echo '<div class="form-group row"><label class="col-sm-3 col-form-label" for="days">';
		echo I18N::translate('Number of days to show');
		echo '</div><div class="col-sm-9">';
		echo '<input type="text" name="days" size="2" value="', $days, '">';
		echo ' ' . I18N::plural('maximum %s day', 'maximum %s days', I18N::number(self::MAX_DAYS), I18N::number(self::MAX_DAYS));
		echo '</div></div>';

		echo '<div class="form-group row"><label class="col-sm-3 col-form-label" for="infoStyle">';
		echo I18N::translate('Presentation style');
		echo '</div><div class="col-sm-9">';
		echo Bootstrap4::select(['list' => I18N::translate('list'), 'table' => I18N::translate('table')], $infoStyle, ['id' => 'infoStyle', 'name' => 'infoStyle']);
		echo '</div></div>';

		echo '<div class="form-group row"><label class="col-sm-3 col-form-label" for="sortStyle">';
		echo I18N::translate('Sort order');
		echo '</div><div class="col-sm-9">';
		echo Bootstrap4::select([
			'name'      => /* I18N: An option in a list-box */ I18N::translate('sort by name'),
			'date_asc'  => /* I18N: An option in a list-box */ I18N::translate('sort by date, oldest first'),
			'date_desc' => /* I18N: An option in a list-box */ I18N::translate('sort by date, newest first'),
		], $sortStyle, ['id' => 'sortStyle', 'name' => 'sortStyle']);
		echo '</div></div>';

		echo '<div class="form-group row"><label class="col-sm-3 col-form-label" for="show_usere">';
		echo /* I18N: label for a yes/no option */ I18N::translate('Show the user who made the change');
		echo '</div><div class="col-sm-9">';
		echo Bootstrap4::radioButtons('show_user', FunctionsEdit::optionsNoYes(), $show_user, true);
		echo '</div></div>';
	}

	/**
	 * Find records that have changed since a given julian day
	 *
	 * @param Tree $tree Changes for which tree
	 * @param int  $days Number of days
	 *
	 * @return GedcomRecord[] List of records with changes
	 */
	private function getRecentChanges(Tree $tree, $days) {
		$sql =
			"SELECT xref FROM `##change`" .
			" WHERE new_gedcom != '' AND change_time > DATE_SUB(NOW(), INTERVAL :days DAY) AND gedcom_id = :tree_id" .
			" GROUP BY xref" .
			" ORDER BY MAX(change_id) DESC";

		$vars = [
			'days'    => $days,
			'tree_id' => $tree->getTreeId(),
		];

		$xrefs = Database::prepare($sql)->execute($vars)->fetchOneColumn();

		$records = [];
		foreach ($xrefs as $xref) {
			$record = GedcomRecord::getInstance($xref, $tree);
			if ($record && $record->canShow()) {
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
	private function changesList(array $records, $sort, $show_user) {
		switch ($sort) {
			case 'name':
				uasort($records, ['self', 'sortByNameAndChangeDate']);
				break;
			case 'date_asc':
				uasort($records, ['self', 'sortByChangeDateAndName']);
				$records = array_reverse($records);
				break;
			case 'date_desc':
				uasort($records, ['self', 'sortByChangeDateAndName']);
		}

		$html = '';
		foreach ($records as $record) {
			$html .= '<a href="' . e($record->url()) . '" class="list_item name2">' . $record->getFullName() . '</a>';
			$html .= '<div class="indent" style="margin-bottom: 5px;">';
			if ($record instanceof Individual) {
				if ($record->getAddName()) {
					$html .= '<a href="' . e($record->url()) . '" class="list_item">' . $record->getAddName() . '</a>';
				}
			}

			// The timestamp may be missing or private.
			$timestamp = $record->lastChangeTimestamp();
			if ($timestamp !== '') {
				if ($show_user) {
					$html .= /* I18N: [a record was] Changed on <date/time> by <user> */
						I18N::translate('Changed on %1$s by %2$s', $timestamp, e($record->lastChangeUser()));
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
	private function changesTable($records, $sort, $show_user) {
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
			->addInlineJavascript('
				$("#' . $table_id . '").dataTable({
					dom: \'t\',
					paging: false,
					autoWidth:false,
					lengthChange: false,
					filter: false,
					' . I18N::datatablesI18N() . ',
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
		$html .= '<th>' . I18N::translate('Last change') . '</th>';
		$html .= '<th>' . GedcomTag::getLabel('_WT_USER') . '</th>';
		$html .= '</tr></thead><tbody>';

		foreach ($records as $record) {
			$html .= '<tr><td>';
			switch ($record::RECORD_TYPE) {
				case 'INDI':
					$html .= FontAwesome::semanticIcon('individual', I18N::translate('Individual'));
					break;
				case 'FAM':
					$html .= FontAwesome::semanticicon('family', I18N::translate('Family'));
					break;
				case 'OBJE':
					$html .= FontAwesome::semanticIcon('media', I18N::translate('Media'));
					break;
				case 'NOTE':
					$html .= FontAwesome::semanticIcon('note', I18N::translate('Note'));
					break;
				case 'SOUR':
					$html .= FontAwesome::semanticIcon('source', I18N::translate('Source'));
					break;
				case 'SUBM':
					$html .= FontAwesome::semanticIcon('submitter', I18N::translate('Submitter'));
					break;
				case 'REPO':
					$html .= FontAwesome::semanticIcon('repository', I18N::translate('Repository'));
					break;
			}
			$html .= '</td>';
			$html .= '<td data-sort="' . e($record->getSortName()) . '">';
			$html .= '<a href="' . e($record->url()) . '">' . $record->getFullName() . '</a>';
			$addname = $record->getAddName();
			if ($addname) {
				$html .= '<div class="indent"><a href="' . e($record->url()) . '">' . $addname . '</a></div>';
			}
			$html .= '</td>';
			$html .= '<td data-sort="' . $record->lastChangeTimestamp(true) . '">' . $record->lastChangeTimestamp() . '</td>';
			$html .= '<td>' . e($record->lastChangeUser()) . '</td>';
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
	private static function sortByChangeDateAndName(GedcomRecord $a, GedcomRecord $b) {
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
	private static function sortByNameAndChangeDate(GedcomRecord $a, GedcomRecord $b) {
		return GedcomRecord::compare($a, $b) ?: $b->lastChangeTimestamp(true) - $a->lastChangeTimestamp(true);
	}
}
