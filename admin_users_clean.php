<?php
/**
 * Administrative User Interface.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2011 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
 *
 * Modifications Copyright (c) 2010 Greg Roach
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
 * @package webtrees
 * @subpackage Admin
 * @version $Id: admin_users_clean 10239 2011-01-01 22:32:55Z greg $
*/

define('WT_SCRIPT_NAME', 'admin_users_clean.php');
require './includes/session.php';
require_once WT_ROOT.'includes/functions/functions_edit.php';

// Only admin users can access this page
if (!WT_USER_IS_ADMIN) {
	header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.'login.php?url='.WT_SCRIPT_NAME);
	exit;
}

print_header(WT_I18N::translate('Delete inactive users'));

// Valid values for form variables
$ALL_ACTIONS=array('cleanup', 'cleanup2', 'createform', 'createuser', 'deleteuser', 'edituser', 'edituser2', 'listusers');
$ALL_THEMES_DIRS=array();
foreach (get_theme_names() as $themename=>$themedir) {
	$ALL_THEME_DIRS[]=$themedir;
}
$ALL_EDIT_OPTIONS=array(
	'none'  => /* I18N: Listbox entry; name of a role */ WT_I18N::translate('Visitor'),
	'access'=> /* I18N: Listbox entry; name of a role */ WT_I18N::translate('Member'),
	'edit'  => /* I18N: Listbox entry; name of a role */ WT_I18N::translate('Editor'),
	'accept'=> /* I18N: Listbox entry; name of a role */ WT_I18N::translate('Moderator'),
	'admin' => /* I18N: Listbox entry; name of a role */ WT_I18N::translate('Manager')
);

// Extract form actions (GET overrides POST if both set)
$action                  =safe_POST('action',  $ALL_ACTIONS);
$usrlang                 =safe_POST('usrlang', array_keys(WT_I18N::installed_languages()));
$username                =safe_POST('username', WT_REGEX_USERNAME);
$filter                  =safe_POST('filter'   );
$ged                     =safe_POST('ged'      );

$action                  =safe_GET('action',   $ALL_ACTIONS,                            $action);
$usrlang                 =safe_GET('usrlang',  array_keys(WT_I18N::installed_languages()), $usrlang);
$username                =safe_GET('username', WT_REGEX_USERNAME,                      $username);
$filter                  =safe_GET('filter',   WT_REGEX_NOSCRIPT,                      $filter);
$ged                     =safe_GET('ged',      WT_REGEX_NOSCRIPT,                      $ged);

// Extract form variables
$oldusername             =safe_POST('oldusername',     WT_REGEX_USERNAME);
$oldemailaddress         =safe_POST('oldemailaddress', WT_REGEX_EMAIL);
$realname                =safe_POST('realname'   );
$pass1                   =safe_POST('pass1',        WT_REGEX_PASSWORD);
$pass2                   =safe_POST('pass2',        WT_REGEX_PASSWORD);
$emailaddress            =safe_POST('emailaddress', WT_REGEX_EMAIL);
$user_theme              =safe_POST('user_theme',               $ALL_THEME_DIRS);
$user_language           =safe_POST('user_language',            array_keys(WT_I18N::installed_languages()), WT_LOCALE);
$new_contact_method      =safe_POST('new_contact_method');
$new_default_tab         =safe_POST('new_default_tab',          array_keys(WT_Module::getActiveTabs()), get_gedcom_setting(WT_GED_ID, 'GEDCOM_DEFAULT_TAB'));
$new_comment             =safe_POST('new_comment',              WT_REGEX_UNSAFE);
$new_comment_exp         =safe_POST('new_comment_exp'           );
$new_auto_accept         =safe_POST_bool('new_auto_accept');
$canadmin                =safe_POST_bool('canadmin');
$visibleonline           =safe_POST_bool('visibleonline');
$editaccount             =safe_POST_bool('editaccount');
$verified                =safe_POST_bool('verified');
$verified_by_admin       =safe_POST_bool('verified_by_admin');

if (empty($ged)) {
	$ged=$GEDCOM;
}

// Load all available gedcoms
$all_gedcoms = get_all_gedcoms();
//-- sorting by gedcom filename
asort($all_gedcoms);

// Delete a user
if ($action=='deleteuser') {
	// don't delete ourselves
	$user_id=get_user_id($username);
	if ($user_id!=WT_USER_ID) {
		delete_user($user_id);
		AddToLog("deleted user ->{$username}<-", 'auth');
	}
	// User data is cached, so reload the page to ensure we're up to date
	header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.WT_SCRIPT_NAME);
	exit;
}

// Cleanup users and user rights
//NOTE: WORKING
if ($action == "cleanup") {
	?>
	<form name="cleanupform" method="post" action="admin_users_clean.php">
	<input type="hidden" name="action" value="cleanup2" />
	<table id="clean" class="<?php echo $TEXT_DIRECTION; ?>">
	<?php
	// Check for idle users
	//if (!isset($month)) $month = 1;
	$month = safe_GET_integer('month', 1, 12, 6);
	echo "<tr><th>", WT_I18N::translate('Number of months since the last login for a user\'s account to be considered inactive: '), "</th>";
	echo "<td><select onchange=\"document.location=options[selectedIndex].value;\">";
	for ($i=1; $i<=12; $i++) {
		echo "<option value=\"admin_users_clean.php?action=cleanup&amp;month=$i\"";
		if ($i == $month) echo " selected=\"selected\"";
		echo " >", $i, "</option>";
	}
	echo "</select></td></tr>";
	?>
	<tr><th colspan="2"><?php echo WT_I18N::translate('Options:'); ?></th></tr>
	<?php
	// Check users not logged in too long
	$ucnt = 0;
	foreach (get_all_users() as $user_id=>$user_name) {
		$userName = getUserFullName($user_id);
		if ((int)get_user_setting($user_id, 'sessiontime') == "0")
			$datelogin = (int)get_user_setting($user_id, 'reg_timestamp');
		else
			$datelogin = (int)get_user_setting($user_id, 'sessiontime');
		if ((mktime(0, 0, 0, (int)date("m")-$month, (int)date("d"), (int)date("Y")) > $datelogin) && get_user_setting($user_id, 'verified') && get_user_setting($user_id, 'verified_by_admin')) {
			?><tr><td><?php echo $user_name, " - <b>", $userName, "</b><br />", WT_I18N::translate('User\'s account has been inactive too long: ');
			echo timestamp_to_gedcom_date($datelogin)->Display(false);
			$ucnt++;
			?></td><td><input type="checkbox" name="<?php echo "del_", str_replace(array(".", "-", " "), array("_", "_", "_"), $user_name); ?>" value="1" /></td></tr><?php
		}
	}

	// Check unverified users
	foreach (get_all_users() as $user_id=>$user_name) {
		if (((date("U") - (int)get_user_setting($user_id, 'reg_timestamp')) > 604800) && !get_user_setting($user_id, 'verified')) {
			$userName = getUserFullName($user_id);
			?><tr><td><?php echo $user_name, " - ", $userName, ":&nbsp;&nbsp;", WT_I18N::translate('User didn\'t verify within 7 days.');
			$ucnt++;
			?></td><td><input type="checkbox" checked="checked" name="<?php echo "del_", str_replace(array(".", "-", " "), array("_",  "_", "_"), $user_name); ?>" value="1" /></td></tr><?php
		}
	}

	// Check users not verified by admin
	foreach (get_all_users() as $user_id=>$user_name) {
		if (!get_user_setting($user_id, 'verified_by_admin') && get_user_setting($user_id, 'verified')) {
			$userName = getUserFullName($user_id);
			?><tr><td><?php echo $user_name, " - ", $userName, ":&nbsp;&nbsp;", WT_I18N::translate('User not verified by administrator.');
			?></td><td><input type="checkbox" name="<?php echo "del_", str_replace(array(".", "-", " "), array("_", "_", "_"), $user_name); ?>" value="1" /></td></tr><?php
			$ucnt++;
		}
	}
	if ($ucnt == 0) {
		echo "<tr><td class=\"accepted\" colspan=\"2\">";
		echo WT_I18N::translate('Nothing found to cleanup'), "</td></tr>";
	} ?>
	</table>
	<p>
	<?php
	if ($ucnt >0) {
		?><input type="submit" value="<?php echo WT_I18N::translate('Continue'); ?>" />&nbsp;&nbsp;<?php
	} ?>
	<input type="button" value="<?php echo WT_I18N::translate('Back'); ?>" onclick="window.location='admin_users_clean.php';"/>
	</p>
	</form><?php
	print_footer();
	exit;
}
// NOTE: No table parts
if ($action == "cleanup2") {
	foreach (get_all_users() as $user_id=>$user_name) {
		$var = "del_".str_replace(array(".", "-", " "), array("_", "_", "_"), $user_name);
		if (safe_POST($var)=='1') {
			delete_user($user_id);
			AddToLog("deleted user ->{$user_name}<-", 'auth');
			echo WT_I18N::translate('Deleted user: '); echo $user_name, "<br />";
		} else {
			$tempArray = unserialize(get_user_setting($user_id, 'canedit'));
			if (is_array($tempArray)) {
				foreach ($tempArray as $gedid=>$data) {
					$var = "delg_".str_replace(array(".", "-", " "), "_", $gedid);
					if (safe_POST($var)=='1' && get_user_gedcom_setting($user_id, $gedid, 'canedit')) {
						set_user_gedcom_setting($user_id, $gedid, 'canedit', null);
						echo $gedid, ":&nbsp;&nbsp;", WT_I18N::translate('Unset GEDCOM rights for '), $user_name, "<br />";
					}
				}
			}
			$tempArray = unserialize(get_user_setting($user_id, 'rootid'));
			if (is_array($tempArray)) {
				foreach ($tempArray as $gedid=>$data) {
					$var = "delg_".str_replace(array(".", "-", " "), "_", $gedid);
					if (safe_POST($var)=='1' && get_user_gedcom_setting($user_id, $gedid, 'rootid')) {
						set_user_gedcom_setting($user_id, $gedid, 'rootid', null);
						echo $gedid, ":&nbsp;&nbsp;", WT_I18N::translate('Unset root ID for '), $user_name, "<br />";
					}
				}
			}
			$tempArray = unserialize(get_user_setting($user_id, 'gedcomid'));
			if (is_array($tempArray)) {
				foreach ($tempArray as $gedid=>$data) {
					$var = "delg_".str_replace(array(".", "-", " "), "_", $gedid);
					if (safe_POST($var)=='1' && get_user_gedcom_setting($user_id, $gedid, 'gedcomid')) {
						set_user_gedcom_setting($user_id, $gedid, 'gedcomid', null);
						echo $gedid, ":&nbsp;&nbsp;", WT_I18N::translate('Unset GEDCOM ID for '), $user_name, "<br />";
					}
				}
			}
		}
	}
}
print_footer();
