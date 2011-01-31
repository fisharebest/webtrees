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

define('WT_SCRIPT_NAME', 'admin_users.php');
require './includes/session.php';
require_once WT_ROOT.'includes/functions/functions_edit.php';

// Only admin users can access this page
if (!WT_USER_IS_ADMIN) {
	header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.'login.php?url='.WT_SCRIPT_NAME);
	exit;
}

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

// Save new user info to the database
if ($action=='createuser' || $action=='edituser2') {
	if (($action=='createuser' || $action=='edituser2' && $username!=$oldusername) && get_user_id($username)) {
		print_header(WT_I18N::translate('User administration'));
		echo "<span class=\"error\">", WT_I18N::translate('Duplicate user name.  A user with that user name already exists.  Please choose another user name.'), "</span><br />";
	} elseif (($action=='createuser' || $action=='edituser2' && $emailaddress!=$oldemailaddress) && get_user_by_email($emailaddress)) {
		print_header(WT_I18N::translate('User administration'));
		echo "<span class=\"error\">", WT_I18N::translate('Duplicate email address.  A user with that email already exists.'), "</span><br />";
	} else {
		if ($pass1!=$pass2) {
			print_header(WT_I18N::translate('User administration'));
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
			header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH."admin_users.php?action=edituser&username=".rawurlencode($username)."&ged=".rawurlencode($ged));
			exit;
		}
	}
} else {
	print_header(WT_I18N::translate('User administration'));
// if ($ENABLE_AUTOCOMPLETE) require WT_ROOT.'js/autocomplete.js.htm'; Removed becasue it doesn't work here for multiple GEDCOMs. Can be reinstated when fixed (https://bugs.launchpad.net/webtrees/+bug/613235)
}

// Print the form to edit a user
if ($action=="edituser") {
	$user_id=get_user_id($username);
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
		if ((frm.pass1.value!="")&&(frm.pass1.value.length < 6)) {
			alert("<?php echo WT_I18N::translate('Passwords must contain at least 6 characters.'); ?>");
			frm.pass1.value = "";
			frm.pass2.value = "";
			frm.pass1.focus();
			return false;
		}
		if ((frm.emailaddress.value!="")&&(frm.emailaddress.value.indexOf("@")==-1)) {
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

	<form name="editform" method="post" action="admin_users.php" onsubmit="return checkform(this);" autocomplete="off">
		<input type="hidden" name="action" value="edituser2" />
		<input type="hidden" name="filter" value="<?php echo $filter; ?>" />
		<input type="hidden" name="usrlang" value="<?php echo $usrlang; ?>" />
		<input type="hidden" name="oldusername" value="<?php echo $username; ?>" />
		<input type="hidden" name="oldemailaddress" value="<?php echo getUserEmail($user_id); ?>" />
		<!--table-->
		<table  id="adduser">
			<tr>
				<td><?php echo WT_I18N::translate('User name'), help_link('useradmin_username'); ?></td>
				<td colspan="3"><input type="text" name="username" value="<?php echo $username; ?>" autofocus /></td>
			</tr>
			<tr>
				<td><?php echo WT_I18N::translate('Real name'), help_link('useradmin_realname'); ?></td>
				<td colspan="3"><input type="text" name="realname" value="<?php echo getUserFullName($user_id); ?>" size="50" /></td>
			</tr>
			<tr>
				<td><?php echo WT_I18N::translate('Password'), help_link('useradmin_password'); ?></td>
				<td><input type="password" name="pass1" /></td>
				<td><?php echo WT_I18N::translate('Confirm password'), help_link('useradmin_conf_password'); ?></td>
				<td><input type="password" name="pass2" /></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="3"><?php echo WT_I18N::translate('Leave password blank if you want to keep the current password.'); ?></td>
			<tr>
				<td><?php echo WT_I18N::translate('Email address'), help_link('useradmin_email'); ?></td>
				<td><input type="text" name="emailaddress" dir="ltr" value="<?php echo getUserEmail($user_id); ?>" size="50" /></td>
				<td><?php echo WT_I18N::translate('Preferred contact method'), help_link('useradmin_user_contact'); ?></td>
				<td>
					<?php
						echo edit_field_contact('new_contact_method', get_user_setting($user_id, 'contactmethod'));
					?>
				</td>
			</tr>
			<tr>
				<td><?php echo WT_I18N::translate('Email verified'), help_link('useradmin_verification'); ?></td>
				<td><input type="checkbox" name="verified" value="1" <?php if (get_user_setting($user_id, 'verified')) echo "checked=\"checked\""; ?> /></td>
				<td><?php echo WT_I18N::translate('Approved by administrator'), help_link('useradmin_verification'); ?></td>
				<td><input type="checkbox" name="verified_by_admin" value="1" <?php if (get_user_setting($user_id, 'verified_by_admin')) echo "checked=\"checked\""; ?> /></td>
			</tr>
			<tr>
				<td><?php echo WT_I18N::translate('Automatically approve changes made by this user'), help_link('useradmin_auto_accept'); ?></td>
				<td><input type="checkbox" name="new_auto_accept" value="1" <?php if (get_user_setting($user_id, 'auto_accept')) echo "checked=\"checked\""; ?> /></td>
				<td><?php echo WT_I18N::translate('Allow this user to edit his account information'), help_link('useradmin_editaccount'); ?></td>
				<td><input type="checkbox" name="editaccount" value="1" <?php if (get_user_setting($user_id, 'editaccount')) echo "checked=\"checked\""; ?> /></td>
			</tr>
			<tr>
				<td><?php echo WT_I18N::translate('Administrator'), help_link('role'); ?></td>
				<?php
					// Forms won't send the value of checkboxes if they are disabled, so use a hidden field
					echo '<td>';
					echo two_state_checkbox('canadmin', get_user_setting($user_id, 'canadmin'), ($user_id==WT_USER_ID) ? 'disabled="disabled"' : '');
					echo '</td>';
				?>
				<td><?php echo WT_I18N::translate('Visible to other users when online'), help_link('useradmin_visibleonline'); ?></td>
				<td><input type="checkbox" name="visibleonline" value="1" <?php if (get_user_setting($user_id, 'visibleonline')) echo "checked=\"checked\""; ?> /></td>
			</tr>
			<tr>
				<td><?php echo WT_I18N::translate('Admin comments on user'), help_link('useradmin_comment'); ?></td>
				<td><textarea cols="38" rows="5" name="new_comment"><?php $tmp = PrintReady(get_user_setting($user_id, 'comment')); echo $tmp; ?></textarea></td>
				<td><?php echo WT_I18N::translate('Admin warning at date'), help_link('useradmin_comment_exp'); ?></td>
				<td><input type="text" name="new_comment_exp" id="new_comment_exp" value="<?php echo get_user_setting($user_id, 'comment_exp'); ?>" />&nbsp;&nbsp;<?php print_calendar_popup("new_comment_exp"); ?></td>
			</tr>
			<tr>
				<td><?php echo WT_I18N::translate('Language'), help_link('edituser_change_lang'); ?></td>
				<td colspan="3">
					<?php
						echo edit_field_language('user_language', get_user_setting($user_id, 'language'));
					?>
				</td>
			</tr>
			<tr>
				<td><?php echo WT_I18N::translate('Theme'), help_link('THEME'); ?></td>
				<td colspan="3">
					<select name="user_theme" dir="ltr">
					<option value=""><?php echo WT_I18N::translate('&lt;default theme&gt;'); ?></option>
					<?php
					foreach (get_theme_names() as $themename=>$themedir) {
						echo "<option value=\"", $themedir, "\"";
						if ($themedir == get_user_setting($user_id, 'theme')) echo " selected=\"selected\"";
						echo ">", $themename, "</option>";
					}
					?></select>
				</td>
			</tr>
			<tr>
				<td><?php echo WT_I18N::translate('Default Tab to show on Individual Information page'), help_link('useradmin_user_default_tab'); ?></td>
				<td colspan="3">
					<?php echo edit_field_default_tab('new_default_tab', get_user_setting($user_id, 'defaulttab')); ?>
				</td>
			</tr>
			<!-- access and relationship path details -->
			<tr>
				<td class="subbar" colspan="4"><?php print WT_I18N::translate('Family tree access and settings'); ?></td>
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
									'<td>', $ged_name, '</td>',
									//Pedigree root person
									'<td>';
										$varname='rootid'.$ged_id;
										echo '<input type="text" name="', $varname, '" id="', $varname, '" value="';
										$pid=get_user_gedcom_setting($user_id, $ged_id, 'rootid');
										echo $pid, '" />', print_findindi_link($varname, "", false, false, $ged_name);
										$GEDCOM=$ged_name; // library functions use global variable instead of parameter.
										$person=WT_Person::getInstance($pid);
										if ($person) {
											echo '<div class="list_item"><a href="', $person->getHtmlUrl(), '">', PrintReady($person->getFullName()), '</a></div>';
										}
									echo '</td>',						
									// GEDCOM INDI Record ID
									'<td>';
										$varname='gedcomid'.$ged_id;
										echo '<input type="text" name="',$varname, '" id="',$varname, '" value="';
										$pid=get_user_gedcom_setting($user_id, $ged_id, 'gedcomid');
										echo $pid, '" />';
										print_findindi_link($varname, "", false, false, $ged_name);
										$GEDCOM=$ged_name; // library functions use global variable instead of parameter.
										$person=WT_Person::getInstance($pid);
										if ($person) {
											echo ' <div class="list_item"><a href="', $person->getHtmlUrl(), '">', PrintReady($person->getFullName()), '</a></div>';
										}
									echo '</td>',
									'<td>';
										$varname='canedit'.$ged_id;
										echo '<select name="', $varname, '" id="', $varname, '">';
										foreach ($ALL_EDIT_OPTIONS as $EDIT_OPTION=>$desc) {
											echo '<option value="', $EDIT_OPTION, '" ';
											if (get_user_gedcom_setting($user_id, $ged_id, 'canedit')==$EDIT_OPTION) {
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
													'<option value="', $n, '"',
													get_user_gedcom_setting($user_id, $ged_id, 'RELATIONSHIP_PATH_LENGTH')==$n ? ' selected="selected"' : '',				
													'>',
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
			<tr>
				<td colspan="4">
					<input type="submit" value="<?php echo WT_I18N::translate('Update user account'); ?>" />
					<input type="button" value="<?php echo WT_I18N::translate('Back'); ?>" onclick="window.location='<?php echo "admin_users.php?action=listusers&amp;filter={$filter}&amp;usrlang={$usrlang}"; ?>';"/>
				</td>
			</tr>
		</table>
	</form>
	<?php
	print_footer();
	exit;
}

?>
<script type="text/javascript">
	jQuery(document).ready(function(){
		/* Insert a 'details' column to the table */
		var nCloneTh = document.createElement( 'th' );
		var nCloneTd = document.createElement( 'td' );
		nCloneTh.innerHTML = 'Details';
		nCloneTd.innerHTML = '<img class="open" src="./themes/_administration/images/open.png">';
		nCloneTd.className = "open-close";
		
		jQuery('#list thead tr').each( function () {
			this.insertBefore( nCloneTh, this.childNodes[0] );
		} );
		
		jQuery('#list tbody tr').each( function () {
			this.insertBefore(  nCloneTd.cloneNode( true ), this.childNodes[0] );
		} );

		var oTable = jQuery('#list').dataTable( {
			"oLanguage": {
				"sLengthMenu": 'Display <select><option value="10">10</option><option value="20">20</option><option value="30">30</option><option value="40">40</option><option value="50">50</option><option value="-1">All</option></select> records',
				"oPaginate": {
					"sFirst": '<?php echo WT_i18n::translate('First');?>',
					"sLast": '<?php echo WT_i18n::translate('Last');?>',
					"sNext": '<?php echo WT_i18n::translate('Next');?>',
					"sPrevious": '<?php echo WT_i18n::translate('Previous');?>',
					"sZeroRecords": '<?php echo WT_i18n::translate('No records to display');?>',
					"sSearch": '<?php echo WT_i18n::translate('Search');?>:',
					"sLengthMenu": 'Display _MENU_ records',
					"sInfo": 'Showing _START_ to _END_ of _TOTAL_ entries'
				}
			},
			"bJQueryUI": true,
			"bAutoWidth":false,
			"iDisplayLength": 10,
			"sPaginationType": "full_numbers",
			"aaSorting": [[2,'asc']],
			"aoColumns": [
				/* 0-Details */ 		{ "bSortable": false },
				/* 1-Message */ 		null,
				/* 2-Name */   			null,
				/* 3-User */   			null,
				/* 4-Language */		{ "bVisible": false },
				/* 5-Role. */  			{ "bVisible": false },
				/* 6-Auto_approve */	{ "bVisible": false },
				/* 7-Theme */			{ "bVisible": false },
				/* 8-Default_tab */		{ "bVisible": false },
				/* 9-Date registered */ null,
				/* 10-Last login */		null,
				/* 11-Verified */		null,
				/* 12-Approved */		null,
				/* 13-Delete */			null
			]
		});
		
		/* Add event listener for opening and closing details
		 * Note that the indicator for showing which row is open is not controlled by DataTables,
		 * rather it is done here
		*/
		jQuery('#list tbody td img.open').live('click', function () {
			var nTr = this.parentNode.parentNode;
			if ( this.src.match('close') )
			{
				/* This row is already open - close it */
				this.src = "./themes/_administration/images/open.png";
				oTable.fnClose( nTr );
			}
			else
			{
				/* Open this row */
				this.src = "./themes/_administration/images/close.png";
				oTable.fnOpen( nTr, fnFormatDetails(oTable, nTr), 'details' );
			}
		} );
	
	});

	/* Formating function for details row */
	function fnFormatDetails ( oTable, nTr )
	{
		var aData = oTable.fnGetData( nTr );
		var sOut = '<table class="details"><tr>';
		sOut += '<th>'+'<?php echo WT_I18N::translate('Language');?>'+': </th><td>'+aData[4]+'</td>';
		sOut += '<th>'+'<?php echo WT_I18N::translate('Role');?>'+': </th><td>'+aData[5]+'</td>';
		sOut += '<th>'+'<?php echo WT_I18N::translate('Auto accept changes');?>'+': </th><td>'+aData[6]+'</td>';
		sOut += '<th>'+'<?php echo WT_I18N::translate('Theme');?>'+': </th><td>'+aData[7]+'</td>';
		sOut += '<th>'+'<?php echo WT_I18N::translate('Default tab');?>'+': </th><td>'+aData[8]+'</td>';
		sOut += '</tr></table>';
		
		return sOut;
	}
</script>
<?php

/*	echo  TEMPORARILY DISABLED UNTIL FISHAREBEST HAS TIME TO COMPLETE ADDING IN-LINE EDITING TO THE TABLE
		'<table id="user-list" width="100%">',
		'<thead>',
		'<tr>',
		'<th>User ID</th>',
		'<th>', WT_I18N::translate('Real name'), '</th>',
		'<th>', WT_I18N::translate('User name'), '</th>',
		'<th>', WT_I18N::translate('Email'), '</th>',
		'<th>', WT_I18N::translate('Language'), '</th>',
		'<th>', WT_I18N::translate('Date registered'), '</th>',
		'<th>', WT_I18N::translate('Last logged in'), '</th>',
		'<th>', WT_I18N::translate('Verified'), '</th>',
		'<th>', WT_I18N::translate('Approved'), '</th>',
		'</tr>',
		'</thead>',
		'<tbody>',
		'</tbody>',
		'</table>',
		WT_JS_START,
		'jQuery(document).ready(function() {',
		' jQuery("#user-list").dataTable( {',
		'  "oLanguage": {',
		'   "sLengthMenu": "Display <select><option value=10>10</option><option value=20>20</option><option value=30>30</option><option value=40>40</option><option value=50>50</option><option value=-1>All</option></select> records"',
		'  },',
		'  "bAutoWidth":false,',
		'  "aaSorting": [[ 1, "asc" ]],',
		'  "bProcessing": true,',
		'  "bServerSide": true,',
		'  "sAjaxSource": "', WT_SERVER_NAME, WT_SCRIPT_PATH, 'load.php?src=user_list",',
		'  "aaSorting": [[ 1, "asc" ]],',
		'  "bJQueryUI": true,',
		'  "sPaginationType": "full_numbers"',
		' } );',
		'} );',		
		WT_JS_END;
*/

//-- echo out a list of the current users
if ($action == "listusers") {
ob_start();
	$users = get_all_users();
	
	// First filter the users, otherwise the javascript to unfold priviledges gets disturbed
	foreach($users as $user_id=>$user_name) {
		if ($filter == "warnings") {
			if (get_user_setting($user_id, 'comment_exp')) {
				if ((strtotime(get_user_setting($user_id, 'comment_exp')) == "-1") || (strtotime(get_user_setting($user_id, 'comment_exp')) >= time("U"))) unset($users[$user_id]);
			}
			else if (((date("U") - (int)get_user_setting($user_id, 'reg_timestamp')) <= 604800) || get_user_setting($user_id, 'verified')) unset($users[$user_id]);
		}
		else if ($filter == "adminusers") {
			if (!get_user_setting($user_id, 'canadmin')) unset($users[$user_id]);
		}
		else if ($filter == "usunver") {
			if (get_user_setting($user_id, 'verified')) unset($users[$user_id]);
		}
		else if ($filter == "admunver") {
			if ((get_user_setting($user_id, 'verified_by_admin')) || (!get_user_setting($user_id, 'verified'))) {
				unset($users[$user_id]);
			}
		}
		else if ($filter == "language") {
			if (get_user_setting($user_id, 'language') != $usrlang) {
				unset($users[$user_id]);
			}
		}
		else if ($filter == "gedadmin") {
			if (get_user_gedcom_setting($user_id, $ged, 'canedit') != "admin") {
				unset($users[$user_id]);
			}
		}
	}

	// Then show the users
	echo
		'<table id="list">',
			'<thead>',
				'<tr>',
					'<th>', WT_I18N::translate('Message'), '</th>',
					'<th>', WT_I18N::translate('Real name'), '</th>',
					'<th>', WT_I18N::translate('User name'), '</th>',
					'<th>', WT_I18N::translate('Languages'), '</th>',
					'<th>', WT_I18N::translate('Role'), '</th>',
					'<th>', WT_I18N::translate('Automatically approve changes made by this user'), '</th>',
					'<th>', WT_I18N::translate('Theme'), '</th>',
					'<th>', WT_I18N::translate('Default tab'), '</th>',
					'<th>', WT_I18N::translate('Date registered'), '</th>',
					'<th>', WT_I18N::translate('Last logged in'), '</th>',
					'<th>', WT_I18N::translate('Verified'), '</th>',
					'<th>', WT_I18N::translate('Approved'), '</th>',
					'<th>', WT_I18N::translate('Delete'), '</th>',
				'</tr>',
			'</thead>',
			'<tbody>';
				foreach($users as $user_id=>$user_name) {
					echo "<tr><td>";
					if ($user_id!=WT_USER_ID && get_user_setting($user_id, 'contactmethod')!='none') {
						echo "<a href=\"javascript:;\" onclick=\"return message('", $user_name, "');\"><div class=\"icon-email\">&nbsp;</div></a>";
					} else {
						echo '&nbsp;';
					}
					echo '</td>';
					$userName = getUserFullName($user_id);
					echo "<td><a class=\"icon-edit\" href=\"admin_users.php?action=edituser&amp;username={$user_name}&amp;filter={$filter}&amp;usrlang={$usrlang}&amp;ged={$ged}\" title=\"", WT_I18N::translate('Edit'), "\">", $userName, '</a>';
					if (get_user_setting($user_id, 'canadmin')) {
						echo '<div class="warning">', WT_I18N::translate('Administrator'), '</div>';
					}
					echo "</td>";
					if (get_user_setting($user_id, "comment_exp")) {
						if ((strtotime(get_user_setting($user_id, "comment_exp")) != "-1") && (strtotime(get_user_setting($user_id, "comment_exp")) < time("U")))
						echo '<td class="red">', $user_name;
						else echo '<td>', $user_name;
					}
					else echo '<td>', $user_name;
						if (get_user_setting($user_id, "comment")) {
							$tempTitle = PrintReady(get_user_setting($user_id, "comment"));
							echo '<img class="adminicon" align="top" alt="', $tempTitle, '" title="', $tempTitle, '" src="images/notes.png" />';
					}
					echo "</td>\n";
					echo '<td>', Zend_Locale::getTranslation(get_user_setting($user_id, 'language'), 'language', WT_LOCALE), '</td>';
					echo '<td>';
					echo "<ul>";
					foreach ($all_gedcoms as $ged_id=>$ged_name) {
						$role=get_user_gedcom_setting($user_id, $ged_id, 'canedit');
						switch ($role) {
						case 'admin':
						case 'accept':
							echo '<li class="warning">', $ALL_EDIT_OPTIONS[$role];
							break;
						case 'edit':
						case 'access':
						case 'none':
							echo '<li>', $ALL_EDIT_OPTIONS[$role];
							break;
						default:
							echo '<li>', $ALL_EDIT_OPTIONS['none'];
							break;
						}
						$uged = get_user_gedcom_setting($user_id, $ged_id, 'gedcomid');
						if ($uged) {
							echo ' <a href="individual.php?pid=', $uged, '&amp;ged=', rawurlencode($ged_name), '">', $ged_name, '</a></li>';
						} else {
							echo ' ', $ged_name, '</li>';
						}
					}
					echo "</ul>";
					echo '</td>';
					echo '<td>';
						if (get_user_setting($user_id, 'auto_accept')) echo WT_I18N::translate('Yes');
						else echo WT_I18N::translate('No');
					echo '</td>';
					echo '<td>';
						if (get_user_setting($user_id, 'theme')) {			
							foreach (get_theme_names() as $themename=>$themedir) {
								if ($themedir == get_user_setting($user_id, 'theme')) echo $themename;
							}
						} else { echo WT_I18N::translate('default theme');}
					echo '</td>';					

					echo '<td>';
						echo get_user_setting($user_id, 'defaulttab');
					echo '</td>';					


					if (((date("U") - (int)get_user_setting($user_id, 'reg_timestamp')) > 604800) && !get_user_setting($user_id, 'verified'))
						echo '<td class="red">';
					else echo '<td>';
						echo format_timestamp((int)get_user_setting($user_id, 'reg_timestamp'));
					echo '</td>';
					echo '<td>';
						if ((int)get_user_setting($user_id, 'reg_timestamp') > (int)get_user_setting($user_id, 'sessiontime')) {
							echo WT_I18N::translate('Never'), '<br />', WT_I18N::time_ago(time() - (int)get_user_setting($user_id, 'reg_timestamp'));
						} else {
							echo format_timestamp((int)get_user_setting($user_id, 'sessiontime')), '<br />', WT_I18N::time_ago(time() - (int)get_user_setting($user_id, 'sessiontime'));
						}
					echo '</td>',
					'<td class="center">';
						if (get_user_setting($user_id, 'verified')) echo WT_I18N::translate('Yes');
						else echo WT_I18N::translate('No');
					echo '</td>',
					'<td class="center">';
						if (get_user_setting($user_id, 'verified_by_admin')) echo WT_I18N::translate('Yes');
						else echo WT_I18N::translate('No');
					echo '</td>',
					'<td>';
						if (WT_USER_ID!=$user_id)
							echo "<a href=\"admin_users.php?action=deleteuser&amp;username=", rawurlencode($user_name)."&amp;sort={$sort}&amp;filter={$filter}&amp;usrlang={$usrlang}&amp;ged=", rawurlencode($ged), "\" onclick=\"return confirm('", WT_I18N::translate('Are you sure you want to delete the user'), " $user_name');\"><div class=\"icon-delete\">&nbsp;</div></a>";
					echo '</td>',
				'</tr>';
				}
			echo '</tbody>',
		'</table>';
	print_footer();
ob_flush();
	exit;
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

	<form name="newform" method="post" action="admin_users.php?action=listusers" onsubmit="return checkform(this);" autocomplete="off">
		<input type="hidden" name="action" value="createuser" />
		<!--table-->
		<table id="adduser">
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
				<td colspan="3"><?php echo edit_field_language('user_language', get_user_setting(WT_USER_ID, 'language')); ?></td>
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
				<td colspan="4">
					<input type="submit" value="<?php echo WT_I18N::translate('Create User'); ?>" />
					<input type="button" value="<?php echo WT_I18N::translate('Back'); ?>" onclick="window.location='admin_users.php?action=listusers;"/>
				</td>
			</tr>	
		</table>
	</form>
	<?php
	print_footer();
	exit;
}

// Cleanup users and user rights
//NOTE: WORKING
if ($action == "cleanup") {
	?>
	<form name="cleanupform" method="post" action="admin_users.php&action=cleanup">
	<input type="hidden" name="action" value="cleanup2" />
	<table id="clean" class="<?php echo $TEXT_DIRECTION; ?>">
	<?php
	// Check for idle users
	//if (!isset($month)) $month = 1;
	$month = safe_GET_integer('month', 1, 12, 6);
	echo "<tr><th>", WT_I18N::translate('Number of months since the last login for a user\'s account to be considered inactive: '), "</th>";
	echo "<td><select onchange=\"document.location=options[selectedIndex].value;\">";
	for ($i=1; $i<=12; $i++) {
		echo "<option value=\"admin_users.php?action=cleanup&amp;month=$i\"";
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
			?><tr><td><?php echo $user_name, " - <p>", $userName, "</p>", WT_I18N::translate('User\'s account has been inactive too long: ');
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
	<input type="button" value="<?php echo WT_I18N::translate('Back'); ?>" onclick="window.location='admin_users.php?action=listusers';"/>
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
