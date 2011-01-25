<?php
// Administrative User Interface.
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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

define('WT_SCRIPT_NAME', 'admin_users_add.php');

require './includes/session.php';
require WT_ROOT.'includes/functions/functions_edit.php';

// Only admin users can access this page
if (!WT_USER_IS_ADMIN) {
	header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.'login.php?url='.WT_SCRIPT_NAME);
	exit;
}

print_header(WT_I18N::translate('Add a new user'));

if ($ENABLE_AUTOCOMPLETE) require WT_ROOT.'js/autocomplete.js.htm';

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

// Save new user info to the database
if ($action=='createuser') {
	if (($action=='createuser' && $username!=$oldusername) && get_user_id($username)) {
		print_header(WT_I18N::translate('Add a new user'));
		echo "<span class=\"error\">", WT_I18N::translate('Duplicate user name.  A user with that user name already exists.  Please choose another user name.'), "</span><br />";
	} elseif (($action=='createuser' || $action=='edituser2' && $emailaddress!=$oldemailaddress) && get_user_by_email($emailaddress)) {
		print_header(WT_I18N::translate('Add a new user'));
		echo "<span class=\"error\">", WT_I18N::translate('Duplicate email address.  A user with that email already exists.'), "</span><br />";
	} else {
		if ($pass1!=$pass2) {
			print_header(WT_I18N::translate('Add a new user'));
			echo "<span class=\"error\">", WT_I18N::translate('Passwords do not match.'), "</span><br />";
		} else {
			// New user
			if ($action=='createuser') {
				if ($user_id=create_user($username, $realname, $emailaddress, crypt($pass1))) {
					set_user_setting($user_id, 'reg_timestamp', date('U'));
					set_user_setting($user_id, 'sessiontime', '0');
					AddToLog("User ->{$username}<- created", 'auth');
				} else {
					AddToLog("User ->{$username}<- was not created", 'auth');
					$user_id=get_user_id($username);
				}
			} else {
				$user_id=get_user_id($oldusername);
			}
			// Change password
			if ($action=='edituser2' && !empty($pass1)) {
				set_user_password($user_id, crypt($pass1));
				AddToLog("User ->{$oldusername}<- had password changed", 'auth');
			}
			// Change username
			if ($action=='edituser2' && $username!=$oldusername) {
				rename_user($oldusername, $username);
				AddToLog("User ->{$oldusername}<- renamed to ->{$username}<-", 'auth');
			}
				// Create/change settings that can be updated in the user's gedcom record?
			$email_changed=($emailaddress!=getUserEmail($user_id));
			$newly_verified=($verified_by_admin && !get_user_setting($user_id, 'verified_by_admin'));
			// Create/change other settings
			setUserFullName ($user_id, $realname);
			setUserEmail    ($user_id, $emailaddress);
			set_user_setting($user_id, 'theme',                $user_theme);
			set_user_setting($user_id, 'language',             $user_language);
			set_user_setting($user_id, 'contactmethod',        $new_contact_method);
			set_user_setting($user_id, 'defaulttab',           $new_default_tab);
			set_user_setting($user_id, 'comment',              $new_comment);
			set_user_setting($user_id, 'comment_exp',          $new_comment_exp);
			set_user_setting($user_id, 'auto_accept',          $new_auto_accept);
			set_user_setting($user_id, 'canadmin',             $canadmin);
			set_user_setting($user_id, 'visibleonline',        $visibleonline);
			set_user_setting($user_id, 'editaccount',          $editaccount);
			set_user_setting($user_id, 'verified',             $verified);
			set_user_setting($user_id, 'verified_by_admin',    $verified_by_admin);
			foreach ($all_gedcoms as $ged_id=>$ged_name) {
				set_user_gedcom_setting($user_id, $ged_id, 'gedcomid', safe_POST_xref('gedcomid'.$ged_id));
				set_user_gedcom_setting($user_id, $ged_id, 'rootid',   safe_POST_xref('rootid'.$ged_id));
				set_user_gedcom_setting($user_id, $ged_id, 'canedit',  safe_POST('canedit'.$ged_id, array_keys($ALL_EDIT_OPTIONS)));
				if (safe_POST_xref('gedcomid'.$ged_id)) {
					set_user_gedcom_setting($user_id, $ged_id, 'RELATIONSHIP_PATH_LENGTH', safe_POST_integer('RELATIONSHIP_PATH_LENGTH'.$ged_id, 0, 10, 0));
				} else {
					// Do not allow a path length to be set if the individual ID is not
					set_user_gedcom_setting($user_id, $ged_id, 'RELATIONSHIP_PATH_LENGTH', null);
				}
			}

			// If we're verifying a new user, send them a message to let them know
			if ($newly_verified && $action=='edituser2') {
				WT_I18N::init($user_language);
				$message=array();
				$message["to"]=$username;
				$headers="From: ".$WEBTREES_EMAIL;
				$message["from"]=WT_USER_NAME;
				$message["subject"]=WT_I18N::translate('Approval of account at %s', WT_SERVER_NAME.WT_SCRIPT_PATH);
				$message["body"]=WT_I18N::translate('The administrator at the webtrees site %s has approved your application for an account.  You may now login by accessing the following link: %s', WT_SERVER_NAME.WT_SCRIPT_PATH, WT_SERVER_NAME.WT_SCRIPT_PATH);
				$message["created"]="";
				$message["method"]="messaging2";
				addMessage($message);
				// and send a copy to the admin
				/*
				$message=array();
				$message["to"]=WT_USER_NAME;
				$headers="From: ".$WEBTREES_EMAIL;
				$message["from"]=$username; // fake the from address - so the admin can "reply" to it.
				$message["subject"]=WT_I18N::translate('Approval of account at %s', WT_SERVER_NAME.WT_SCRIPT_PATH));
				$message["body"]=WT_I18N::translate('The administrator at the webtrees site %s has approved your application for an account.  You may now login by accessing the following link: %s', WT_SERVER_NAME.WT_SCRIPT_PATH, WT_SERVER_NAME.WT_SCRIPT_PATH));
				$message["created"]="";
				$message["method"]="messaging2";
				addMessage($message); */
			}
			// Reload the form cleanly, to allow the user to verify their changes
			header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH."useradmin.php?action=edituser&username=".rawurlencode($username)."&ged=".rawurlencode($ged));
			exit;
		}
	}
}

// -- echo out the form to add a new user
// NOTE: WORKING
if ($action == "createform") {
	init_calendar_popup();
	?>
	<script type="text/javascript">
	<!--
		function checkform(frm) {
			if (frm.username.value=="") {
				alert("<?php echo WT_I18N::translate('You must enter a user name.'); ?>");
				frm.username.focus();
				return false;
			}
			if (frm.realname.value=="") {
				alert("<?php echo WT_I18N::translate('You must enter a real name.'); ?>");
				frm.realname.focus();
				return false;
			}
			if (frm.pass1.value=="") {
				alert("<?php echo WT_I18N::translate('You must enter a password.'); ?>");
				frm.pass1.focus();
				return false;
			}
			if (frm.pass2.value=="") {
				alert("<?php echo WT_I18N::translate('You must confirm the password.'); ?>");
				frm.pass2.focus();
				return false;
			}
			if (frm.pass1.value.length < 6) {
				alert("<?php echo WT_I18N::translate('Passwords must contain at least 6 characters.'); ?>");
				frm.pass1.value = "";
				frm.pass2.value = "";
				frm.pass1.focus();
				return false;
			}
			if (frm.emailaddress.value.indexOf("@")==-1) {
				alert("<?php echo WT_I18N::translate('You must enter an email address.'); ?>");
				frm.emailaddress.focus();
				return false;
			}
			return true;
		}
		var pastefield;
		function paste_id(value) {
			pastefield.value=value;
		}
		jQuery(document).ready(function() {
			jQuery('.relpath').change(function() {
				var fieldIDx = jQuery(this).attr('id');
				var idNum = fieldIDx.replace('RELATIONSHIP_PATH_LENGTH','');
				var newIDx = "gedcomid"+idNum;
				if (jQuery('#'+newIDx).val()=='') {
					alert("<?php echo WT_I18N::translate('You must specify an individual record before you can restrict the user to their immediate family.'); ?>");
					jQuery(this).val('');
				}
			});
		});
		
	//-->
	</script>

	<form name="newform" method="post" action="useradmin.php" onsubmit="return checkform(this);" autocomplete="off">
		<input type="hidden" name="action" value="createuser" />
		<!--table-->
		<table id="adduser" class="<?php echo $TEXT_DIRECTION; ?>">
			<tr>
				<td><?php echo WT_I18N::translate('User name'), help_link('useradmin_username'); ?></td>
				<td colspan="3" ><input type="text" name="username" autofocus /></td>
			</tr>
			<tr>
				<td><?php echo WT_I18N::translate('Real name'), help_link('useradmin_realname'); ?></td>
				<td colspan="3" ><input type="text" name="realname" size="50" /></td>
			</tr>
			<tr>
				<td><?php echo WT_I18N::translate('Password'), help_link('useradmin_password'); ?></td>
				<td ><input type="password" name="pass1" /></td>
				<td><?php echo WT_I18N::translate('Confirm password'), help_link('useradmin_conf_password'); ?></td>
				<td ><input type="password" name="pass2" /></td>
			</tr>
			<tr>
			<tr>
				<td><?php echo WT_I18N::translate('Email address'), help_link('useradmin_email'); ?></td>
				<td ><input type="text" name="emailaddress" value="" size="50" /></td>
				<td><?php echo WT_I18N::translate('Preferred contact method'), help_link('useradmin_user_contact'); ?></td>
				<td >
					<?php
						echo edit_field_contact('new_contact_method');
					?>
				</td>
			</tr>
			<tr>
				<td><?php echo WT_I18N::translate('Email verified'), help_link('useradmin_verification'); ?></td>
				<td ><input type="checkbox" name="verified" value="1" checked="checked" /></td>
				<td><?php echo WT_I18N::translate('Approved by administrator'), help_link('useradmin_verification'); ?></td>
				<td ><input type="checkbox" name="verified_by_admin" value="1" checked="checked" /></td>
			</tr>
			<tr>
				<td><?php echo WT_I18N::translate('Automatically approve changes made by this user'), help_link('useradmin_auto_accept'); ?></td>
				<td ><input type="checkbox" name="new_auto_accept" value="1" /></td>
				<td><?php echo WT_I18N::translate('Allow this user to edit his account information'), help_link('useradmin_editaccount'); ?></td>
				<td ><input type="checkbox" name="editaccount" value="1" <?php echo "checked=\"checked\""; ?> /></td>
			</tr>
			<tr>
				<td><?php echo WT_I18N::translate('Administrator'), help_link('role'); ?></td>
				<td ><input type="checkbox" name="canadmin" value="1" /></td>
				<td><?php echo WT_I18N::translate('Visible to other users when online'), help_link('useradmin_visibleonline'); ?></td>
				<td ><input type="checkbox" name="visibleonline" value="1" <?php echo "checked=\"checked\""; ?> /></td>
			</tr>
			<?php if (WT_USER_IS_ADMIN) { ?>
			<tr>
				<td><?php echo WT_I18N::translate('Admin comments on user'), help_link('useradmin_comment'); ?></td>
				<td ><textarea cols="38" rows="5" name="new_comment"></textarea></td>
				<td><?php echo WT_I18N::translate('Date'), help_link('useradmin_comment_exp'); ?></td>
				<td ><input type="text" name="new_comment_exp" id="new_comment_exp" />&nbsp;&nbsp;<?php print_calendar_popup("new_comment_exp"); ?></td>
			</tr>
			<?php } ?>
			<tr>
				<td><?php echo WT_I18N::translate('Language'), help_link('useradmin_change_lang'); ?></td>
				<td colspan="3"  ><?php echo edit_field_language('user_language', get_user_setting(WT_USER_ID, 'language')); ?></td>
			</tr>
			<?php if (get_site_setting('ALLOW_USER_THEMES')) { ?>
				<tr>
					<td><?php echo WT_I18N::translate('Theme'), help_link('THEME'); ?></td>
					<td colspan="3">
						<select name="new_user_theme">
						<option value="" selected="selected"><?php echo WT_I18N::translate('Site Default'); ?></option>
						<?php
							foreach (get_theme_names() as $themename=>$themedir) {
								echo "<option value=\"", $themedir, "\"";
								echo ">", $themename, "</option>";
							}
						?>
						</select>
					</td>
				</tr>
			<?php } ?>
			<tr>
				<td><?php echo WT_I18N::translate('Default Tab to show on Individual Information page'), help_link('useradmin_user_default_tab'); ?></td>
				<td colspan="3">
					<?php echo edit_field_default_tab('new_default_tab', get_gedcom_setting(WT_GED_ID, 'GEDCOM_DEFAULT_TAB')); ?>
				</td>
			</tr>
			<!-- access and relationship path details -->
			<tr>
				<th colspan="4"><?php print WT_I18N::translate('Family tree access and settings'); ?></th>
			</tr>
			<tr>
				<td colspan="4">
					<table id="adduser2">
						<tr>
							<th><?php echo WT_I18N::translate('Family tree'); ?></th>
							<th><?php echo WT_I18N::translate('Pedigree chart root person'), help_link('useradmin_rootid'); ?></th>
							<th><?php echo WT_I18N::translate('Individual record'), help_link('useradmin_gedcomid'); ?></th>
							<th><?php echo WT_I18N::translate('Role'), help_link('role'); ?></th>
							<th><?php echo WT_I18N::translate('Restrict to immediate family'), help_link('RELATIONSHIP_PATH_LENGTH'); ?></th>
						</tr>
						<?php
							foreach ($all_gedcoms as $ged_id=>$ged_name) {
								echo '<tr>',
									'<td >', WT_I18N::translate('%s', get_gedcom_setting($ged_id, 'title')), '</td>',
									//Pedigree root person
									'<td >';
										$varname='rootid'.$ged_id;
										echo '<input type="text" size="12" name="', $varname, '" id="', $varname, '" value="" />', print_findindi_link($varname, "", false, false, $ged_name),
									'</td>',						
									// GEDCOM INDI Record ID
									'<td >';
										$varname='gedcomid'.$ged_id;
										echo '<input type="text" size="12" name="',$varname, '" id="',$varname, '" value="" />' ,print_findindi_link($varname, "", false, false, $ged_name),
									'</td>',
									'<td >';
										$varname='canedit'.$ged_id;
										echo '<select name="', $varname, '">';
										foreach ($ALL_EDIT_OPTIONS as $EDIT_OPTION=>$desc) {
											echo '<option value="', $EDIT_OPTION, '" ';
											if ($EDIT_OPTION == WT_I18N::translate('None')) {
												echo 'selected="selected" ';
											}
											echo '>', $desc, '</option>';
										}
										echo '</select>',
									'</td>',
									//Relationship path
									'<td>';
										$varname = 'RELATIONSHIP_PATH_LENGTH'.$ged_id;
										echo '<select name="', $varname, '" id="', $varname, '" class="relpath" />';
											for ($n=0; $n<=10; ++$n) {
												echo
													'<option value="', $n, '">',
													$n ? $n : WT_I18N::translate('No'),
													'</option>';
											}
										echo '</select>',
									'</td>',
								'</tr>';
							}
						?>
					</table>
				</td>
			</tr>
				<td class="topbottombar" colspan="4">
					<input type="submit" value="<?php echo WT_I18N::translate('Create User'); ?>" />
					<input type="button" value="<?php echo WT_I18N::translate('Back'); ?>" onclick="window.location='useradmin.php';"/>
				</td>
			</tr>	
		</table>
	</form>
	<?php
	print_footer();
	exit;
}
