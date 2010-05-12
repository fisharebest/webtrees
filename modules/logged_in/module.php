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

class logged_in_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return i18n::translate('Logged In Users');
	}

	// Extend class WT_Module
	public function getDescription() {
		return i18n::translate('The Logged In Users block shows a list of the users who are currently logged in.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id) {
		global $WT_SESSION_TIME, $TEXT_DIRECTION, $THEME_DIR;

		// Log out inactive users
		foreach (get_idle_users(time()-$WT_SESSION_TIME) as $user_id=>$user_name) {
			if ($user_id!=WT_USER_ID) {
				userLogout($user_id);
			}
		}

		// List active users
		$NumAnonymous = 0;
		$loggedusers = array ();
		foreach (get_logged_in_users() as $user_id=>$user_name) {
			if (WT_USER_IS_ADMIN || get_user_setting($user_id, 'visibleonline')=='Y') {
				$loggedusers[$user_id]=$user_name;
			} else {
				$NumAnonymous++;
			}
		}

		$id=$this->getName().$block_id;
		$title=i18n::translate('Users Logged In').help_link('index_loggedin');
		$content='<table width="90%">';
		$LoginUsers=count($loggedusers);
		if ($LoginUsers==0 && $NumAnonymous==0) {
			$content.='<tr><td><b>' . i18n::translate('No logged-in and no anonymous users') . '</b></td></tr>';
		}
		if ($NumAnonymous>0) {
			$content.='<tr><td><b>' . i18n::plural('%d anonymous logged-in user', '%d anonymous logged-in users', $NumAnonymous, $NumAnonymous) . '</b></td></tr>';
		}
		if ($LoginUsers>0) {
			$content.='<tr><td><b>' . i18n::plural('%d logged-in user', '%d logged-in users', $LoginUsers, $LoginUsers) . '</b></td></tr>';
		}
		if (WT_USER_ID) {
			foreach ($loggedusers as $user_id=>$user_name) {
				$content .= "<tr><td><br />".PrintReady(getUserFullName($user_id))." - ".$user_name;
				if (WT_USER_ID!=$user_id && get_user_setting($user_id, 'contactmethod')!="none") {
					$content .= "<br /><a href=\"javascript:;\" onclick=\"return message('" . $user_id . "');\">" . i18n::translate('Send Message') . "</a>";
				}
				$content .= "</td></tr>";
			}
		}
		$content .= "</table>";

		require $THEME_DIR.'templates/block_main_temp.php';
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
	}
}
