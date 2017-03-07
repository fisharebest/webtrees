<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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
use Fisharebest\Webtrees\Datatables;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\FontAwesome;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Theme;

/**
 * Class ResearchTaskModule
 */
class ResearchTaskModule extends AbstractModule implements ModuleBlockInterface {
	const DEFAULT_SHOW_OTHER      = '1';
	const DEFAULT_SHOW_UNASSIGNED = '1';
	const DEFAULT_SHOW_FUTURE     = '1';
	const DEFAULT_BLOCK           = '1';

	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module. Tasks that need further research. */ I18N::translate('Research tasks');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of “Research tasks” module */ I18N::translate('A list of tasks and activities that are linked to the family tree.');
	}

	/**
	 * Generate the HTML content of this block.
	 *
	 * @param int      $block_id
	 * @param bool     $template
	 * @param string[] $cfg
	 *
	 * @return string
	 */
	public function getBlock($block_id, $template = true, $cfg = []) {
		global $ctype, $WT_TREE;

		$show_other      = $this->getBlockSetting($block_id, 'show_other', self::DEFAULT_SHOW_OTHER);
		$show_unassigned = $this->getBlockSetting($block_id, 'show_unassigned', self::DEFAULT_SHOW_UNASSIGNED);
		$show_future     = $this->getBlockSetting($block_id, 'show_future', self::DEFAULT_SHOW_FUTURE);

		foreach (['show_unassigned', 'show_other', 'show_future'] as $name) {
			if (array_key_exists($name, $cfg)) {
				$$name = $cfg[$name];
			}
		}

		$id    = $this->getName() . $block_id;
		$class = $this->getName() . '_block';
		if ($ctype === 'gedcom' && Auth::isManager($WT_TREE) || $ctype === 'user' && Auth::check()) {
			$title = FontAwesome::linkIcon('preferences', I18N::translate('Preferences'), ['class' => 'btn btn-link', 'href' => 'block_edit.php?block_id=' . $block_id . '&ged=' . $WT_TREE->getNameHtml() . '&ctype=' . $ctype]) . ' ';
		} else {
			$title = '';
		}
		$title .= $this->getTitle();

		$end_jd = $show_future ? 99999999 : WT_CLIENT_JD;

		$xrefs = Database::prepare(
			"SELECT DISTINCT d_gid FROM `##dates`" .
			" WHERE d_file = :tree_id AND d_fact = '_TODO' AND d_julianday1 < :jd"
		)->execute([
			'tree_id' => $WT_TREE->getTreeId(),
			'jd'      => $end_jd,
		])->fetchOneColumn();

		$content = '';
		$content .= '<table ' . Datatables::researchTaskTableAttributes() . '>';
		$content .= '<thead><tr>';
		$content .= '<th>' . I18N::translate('Date') . '</th>';
		$content .= '<th>' . I18N::translate('Record') . '</th>';
		$content .= '<th>' . I18N::translate('Username') . '</th>';
		$content .= '<th>' . I18N::translate('Research task') . '</th>';
		$content .= '</tr></thead><tbody>';

		foreach ($xrefs as $xref) {
			$record = GedcomRecord::getInstance($xref, $WT_TREE);
			if ($record->canShow()) {
				foreach ($record->getFacts('_TODO') as $fact) {
					$user_name = $fact->getAttribute('_WT_USER');
					if ($user_name === Auth::user()->getUserName() || !$user_name && $show_unassigned || $user_name && $show_other) {
						$content .= '<tr>';
						$content .= '<td data-sort="' . $fact->getDate()->julianDay() . '">' . $fact->getDate()->display() . '</td>';
						$content .= '<td data-sort="' . Filter::escapeHtml($record->getSortName()) . '"><a href="' . $record->getHtmlUrl() . '">' . $record->getFullName() . '</a></td>';
						$content .= '<td>' . $user_name . '</td>';
						$content .= '<td dir="auto">' . $fact->getValue() . '</td>';
						$content .= '</tr>';
					}
				}
			}
		}

		$content .= '</tbody></table>';
		if (empty($xrefs)) {
			$content .= '<p>' . I18N::translate('There are no research tasks in this family tree.') . '</p>';
		}

		if ($template) {
			return Theme::theme()->formatBlock($id, $title, $class, $content);
		} else {
			return $content;
		}
	}

	/** {@inheritdoc} */
	public function loadAjax() {
		return false;
	}

	/** {@inheritdoc} */
	public function isUserBlock() {
		return true;
	}

	/** {@inheritdoc} */
	public function isGedcomBlock() {
		return true;
	}

	/**
	 * An HTML form to edit block settings
	 *
	 * @param int $block_id
	 */
	public function configureBlock($block_id) {
		if (Filter::postBool('save') && Filter::checkCsrf()) {
			$this->setBlockSetting($block_id, 'show_other', Filter::postBool('show_other'));
			$this->setBlockSetting($block_id, 'show_unassigned', Filter::postBool('show_unassigned'));
			$this->setBlockSetting($block_id, 'show_future', Filter::postBool('show_future'));
		}

		$show_other      = $this->getBlockSetting($block_id, 'show_other', self::DEFAULT_SHOW_OTHER);
		$show_unassigned = $this->getBlockSetting($block_id, 'show_unassigned', self::DEFAULT_SHOW_UNASSIGNED);
		$show_future     = $this->getBlockSetting($block_id, 'show_future', self::DEFAULT_SHOW_FUTURE);

		?>
		<p>
			<?= I18N::translate('Research tasks are special events, added to individuals in your family tree, which identify the need for further research. You can use them as a reminder to check facts against more reliable sources, to obtain documents or photographs, to resolve conflicting information, etc.') ?>
			<?= I18N::translate('To create new research tasks, you must first add “research task” to the list of facts and events in the family tree’s preferences.') ?>
			<?= I18N::translate('Research tasks are stored using the custom GEDCOM tag “_TODO”. Other genealogy applications may not recognize this tag.') ?>
		</p>
		<?php

		echo '<div class="form-group row"><label class="col-sm-3 col-form-label" for="show_other">';
		echo I18N::translate('Show research tasks that are assigned to other users');
		echo '</div><div class="col-sm-9">';
		echo Bootstrap4::radioButtons('show_other', FunctionsEdit::optionsNoYes(), $show_other, true);
		echo '</div></div>';

		echo '<div class="form-group row"><label class="col-sm-3 col-form-label" for="show_unassigned">';
		echo I18N::translate('Show research tasks that are not assigned to any user');
		echo '</div><div class="col-sm-9">';
		echo Bootstrap4::radioButtons('show_unassigned', FunctionsEdit::optionsNoYes(), $show_unassigned, true);
		echo '</div></div>';

		echo '<div class="form-group row"><label class="col-sm-3 col-form-label" for="show_future">';
		echo I18N::translate('Show research tasks that have a date in the future');
		echo '</div><div class="col-sm-9">';
		echo Bootstrap4::radioButtons('show_future', FunctionsEdit::optionsNoYes(), $show_future, true);
		echo '</div></div>';
	}
}
