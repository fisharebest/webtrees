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
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Tree;

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
		$show_user = (bool) $this->getBlockSetting($block_id, 'show_user', self::DEFAULT_SHOW_USER);

		extract($cfg, EXTR_OVERWRITE);

		$records = $this->getRecentChanges($WT_TREE, $days);

		switch ($sortStyle) {
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

		if (empty($records)) {
			$content = I18N::plural('There have been no changes within the last %s day.', 'There have been no changes within the last %s days.', $days, I18N::number($days));
		} elseif ($infoStyle === 'list') {
			$content = $this->changesList($records, $show_user);
		} else {
			$content = view('blocks/changes-' . $infoStyle, [
				'records'   => $records,
				'show_user' => $show_user,
			]);
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
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$this->setBlockSetting($block_id, 'days', Filter::postInteger('days', 1, self::MAX_DAYS));
			$this->setBlockSetting($block_id, 'infoStyle', Filter::post('infoStyle', 'list|table'));
			$this->setBlockSetting($block_id, 'sortStyle', Filter::post('sortStyle', 'name|date_asc|date_desc'));
			$this->setBlockSetting($block_id, 'show_user', Filter::postBool('show_user'));

			return;
		}

		$days      = $this->getBlockSetting($block_id, 'days', self::DEFAULT_DAYS);
		$infoStyle = $this->getBlockSetting($block_id, 'infoStyle', self::DEFAULT_INFO_STYLE);
		$sortStyle = $this->getBlockSetting($block_id, 'sortStyle', self::DEFAULT_SORT_STYLE);
		$show_user = $this->getBlockSetting($block_id, 'show_user', self::DEFAULT_SHOW_USER);

		$info_styles = [
			'list'  => /* I18N: An option in a list-box */ I18N::translate('list'),
			'table' => /* I18N: An option in a list-box */ I18N::translate('table'),
		];

		$sort_styles = [
			'name'      => /* I18N: An option in a list-box */ I18N::translate('sort by name'),
			'date_asc'  => /* I18N: An option in a list-box */ I18N::translate('sort by date, oldest first'),
			'date_desc' => /* I18N: An option in a list-box */ I18N::translate('sort by date, newest first'),
		];

		echo view('blocks/recent-changes-config', [
			'days'        => $days,
			'infoStyle'   => $infoStyle,
			'info_styles' => $info_styles,
			'max_days'    => self::MAX_DAYS,
			'sortStyle'   => $sortStyle,
			'sort_styles' => $sort_styles,
			'show_user'   => $show_user,
		]);
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
	 * @param bool           $show_user
	 *
	 * @return string
	 */
	private function changesList(array $records, $show_user) {
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
