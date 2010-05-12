<?php
/**
 * Classes and libraries for module system
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2010 John Finlay
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @version $Id: class_media.php 5451 2009-05-05 22:15:34Z fisharebest $
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require_once WT_ROOT.'includes/classes/class_module.php';

class review_changes_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return i18n::translate('Pending Changes');
	}

	// Extend class WT_Module
	public function getDescription() {
		return i18n::translate('The Pending Changes block will give users with Edit rights a list of the records that have been changed online and that still need to be reviewed and accepted.  These changes are pending acceptance or rejection.<br /><br />If this block is enabled, users with Accept rights will receive an email once a day notifying them that changes need to be reviewed.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id) {
		global $ctype, $QUERY_STRING, $WT_IMAGE_DIR, $WT_IMAGES, $TEXT_DIRECTION, $SHOW_SOURCES, $WEBTREES_EMAIL, $TBLPREFIX;

		$changes=WT_DB::prepare(
			"SELECT 1".
			" FROM {$TBLPREFIX}change".
			" WHERE status='pending'".
			" LIMIT 1"
		)->fetchOne();

		$days    =get_block_setting($block_id, 'days',     1);
		$sendmail=get_block_setting($block_id, 'sendmail', true);
		$block   =get_block_setting($block_id, 'block',    true);

		if ($changes) {
			//-- if the time difference from the last email is greater than 24 hours then send out another email
			$LAST_CHANGE_EMAIL=get_site_setting('LAST_CHANGE_EMAIL');
			if (time()-$LAST_CHANGE_EMAIL > (60*60*24*$days)) {
				$LAST_CHANGE_EMAIL = time();
				set_site_setting('LAST_CHANGE_EMAIL', $LAST_CHANGE_EMAIL);
				if ($sendmail=="yes") {
					// Which users have pending changes?
					$users_with_changes=array();
					foreach (get_all_users() as $user_id=>$user_name) {
						foreach (get_all_gedcoms() as $ged_id=>$ged_name) {
							if (exists_pending_change($user_id, $ged_id)) {
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
						$message["subject"] = i18n::translate('webtrees - Review changes');
						$message["body"] = i18n::translate('Online changes have been made to a genealogical database.  These changes need to be reviewed and accepted before they will appear to all users.  Please use the URL below to enter that webtrees site and login to review the changes.');
						$message["method"] = get_user_setting($user_id, 'contactmethod');
						$message["url"] = WT_SERVER_NAME.WT_SCRIPT_PATH;
						$message["no_from"] = true;
						addMessage($message);
					}
				}
			}
			if (WT_USER_CAN_EDIT) {
				$id=$this->getName().$block_id;
				$title='';
				if ($ctype=="gedcom" && WT_USER_GEDCOM_ADMIN || $ctype=="user" && WT_USER_ID) {
					if ($ctype=="gedcom") {
						$name = WT_GEDCOM;
					} else {
						$name = WT_USER_NAME;
					}
					$title .= "<a href=\"javascript: configure block\" onclick=\"window.open('index_edit.php?action=configure&block_id={$block_id}', '_blank', 'top=50,left=50,width=600,height=350,scrollbars=1,resizable=1'); return false;\">";
					$title .= "<img class=\"adminicon\" src=\"$WT_IMAGE_DIR/".$WT_IMAGES["admin"]["small"]."\" width=\"15\" height=\"15\" border=\"0\" alt=\"".i18n::translate('Configure')."\" /></a>";
				}
				$title.=i18n::translate('Review GEDCOM Changes').help_link('review_changes');
				$content = "";
				if (WT_USER_CAN_ACCEPT) {
					$content .= "<a href=\"javascript:;\" onclick=\"window.open('edit_changes.php','_blank','width=600,height=500,resizable=1,scrollbars=1'); return false;\">".i18n::translate('Accept / Reject Changes')."</a><br />";
				}
				if ($sendmail=="yes") {
					$content .= i18n::translate('Last email reminder was sent ').format_timestamp($LAST_CHANGE_EMAIL)."<br />";
					$content .= i18n::translate('Next email reminder will be sent after ').format_timestamp($LAST_CHANGE_EMAIL+(60*60*24*$days))."<br /><br />";
				}
				$changes=WT_DB::prepare(
					"SELECT xref".
					" FROM  {$TBLPREFIX}change".
					" WHERE status='pending'".
					" AND   gedcom_id=?".
					" GROUP BY xref"
				)->execute(array(WT_GED_ID))->fetchAll();
				foreach ($changes as $change) {
					$record=GedcomRecord::getInstance($change->xref);
					if ($record->getType()!='SOUR' || $SHOW_SOURCES>=WT_USER_ACCESS_LEVEL) {
						$content.='<b>'.PrintReady($record->getFullName()).'</b> '.getLRM().'('.$record->getXref().')'.getLRM();
						switch ($record->getType()) {
						case 'INDI':
						case 'FAM':
						case 'SOUR':
						case 'OBJE':
							$content.=$block ? '<br />' : ' ';
							$content.='<a href="'.encode_url($record->getLinkUrl().'&show_changes=yes').'">'.i18n::translate('View Change Diff').'</a>';
							break;
						}
						$content.='<br />';
					}
				}

				global $THEME_DIR;
				if ($block) {
					require $THEME_DIR.'templates/block_small_temp.php';
				} else {
					require $THEME_DIR.'templates/block_main_temp.php';
				}
			}
		}
	}

	// Implement class WT_Module_Block
	public function canLoadAjax() {
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
		global $WT_BLOCKS;
		if (empty($config)) $config = $WT_BLOCKS["review_changes_block"]["config"];
		print i18n::translate('Send out reminder emails?');
		print "&nbsp;<select name='sendmail'>";
		print "<option value='yes'";
		if ($config["sendmail"]=="yes") print " selected='selected'";
		print ">".i18n::translate('Yes')."</option>";
		print "<option value='no'";
		if ($config["sendmail"]=="no") print " selected='selected'";
		print ">".i18n::translate('No')."</option>";
		print "</select><br /><br />";
		print i18n::translate('Reminder email frequency (days)')."&nbsp;<input type='text' name='days' value='".$config["days"]."' size='2' />";
		// Cache file life
		if ($ctype=="gedcom") {
			echo "<tr><td class=\"descriptionbox wrap width33\">";
			echo i18n::translate('Cache file life'), help_link('cache_life');
			echo "</td><td class=\"optionbox\">";
			echo "<input type=\"text\" name=\"cache\" size=\"2\" value=\"".$config["cache"]."\" />";
			echo "</td></tr>";
		}
		// Cache file life is not configurable by user:  anything other than "no cache" doesn't make sense
		print "<input type=\"hidden\" name=\"cache\" value=\"0\" />";
	}
}
