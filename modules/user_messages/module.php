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

class user_messages_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return i18n::translate('User Messages');
	}

	// Extend class WT_Module
	public function getDescription() {
		return i18n::translate('The User Messages block shows a list of the messages that have been sent to the active user.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id) {
		global $ctype, $WT_IMAGE_DIR, $TEXT_DIRECTION, $WT_IMAGES, $THEME_DIR;

		require_once WT_ROOT.'includes/functions/functions_print_facts.php';

		// Block actions
		$action=safe_GET('action');
		$message_id=safe_GET('message_id');
		if ($action=='deletemessage') {
			if (is_array($message_id)) {
				foreach ($message_id as $msg_id) {
					deleteMessage($msg_id);
				}
			} else {
				deleteMessage($message_id);
			}
		}

		$usermessages = getUserMessages(WT_USER_NAME);

		$id=$this->getName().$block_id;
		$title=i18n::translate('My Messages').help_link('mygedview_message');
		if ($TEXT_DIRECTION=="rtl") $title .= getRLM();
		$title .= "(".count($usermessages).")";
		if ($TEXT_DIRECTION=="rtl") $title .= getRLM();

		$content = "";
		$content .= "<form name=\"messageform\" action=\"index.php?ctype={$ctype}\" method=\"get\" onsubmit=\"return confirm('".i18n::translate('Are you sure you want to delete this message?  It cannot be retrieved later.')."');\">";
		if (count($usermessages)==0) {
			$content .= i18n::translate('You have no pending messages.')."<br />";
		} else {
			$content .= '
				<script language="JavaScript" type="text/javascript">
				<!--
					function select_all() {
						';
			foreach($usermessages as $key=>$message) {
				if (isset($message["id"])) $key = $message["id"];
				$content .= '
							var cb = document.getElementById("cb_message'.$key.'");
							if (cb) {
								if (!cb.checked) cb.checked = true;
								else cb.checked = false;
							}
							';
			}
			$content .= '
					return false;
				}
				//-->
				</script>
			';
			$content .= "<input type=\"hidden\" name=\"action\" value=\"deletemessage\" />";
			$content .= "<table class=\"list_table\"><tr>";
			$content .= "<td class=\"list_label\">".i18n::translate('Delete')."<br /><a href=\"javascript:;\" onclick=\"return select_all();\">".i18n::translate('All')."</a></td>";
			$content .= "<td class=\"list_label\">".i18n::translate('Subject:')."</td>";
			$content .= "<td class=\"list_label\">".i18n::translate('Date Sent:')."</td>";
			$content .= "<td class=\"list_label\">".i18n::translate('Email Address:')."</td>";
			$content .= "</tr>";
			foreach($usermessages as $key=>$message) {
				if (isset($message["id"])) $key = $message["id"];
				$content .= "<tr>";
				$content .= "<td class=\"list_value_wrap\"><input type=\"checkbox\" id=\"cb_message$key\" name=\"message_id[]\" value=\"$key\" /></td>";
				$showmsg=preg_replace("/(\w)\/(\w)/","\$1/<span style=\"font-size:1px;\"> </span>\$2",PrintReady($message["subject"]));
				$showmsg=str_replace("@","@<span style=\"font-size:1px;\"> </span>",$showmsg);
				$content .= "<td class=\"list_value_wrap\"><a href=\"javascript:;\" onclick=\"expand_layer('message{$key}'); return false;\"><img id=\"message{$key}_img\" src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["plus"]["other"]."\" border=\"0\" alt=\"".i18n::translate('Show Details')."\" title=\"".i18n::translate('Show Details')."\" /> <b>".$showmsg."</b></a></td>";
				if (!empty($message["created"])) {
					$time = strtotime($message["created"]);
				} else {
					$time = time();
				}
				$content .= "<td class=\"list_value_wrap\">".format_timestamp($time)."</td>";
				$content .= "<td class=\"list_value_wrap\">";
				$user_id=get_user_id($message["from"]);
				if ($user_id) {
					$content .= PrintReady(getUserFullName($user_id));
					if ($TEXT_DIRECTION=="ltr") {
						$content .= " " . getLRM() . " - ".htmlspecialchars($user_id,ENT_COMPAT,'UTF-8') . getLRM();
					} else {
						$content .= " " . getRLM() . " - ".htmlspecialchars($user_id,ENT_COMPAT,'UTF-8') . getRLM();
					}
				} else {
					$content .= "<a href=\"mailto:".$message["from"]."\">".str_replace("@","@<span style=\"font-size:1px;\"> </span>",$message["from"])."</a>";
				}
				$content .= "</td>";
				$content .= "</tr>";
				$content .= "<tr><td class=\"list_value_wrap\" colspan=\"5\"><div id=\"message$key\" style=\"display: none;\">";
				$message["body"] = nl2br(htmlspecialchars($message["body"],ENT_COMPAT,'UTF-8'));
				$message["body"] = expand_urls($message["body"]);

				$content .= PrintReady($message["body"])."<br /><br />";
				if (strpos($message["subject"], "RE:")===false) {
					$message["subject"]="RE:".$message["subject"];
				}
				if ($user_id) {
					$content .= "<a href=\"javascript:;\" onclick=\"reply('".$user_id."', '".$message["subject"]."'); return false;\">".i18n::translate('Reply')."</a> | ";
				}
				$content .= "<a href=\"".encode_url("index.php?action=deletemessage&message_id={$key}")."\" onclick=\"return confirm('".i18n::translate('Are you sure you want to delete this message?  It cannot be retrieved later.')."');\">".i18n::translate('Delete')."</a></div></td></tr>";
			}
			$content .= "</table>";
			$content .= "<input type=\"submit\" value=\"".i18n::translate('Delete Selected Messages')."\" /><br /><br />";
		}
		if (get_user_count()>1) {
			$content .= i18n::translate('Send Message')." <select name=\"touser\">";
			if (WT_USER_IS_ADMIN) {
				$content .= "<option value=\"all\">".i18n::translate('Broadcast to all users')."</option>";
				$content .= "<option value=\"never_logged\">".i18n::translate('Send message to users who have never logged in')."</option>";
				$content .= "<option value=\"last_6mo\">".i18n::translate('Send message to users who have not logged in for 6 months')."</option>";
			}
			foreach (get_all_users() as $user_id=>$user_name) {
				if ($user_id!=WT_USER_ID && get_user_setting($user_id, 'verified_by_admin')=='yes' && get_user_setting($user_id, 'contactmethod')!='none') {
					$content .= "<option value=\"".$user_name."\">".PrintReady(getUserFullName($user_id))." ";
					if ($TEXT_DIRECTION=="ltr") {
						$content .= stripLRMRLM(getLRM()." - ".$user_name.getLRM());
					} else {
						$content .= stripLRMRLM(getRLM()." - ".$user_name.getRLM());
					}
					$content .= "</option>";
				}
			}
			$content .= "</select><input type=\"button\" value=\"".i18n::translate('Send')."\" onclick=\"message(document.messageform.touser.options[document.messageform.touser.selectedIndex].value, 'messaging2', ''); return false;\" />";
		}
		$content .= "</form>";

		$block=get_block_setting($block_id, 'block', true);
		if ($block) {
			require $THEME_DIR.'templates/block_small_temp.php';
		} else {
			require $THEME_DIR.'templates/block_main_temp.php';
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
		return false;
	}

	// Implement class WT_Module_Block
	public function configureBlock($block_id) {
	}
}
