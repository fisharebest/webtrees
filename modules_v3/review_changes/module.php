<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

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

		if ($changes) {
			//-- if the time difference from the last email is greater than 24 hours then send out another email
			$LAST_CHANGE_EMAIL=WT_Site::preference('LAST_CHANGE_EMAIL');
			if (WT_TIMESTAMP - $LAST_CHANGE_EMAIL > (60*60*24*$days)) {
				$LAST_CHANGE_EMAIL = WT_TIMESTAMP;
				WT_Site::preference('LAST_CHANGE_EMAIL', $LAST_CHANGE_EMAIL);
				if ($sendmail=="yes") {
					// Which users have pending changes?
					$users_with_changes=array();
					foreach (get_all_users() as $user_id=>$user_name) {
						foreach (WT_Tree::getAll() as $tree) {
							if (exists_pending_change($user_id, $tree->tree_id)) {
								$users_with_changes[$user_id]=$user_name;
								break;
							}
						}
					}
					foreach ($users_with_changes as $user_id=>$user_name) {
						//-- send message
						$message = array();
						$message["to"]=$user_name;
						$message["from"] = $WEBTREES_EMAIL;
						$message["subject"] = WT_I18N::translate('webtrees - Review changes');
						$message["body"] = WT_I18N::translate('Online changes have been made to a genealogical database.  These changes need to be reviewed and accepted before they will appear to all users.  Please use the URL below to enter that webtrees site and login to review the changes.');
						$message["method"] = get_user_setting($user_id, 'contactmethod');
						$message["url"] = WT_SERVER_NAME.WT_SCRIPT_PATH;
						$message["no_from"] = true;
						addMessage($message);
					}
				}
			}
			if (WT_USER_CAN_EDIT) {
				$id=$this->getName().$block_id;
				$class=$this->getName().'_block';
				if ($ctype=='gedcom' && WT_USER_GEDCOM_ADMIN || $ctype=='user' && WT_USER_ID) {
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
					$content .= WT_I18N::translate('Last email reminder was sent ').format_timestamp($LAST_CHANGE_EMAIL)."<br>";
					$content .= WT_I18N::translate('Next email reminder will be sent after ').format_timestamp($LAST_CHANGE_EMAIL+(60*60*24*$days))."<br><br>";
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
					if ($record->canDisplayDetails()) {
						$content.='<b>'.$record->getFullName().'</b>';
						switch ($record->getType()) {
						case 'INDI':
						case 'FAM':
						case 'SOUR':
						case 'OBJE':
							$content.=$block ? '<br>' : ' ';
							$content.='<a href="'.$record->getHtmlUrl().'">'.WT_I18N::translate('View the changes').'</a>';
							break;
						}
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
		if (safe_POST_bool('save')) {
			set_block_setting($block_id, 'days',     safe_POST_integer('num', 1, 180, 7));
			set_block_setting($block_id, 'sendmail', safe_POST_bool('sendmail'));
			set_block_setting($block_id, 'block',    safe_POST_bool('block'));
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
