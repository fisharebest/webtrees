<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2010 John Finlay
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use WT\Auth;
use WT\User;

class review_changes_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return /* I18N: Name of a module */ WT_I18N::translate('Pending changes');
	}

	// Extend class WT_Module
	public function getDescription() {
		return /* I18N: Description of the “Pending changes” module */ WT_I18N::translate('A list of changes that need moderator approval, and email notifications.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id, $template=true, $cfg=null) {
		global $ctype, $WEBTREES_EMAIL;

		$changes=WT_DB::prepare(
			"SELECT 1".
			" FROM `##change`".
			" WHERE status='pending'".
			" LIMIT 1"
		)->fetchOne();

		$days    =get_block_setting($block_id, 'days',     1);
		$sendmail=get_block_setting($block_id, 'sendmail', true);
		$block   =get_block_setting($block_id, 'block',    true);
		if ($cfg) {
			foreach (array('days', 'sendmail', 'block') as $name) {
				if (array_key_exists($name, $cfg)) {
					$$name=$cfg[$name];
				}
			}
		}

		if ($changes && $sendmail=='yes') {
			// There are pending changes - tell moderators/managers/administrators about them.
			if (WT_TIMESTAMP - WT_Site::getPreference('LAST_CHANGE_EMAIL') > (60*60*24*$days)) {
				// Which users have pending changes?
				foreach (User::all() as $user) {
					if ($user->getPreference('contactmethod') !== 'none') {
						foreach (WT_Tree::getAll() as $tree) {
							if (exists_pending_change($user, $tree)) {
								WT_I18N::init($user->getPreference('language'));
								WT_Mail::systemMessage(
									$tree,
									$user,
									WT_I18N::translate('Pending changes'),
									WT_I18N::translate('There are pending changes for you to moderate.') .
									WT_Mail::EOL . WT_MAIL::EOL .
									'<a href="' . WT_SERVER_NAME . WT_SCRIPT_PATH . 'index.php?ged=' . WT_GEDURL . '">' . WT_SERVER_NAME . WT_SCRIPT_PATH . 'index.php?ged=' . WT_GEDURL . '</a>'
								);
								WT_I18N::init(WT_LOCALE);
							}
						}
					}
				}
				WT_Site::setPreference('LAST_CHANGE_EMAIL', WT_TIMESTAMP);
			}
			if (WT_USER_CAN_EDIT) {
				$id=$this->getName().$block_id;
				$class=$this->getName().'_block';
				if ($ctype === 'gedcom' && WT_USER_GEDCOM_ADMIN || $ctype === 'user' && Auth::check()) {
					$title='<i class="icon-admin" title="'.WT_I18N::translate('Configure').'" onclick="modalDialog(\'block_edit.php?block_id='.$block_id.'\', \''.$this->getTitle().'\');"></i>';
				} else {
					$title='';
				}
				$title.=$this->getTitle().help_link('review_changes', $this->getName());

				$content = '';
				if (WT_USER_CAN_ACCEPT) {
					$content .= "<a href=\"#\" onclick=\"window.open('edit_changes.php','_blank', chan_window_specs); return false;\">".WT_I18N::translate('There are pending changes for you to moderate.')."</a><br>";
				}
				if ($sendmail=="yes") {
					$content .= WT_I18N::translate('Last email reminder was sent ').format_timestamp(WT_Site::getPreference('LAST_CHANGE_EMAIL'))."<br>";
					$content .= WT_I18N::translate('Next email reminder will be sent after ').format_timestamp(WT_Site::getPreference('LAST_CHANGE_EMAIL')+(60*60*24*$days))."<br><br>";
				}
				$changes=WT_DB::prepare(
					"SELECT xref".
					" FROM  `##change`".
					" WHERE status='pending'".
					" AND   gedcom_id=?".
					" GROUP BY xref"
				)->execute(array(WT_GED_ID))->fetchAll();
				foreach ($changes as $change) {
					$record=WT_GedcomRecord::getInstance($change->xref);
					if ($record->canShow()) {
						$content.='<b>'.$record->getFullName().'</b>';
						$content.=$block ? '<br>' : ' ';
						$content.='<a href="'.$record->getHtmlUrl().'">'.WT_I18N::translate('View the changes').'</a>';
						$content.='<br>';
					}
				}

				if ($template) {
					if ($block) {
						require WT_THEME_DIR.'templates/block_small_temp.php';
					} else {
						require WT_THEME_DIR.'templates/block_main_temp.php';
					}
				} else {
					return $content;
				}
			}
		}
	}

	// Implement class WT_Module_Block
	public function loadAjax() {
		return false;
	}

	// Implement class WT_Module_Block
	public function isUserBlock() {
		return true;
	}

	// Implement class WT_Module_Block
	public function isGedcomBlock() {
		return true;
	}

	// Implement class WT_Module_Block
	public function configureBlock($block_id) {
		if (WT_Filter::postBool('save') && WT_Filter::checkCsrf()) {
			set_block_setting($block_id, 'days',     WT_Filter::postInteger('num', 1, 180, 7));
			set_block_setting($block_id, 'sendmail', WT_Filter::postBool('sendmail'));
			set_block_setting($block_id, 'block',    WT_Filter::postBool('block'));
			exit;
		}

		require_once WT_ROOT.'includes/functions/functions_edit.php';

		$sendmail=get_block_setting($block_id, 'sendmail', true);
		$days=get_block_setting($block_id, 'days', 7);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo WT_I18N::translate('Send out reminder emails?');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('sendmail', $sendmail);
		echo '<br>';
		echo WT_I18N::translate('Reminder email frequency (days)')."&nbsp;<input type='text' name='days' value='".$days."' size='2'>";
		echo '</td></tr>';

		$block=get_block_setting($block_id, 'block', true);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo /* I18N: label for a yes/no option */ WT_I18N::translate('Add a scrollbar when block contents grow');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('block', $block);
		echo '</td></tr>';
	}
}
