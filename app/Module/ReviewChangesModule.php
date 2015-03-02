<?php
namespace Fisharebest\Webtrees;

/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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

/**
 * Class ReviewChangesModule
 */
class ReviewChangesModule extends Module implements ModuleBlockInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */ I18N::translate('Pending changes');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Pending changes” module */ I18N::translate('A list of changes that need moderator approval, and email notifications.');
	}

	/** {@inheritdoc} */
	public function getBlock($block_id, $template = true, $cfg = null) {
		global $ctype, $WT_TREE;

		$sendmail = get_block_setting($block_id, 'sendmail', '1');
		$days     = get_block_setting($block_id, 'days', '1');
		$block    = get_block_setting($block_id, 'block', '1');

		if ($cfg) {
			foreach (array('days', 'sendmail', 'block') as $name) {
				if (array_key_exists($name, $cfg)) {
					$$name = $cfg[$name];
				}
			}
		}

		$changes = Database::prepare(
			"SELECT 1" .
			" FROM `##change`" .
			" WHERE status='pending'" .
			" LIMIT 1"
		)->fetchOne();

		if ($changes && $sendmail == 'yes') {
			// There are pending changes - tell moderators/managers/administrators about them.
			if (WT_TIMESTAMP - Site::getPreference('LAST_CHANGE_EMAIL') > (60 * 60 * 24 * $days)) {
				// Which users have pending changes?
				foreach (User::all() as $user) {
					if ($user->getPreference('contactmethod') !== 'none') {
						foreach (Tree::getAll() as $tree) {
							if ($tree->hasPendingEdit() && Auth::isManager($tree, $user)) {
								I18N::init($user->getPreference('language'));
								Mail::systemMessage(
									$tree,
									$user,
									I18N::translate('Pending changes'),
									I18N::translate('There are pending changes for you to moderate.') .
									Mail::EOL . Mail::EOL .
									'<a href="' . WT_BASE_URL . 'index.php?ged=' . WT_GEDURL . '">' . WT_BASE_URL . 'index.php?ged=' . WT_GEDURL . '</a>'
								);
								I18N::init(WT_LOCALE);
							}
						}
					}
				}
				Site::setPreference('LAST_CHANGE_EMAIL', WT_TIMESTAMP);
			}
		}
		if (Auth::isEditor($WT_TREE) && $WT_TREE->hasPendingEdit()) {
			$id = $this->getName() . $block_id;
			$class = $this->getName() . '_block';
			if ($ctype === 'user' || Auth::isManager($WT_TREE)) {
				$title = '<i class="icon-admin" title="' . I18N::translate('Configure') . '" onclick="modalDialog(\'block_edit.php?block_id=' . $block_id . '\', \'' . $this->getTitle() . '\');"></i>';
			} else {
				$title = '';
			}
			$title .= $this->getTitle();

			$content = '';
			if (Auth::isModerator($WT_TREE)) {
				$content .= "<a href=\"#\" onclick=\"window.open('edit_changes.php','_blank', chan_window_specs); return false;\">" . I18N::translate('There are pending changes for you to moderate.') . "</a><br>";
			}
			if ($sendmail == "yes") {
				$content .= I18N::translate('Last email reminder was sent ') . format_timestamp(Site::getPreference('LAST_CHANGE_EMAIL')) . "<br>";
				$content .= I18N::translate('Next email reminder will be sent after ') . format_timestamp(Site::getPreference('LAST_CHANGE_EMAIL') + (60 * 60 * 24 * $days)) . "<br><br>";
			}
			$content .= '<ul>';
			$changes = Database::prepare(
				"SELECT xref" .
				" FROM  `##change`" .
				" WHERE status='pending'" .
				" AND   gedcom_id=?" .
				" GROUP BY xref"
			)->execute(array(WT_GED_ID))->fetchAll();
			foreach ($changes as $change) {
				$record = GedcomRecord::getInstance($change->xref);
				if ($record->canShow()) {
					$content .= '<li><a href="' . $record->getHtmlUrl() . '">' . $record->getFullName() . '</a></li>';
				}
			}
			$content .= '</ul>';

			if ($template) {
				if ($block) {
					$class .= ' small_inner_block';
				}
				return Theme::theme()->formatBlock($id, $title, $class, $content);
			} else {
				return $content;
			}
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

	/** {@inheritdoc} */
	public function configureBlock($block_id) {
		if (Filter::postBool('save') && Filter::checkCsrf()) {
			set_block_setting($block_id, 'days', Filter::postInteger('num', 1, 180, 1));
			set_block_setting($block_id, 'sendmail', Filter::postBool('sendmail'));
			set_block_setting($block_id, 'block', Filter::postBool('block'));
		}

		$sendmail = get_block_setting($block_id, 'sendmail', '1');
		$days     = get_block_setting($block_id, 'days', '1');
		$block    = get_block_setting($block_id, 'block', '1');

	?>
	<tr>
		<td colspan="2">
			<?php echo I18N::translate('This block will show editors a list of records with pending changes that need to be approved by a moderator.  It also generates daily emails to moderators whenever pending changes exist.'); ?>
		</td>
	</tr>

	<?php
		echo '<tr><td class="descriptionbox wrap width33">';
		echo I18N::translate('Send out reminder emails?');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('sendmail', $sendmail);
		echo '<br>';
		echo I18N::translate('Reminder email frequency (days)') . "&nbsp;<input type='text' name='days' value='" . $days . "' size='2'>";
		echo '</td></tr>';

		echo '<tr><td class="descriptionbox wrap width33">';
		echo /* I18N: label for a yes/no option */ I18N::translate('Add a scrollbar when block contents grow');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('block', $block);
		echo '</td></tr>';
	}
}
