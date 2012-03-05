<?php
// Administrative User Interface.
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
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

$controller=new WT_Controller_Base();
$controller
	->requireAdminLogin()
	->setPageTitle(WT_I18N::translate('User administration'));

require_once WT_ROOT.'includes/functions/functions_edit.php';

// Valid values for form variables
$ALL_ACTIONS=array('cleanup', 'cleanup2', 'createform', 'createuser', 'deleteuser', 'listusers', 'loadrows', 'load1row');
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

// Form actions
$action            =safe_GET('action',   $ALL_ACTIONS, 'listusers');
$usrlang           =safe_POST('usrlang',  array_keys(WT_I18N::installed_languages()));
$username          =safe_POST('username', WT_REGEX_USERNAME);
$filter            =safe_POST('filter',   WT_REGEX_NOSCRIPT);
$ged               =safe_POST('ged',      WT_REGEX_NOSCRIPT);

// Extract form variables
$realname          =safe_POST('realname'   );
$pass1             =safe_POST('pass1',        WT_REGEX_PASSWORD);
$pass2             =safe_POST('pass2',        WT_REGEX_PASSWORD);
$emailaddress      =safe_POST('emailaddress', WT_REGEX_EMAIL);
$user_theme        =safe_POST('user_theme',               $ALL_THEME_DIRS);
$user_language     =safe_POST('user_language',            array_keys(WT_I18N::installed_languages()), WT_LOCALE);
$new_contact_method=safe_POST('new_contact_method');
$new_comment       =safe_POST('new_comment',              WT_REGEX_UNSAFE);
$new_auto_accept   =safe_POST_bool('new_auto_accept');
$canadmin          =safe_POST_bool('canadmin');
$visibleonline     =safe_POST_bool('visibleonline');
$editaccount       =safe_POST_bool('editaccount');
$verified          =safe_POST_bool('verified');
$verified_by_admin =safe_POST_bool('verified_by_admin');

// Load all available gedcoms
$all_gedcoms = get_all_gedcoms();
//-- sorting by gedcom filename
asort($all_gedcoms);

switch ($action) {
case 'deleteuser':
	// Delete a user - but don't delete ourselves!
	$username=safe_GET('username');
	$user_id=get_user_id($username);
	if ($user_id && $user_id!=WT_USER_ID) {
		delete_user($user_id);
		AddToLog("deleted user ->{$username}<-", 'auth');
	}
	$action='listusers';
	break;
case 'loadrows':
	// Generate an AJAX/JSON response for datatables to load a block of rows
	$sSearch=safe_GET('sSearch');
	if ($sSearch) {
		$WHERE=
			" WHERE".
			" user_name LIKE CONCAT('%', ?, '%') OR " .
			" real_name LIKE CONCAT('%', ?, '%') OR " .
			" email     LIKE CONCAT('%', ?, '%')";
		$ARGS=array($sSearch, $sSearch, $sSearch);
	} else {
		$WHERE="";
		$ARGS=array();
	}
	$iDisplayStart =(int)safe_GET('iDisplayStart');
	$iDisplayLength=(int)safe_GET('iDisplayLength');
	set_user_setting(WT_USER_ID, 'admin_users_page_size', $iDisplayLength);
	if ($iDisplayLength>0) {
		$LIMIT=" LIMIT " . $iDisplayStart . ',' . $iDisplayLength;
	} else {
		$LIMIT="";
	}
	$iSortingCols=(int)safe_GET('iSortingCols');
	if ($iSortingCols) {
		$ORDER_BY=' ORDER BY ';
		for ($i=0; $i<$iSortingCols; ++$i) {
			// Datatables numbers columns 0, 1, 2, ...
			// MySQL numbers columns 1, 2, 3, ...
			switch (safe_GET('sSortDir_'.$i)) {
			case 'asc':
				$ORDER_BY.=(1+(int)safe_GET('iSortCol_'.$i)).' ASC ';
				break;
			case 'desc':
				$ORDER_BY.=(1+(int)safe_GET('iSortCol_'.$i)).' DESC ';
				break;
			}
			if ($i<$iSortingCols-1) {
				$ORDER_BY.=',';
			}
		}
	} else {
		$ORDER_BY='';
	}
	
	$sql=
		"SELECT SQL_CACHE SQL_CALC_FOUND_ROWS '', u.user_id, user_name, real_name, email, '', us1.setting_value, us2.setting_value, us2.setting_value, us3.setting_value, us3.setting_value, us4.setting_value, us5.setting_value".
		" FROM `##user` u".
		" LEFT JOIN `##user_setting` us1 ON (u.user_id=us1.user_id AND us1.setting_name='language')".
		" LEFT JOIN `##user_setting` us2 ON (u.user_id=us2.user_id AND us2.setting_name='reg_timestamp')".
		" LEFT JOIN `##user_setting` us3 ON (u.user_id=us3.user_id AND us3.setting_name='sessiontime')".
		" LEFT JOIN `##user_setting` us4 ON (u.user_id=us4.user_id AND us4.setting_name='verified')".
		" LEFT JOIN `##user_setting` us5 ON (u.user_id=us5.user_id AND us5.setting_name='verified_by_admin')".
		" WHERE u.user_id>0 ".
		$WHERE.
		$ORDER_BY.
		$LIMIT;
	
	// This becomes a JSON list, not array, so need to fetch with numeric keys.
	$aaData=WT_DB::prepare($sql)->execute($ARGS)->fetchAll(PDO::FETCH_NUM);
	
	// Reformat various columns for display
	foreach ($aaData as &$aData) {
		$aData[0]='<a href="#" title="'.WT_I18N::translate('Details').'">&nbsp;</a>';
		// $aData[1] is the user ID
		$user_id  =$aData[1];
		$user_name=$aData[2];
		$aData[2]=edit_field_inline('user-user_name-'.$user_id, $aData[2]);
		$aData[3]=edit_field_inline('user-real_name-'.$user_id, $aData[3]);
		$aData[4]=edit_field_inline('user-email-'.    $user_id, $aData[4]);
		// $aData[5] is a link to an email icon
		if ($user_id != WT_USER_ID) {
			$aData[5]='<div class="icon-email" onclick="return message(\''.$user_name.'\');"></div>';
		}
		$aData[6]=edit_field_language_inline('user_setting-'.$user_id.'-language', $aData[6]);
		// $aData[7] is the sortable registration timestamp
		$aData[8]=format_timestamp($aData[8]);
		if (date("U") - $aData[7] > 604800 && !$aData[11]) {
			$aData[8]='<span class="red">'.$aData[8].'</span>';
		}
		// $aData[9] is the sortable last-login timestamp
		if ($aData[9]) {
			$aData[10]=format_timestamp($aData[9]).'<br>'.WT_I18N::time_ago(time() - $aData[9]);
		} else {
			$aData[10]=WT_I18N::translate('Never');
		}
		$aData[11]=edit_field_yes_no_inline('user_setting-'.$user_id.'-verified-',          $aData[11]);
		$aData[12]=edit_field_yes_no_inline('user_setting-'.$user_id.'-verified_by_admin-', $aData[12]);
		// Add extra column for "delete" action
		if ($user_id != WT_USER_ID) {
			$aData[13]='<div class="icon-delete" onclick="if (confirm(\''.htmlspecialchars(WT_I18N::translate('Permanently delete "%s"?', $user_name)).'\')) { document.location=\''.WT_SCRIPT_NAME.'?action=deleteuser&username='.htmlspecialchars($user_name).'\'; }"></div>';
		} else {
			// Do not delete ourself!
			$aData[13]='';
		}
	}
	
	// Total filtered/unfiltered rows
	$iTotalDisplayRecords=WT_DB::prepare("SELECT FOUND_ROWS()")->fetchOne();
	$iTotalRecords=WT_DB::prepare("SELECT SQL_CACHE COUNT(*) FROM `##user` WHERE user_id>0")->fetchOne();

	header('Content-type: application/json');
	echo json_encode(array( // See http://www.datatables.net/usage/server-side
		'sEcho'               =>(int)safe_GET('sEcho'),
		'iTotalRecords'       =>$iTotalRecords,
		'iTotalDisplayRecords'=>$iTotalDisplayRecords,
		'aaData'              =>$aaData
	));
	exit;
case 'load1row':
	// Generate an AJAX response for datatables to load expanded row
	$user_id=(int)safe_GET('user_id');
	header('Content-type: text/html; charset=UTF-8');
	echo '<h2>', WT_I18N::translate('Details'), '</h2>';
	echo '<dl>';
	echo '<dt>', WT_I18N::translate('Administrator'), '</dt>';
	echo '<dd>', edit_field_yes_no_inline('user_setting-'.$user_id.'-canadmin', get_user_setting($user_id, 'canadmin')), '</dd>';

	echo '<dt>', WT_I18N::translate('Password'), '</dt>';
	echo '<dd>', edit_field_inline('user-password-'.$user_id, ''), '</dd>';

	echo '<dt>', WT_I18N::translate('Preferred contact method'), '</dt>';
	echo '<dd>', edit_field_contact_inline('user_setting-'.$user_id.'-contactmethod', get_user_setting($user_id, 'contactmethod')), '</dd>';

	echo '<dt>', WT_I18N::translate('Allow this user to edit his account information'), '</dt>';
	echo '<dd>', edit_field_yes_no_inline('user_setting-'.$user_id.'-editaccount', get_user_setting($user_id, 'editaccount')), '</dd>';

	echo '<dt>', WT_I18N::translate('Automatically approve changes made by this user'), '</dt>';
	echo '<dd>', edit_field_yes_no_inline('user_setting-'.$user_id.'-auto_accept', get_user_setting($user_id, 'auto_accept')), '</dd>';

	echo '<dt>', WT_I18N::translate('Theme'), '</dt>';
	echo '<dd>', select_edit_control_inline('user_setting-'.$user_id.'-theme', array_flip(get_theme_names()), WT_I18N::translate('<default theme>'), get_user_setting($user_id, 'theme')), '</dd>';

	echo '<dt>', WT_I18N::translate('Visible to other users when online'), '</dt>';
	echo '<dd>', edit_field_yes_no_inline('user_setting-'.$user_id.'-visibleonline', get_user_setting($user_id, 'visibleonline')), '</dd>';

	echo '<dt>', WT_I18N::translate('Comments'), '</dt>';
	echo '<dd>', edit_field_inline('user_setting-'.$user_id.'-comment', get_user_setting($user_id, 'comment')), '</dd>';

	echo '<dt>', WT_I18N::translate('My page'), '</dt>';
	echo '<dd><a href="#" onclick="modalDialog(\'index_edit.php?user_id='.$user_id.'\', \'', WT_I18N::translate('Change the blocks on this page'), '\');">', WT_I18N::translate('Change the blocks on this page'), '</a></dd>';

	echo '</dl>';

	// Column One - details

	echo
		'<div id="access">',
		'<h2>', WT_I18N::translate('Family tree access and settings'), '</h2>',
		'<table><tr>',
		'<th>', WT_I18N::translate('Family tree'), '</th>',
		'<th>', WT_I18N::translate('Pedigree chart root person'), help_link('useradmin_rootid'), '</th>',
		'<th>', WT_I18N::translate('Individual record'), help_link('useradmin_gedcomid'), '</th>',
		'<th>', WT_I18N::translate('Role'), help_link('role'), '</th>',
		'<th>', WT_I18N::translate('Restrict to immediate family'), help_link('RELATIONSHIP_PATH_LENGTH'), '</th>',
		'</tr>';

	foreach ($all_gedcoms as $ged_id=>$ged_name) {
		echo
			'<tr><td>',
			WT_I18N::translate('%s', get_gedcom_setting($ged_id, 'title')), 
			//Pedigree root person
			'</td><td>',
			// TODO: autocomplete/find/etc. for this field
			edit_field_inline('user_gedcom_setting-'.$user_id.'-'.$ged_id.'-rootid', get_user_gedcom_setting($user_id, $ged_id, 'rootid')),
			'</td><td>',
			// TODO: autocomplete/find/etc. for this field
			edit_field_inline('user_gedcom_setting-'.$user_id.'-'.$ged_id.'-gedcomid', get_user_gedcom_setting($user_id, $ged_id, 'gedcomid')),
			'</td><td>',
			select_edit_control_inline('user_gedcom_setting-'.$user_id.'-'.$ged_id.'-canedit', $ALL_EDIT_OPTIONS, null, get_user_gedcom_setting($user_id, $ged_id, 'canedit', 'none')),
			'</td><td>',
			select_edit_control_inline('user_gedcom_setting-'.$user_id.'-'.$ged_id.'-RELATIONSHIP_PATH_LENGTH', array(0=>WT_I18N::translate('no'), 1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10), null, get_user_gedcom_setting($user_id, $ged_id, 'RELATIONSHIP_PATH_LENGTH', '0')),
			'</td></tr>';
	}
	echo '</table>';
	exit;
}

$controller->pageHeader();

// Pass 1 - perform action updates
switch ($action) {
case 'createuser':
	if (get_user_id($username)) {
		echo '<div class="ui-state-error">', WT_I18N::translate('Duplicate user name.  A user with that user name already exists.  Please choose another user name.'), '</div>';
		$action='createform';
	} elseif (get_user_by_email($emailaddress)) {
		echo '<div class="ui-state-error">', WT_I18N::translate('Duplicate email address.  A user with that email already exists.'), '</div>';
		$action='createform';
	} elseif ($pass1!=$pass2) {
		echo '<div class="ui-state-error">', WT_I18N::translate('Passwords do not match.'), '</div>';
		$action='createform';
	} else {
		// Create new uers
		$user_id=create_user($username, $realname, $emailaddress, $pass1);
		set_user_setting($user_id, 'reg_timestamp', date('U'));
		set_user_setting($user_id, 'sessiontime', '0');
		setUserFullName ($user_id, $realname);
		setUserEmail    ($user_id, $emailaddress);
		set_user_setting($user_id, 'theme',                $user_theme);
		set_user_setting($user_id, 'language',             $user_language);
		set_user_setting($user_id, 'contactmethod',        $new_contact_method);
		set_user_setting($user_id, 'comment',              $new_comment);
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
		AddToLog("User ->{$username}<- created", 'auth');
		$action='listusers';
	}
}

// Pass 2 - display page
switch ($action) {
case 'createform':
	if (get_gedcom_count()==1) { //Removed becasue it doesn't work here for multiple GEDCOMs. Can be reinstated when fixed (https://bugs.launchpad.net/webtrees/+bug/613235)
		if ($ENABLE_AUTOCOMPLETE) require WT_ROOT.'js/autocomplete.js.htm'; 
	}

	init_calendar_popup();
	$controller->addInlineJavaScript('
		function checkform(frm) {
			if (frm.username.value=="") {
				alert("'.addslashes(WT_I18N::translate('You must enter a user name.')).'");
				frm.username.focus();
				return false;
			}
			if (frm.realname.value=="") {
				alert("'.addslashes(WT_I18N::translate('You must enter a real name.')).'");
				frm.realname.focus();
				return false;
			}
			if (frm.pass1.value=="") {
				alert("'.addslashes(WT_I18N::translate('You must enter a password.')).'");
				frm.pass1.focus();
				return false;
			}
			if (frm.pass2.value=="") {
				alert("'.addslashes(WT_I18N::translate('You must confirm the password.')).'");
				frm.pass2.focus();
				return false;
			}
			if (frm.pass1.value.length < 6) {
				alert("'.addslashes(WT_I18N::translate('Passwords must contain at least 6 characters.')).'");
				frm.pass1.value = "";
				frm.pass2.value = "";
				frm.pass1.focus();
				return false;
			}
			if (frm.emailaddress.value.indexOf("@")==-1) {
				alert("'.addslashes(WT_I18N::translate('You must enter an email address.')).'");
				frm.emailaddress.focus();
				return false;
			}
			return true;
		}
		var pastefield;
		function paste_id(value) {
			pastefield.value=value;
		}
		jQuery(".relpath").change(function() {
			var fieldIDx = jQuery(this).attr("id");
			var idNum = fieldIDx.replace("RELATIONSHIP_PATH_LENGTH","");
			var newIDx = "gedcomid"+idNum;
			if (jQuery("#"+newIDx).val()=="") {
				alert("'.addslashes(WT_I18N::translate('You must specify an individual record before you can restrict the user to their immediate family.')).'");
				jQuery(this).val("");
			}
		});
	');

	?>
	<form name="newform" method="post" action="admin_users.php?action=createuser" onsubmit="return checkform(this);">
		<!--table-->
		<table id="adduser">
			<tr>
				<td><?php echo WT_I18N::translate('Username'), help_link('username'); ?></td>
				<td colspan="3"><input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" autofocus></td>
			</tr>
			<tr>
				<td><?php echo WT_I18N::translate('Real name'), help_link('real_name'); ?></td>
				<td colspan="3"><input type="text" name="realname" size="40" value="<?php echo htmlspecialchars($realname); ?>"></td>
			</tr>
			<tr>
				<td><?php echo WT_I18N::translate('Password'), help_link('password'); ?></td>
				<td><input type="password" name="pass1" value="<?php echo htmlspecialchars($pass1); ?>"></td>
				<td><?php echo WT_I18N::translate('Confirm password'), help_link('password_confirm'); ?></td>
				<td><input type="password" name="pass2" value="<?php echo htmlspecialchars($pass2); ?>"></td>
			</tr>
			<tr>
				<td><?php echo WT_I18N::translate('Email address'), help_link('email'); ?></td>
				<td><input type="text" name="emailaddress" size="40" value="<?php echo htmlspecialchars($emailaddress); ?>"></td>
				<td><?php echo WT_I18N::translate('Preferred contact method'); ?></td>
				<td>
					<?php
						echo edit_field_contact('new_contact_method', $new_contact_method);
					?>
				</td>
			</tr>
			<tr>
				<td><?php echo WT_I18N::translate('Email verified'), help_link('useradmin_verification'); ?></td>
				<td><input type="checkbox" name="verified" value="1" checked="checked"></td>
				<td><?php echo WT_I18N::translate('Approved by administrator'), help_link('useradmin_verification'); ?></td>
				<td><input type="checkbox" name="verified_by_admin" value="1" checked="checked"></td>
			</tr>
			<tr>
				<td><?php echo WT_I18N::translate('Automatically approve changes made by this user'), help_link('useradmin_auto_accept'); ?></td>
				<td><input type="checkbox" name="new_auto_accept" value="1"></td>
				<td><?php echo WT_I18N::translate('Allow this user to edit his account information'), help_link('useradmin_editaccount'); ?></td>
				<td><input type="checkbox" name="editaccount" value="1" checked="checked"></td>
			</tr>
			<tr>
				<td><?php echo WT_I18N::translate('Administrator'), help_link('role'); ?></td>
				<td><input type="checkbox" name="canadmin" value="1"></td>
				<td><?php echo WT_I18N::translate('Visible to other users when online'), help_link('useradmin_visibleonline'); ?></td>
				<td><input type="checkbox" name="visibleonline" value="1" checked="checked"></td>
			</tr>
			<?php if (WT_USER_IS_ADMIN) { ?>
			<tr>
				<td><?php echo WT_I18N::translate('Admin comments on user'); ?></td>
				<td colspan="3"><textarea cols="80" rows="5" name="new_comment" value="<?php echo htmlspecialchars($new_comment); ?>"></textarea></td>
			</tr>
			<?php } ?>
			<tr>
				<td><?php echo WT_I18N::translate('Language'); ?></td>
				<td colspan="3"><?php echo edit_field_language('user_language', $user_language); ?></td>
			</tr>
			<?php if (get_site_setting('ALLOW_USER_THEMES')) { ?>
				<tr>
					<td><?php echo WT_I18N::translate('Theme'), help_link('THEME'); ?></td>
					<td colspan="3">
						<select name="new_user_theme">
						<option value="" selected="selected"><?php echo htmlspecialchars(WT_I18N::translate('<default theme>')); ?></option>
						<?php
							foreach (get_theme_names() as $themename=>$themedir) {
								echo '<option value="', $themedir, '">', $themename, '</option>';
							}
						?>
						</select>
					</td>
				</tr>
			<?php } ?>
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
									'<td>', WT_I18N::translate('%s', get_gedcom_setting($ged_id, 'title')), '</td>',
									//Pedigree root person
									'<td>';
										$varname='rootid'.$ged_id;
										echo '<input type="text" size="12" name="', $varname, '" id="', $varname, '" value="', htmlspecialchars(safe_POST_xref('gedcomid'.$ged_id)), '">', print_findindi_link($varname, "", false, false, $ged_name),
									'</td>',						
									// GEDCOM INDI Record ID
									'<td>';
										$varname='gedcomid'.$ged_id;
										echo '<input type="text" size="12" name="',$varname, '" id="',$varname, '" value="', htmlspecialchars(safe_POST_xref('rootid'.$ged_id)), '">' ,print_findindi_link($varname, "", false, false, $ged_name),
									'</td>',
									'<td>';
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
										echo '<select name="', $varname, '" id="', $varname, '" class="relpath">';
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
					<input type="submit" value="<?php echo WT_I18N::translate('Create User'); ?>">
				</td>
			</tr>	
		</table>
	</form>
	<?php
	break;
case 'cleanup':
	?>
	<form name="cleanupform" method="post" action="admin_users.php?action=cleanup2">
	<table id="clean">
	<?php
	// Check for idle users
	//if (!isset($month)) $month = 1;
	$month = safe_GET_integer('month', 1, 12, 6);
	echo "<tr><th>", WT_I18N::translate('Number of months since the last login for a user\'s account to be considered inactive: '), "</th>";
	echo "<td><select onchange=\"document.location=options[selectedIndex].value;\">";
	for ($i=1; $i<=12; $i++) {
		echo "<option value=\"admin_users.php?action=cleanup&amp;month=$i\"";
		if ($i == $month) echo " selected=\"selected\"";
		echo ">", $i, "</option>";
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
			?></td><td><input type="checkbox" name="<?php echo "del_", str_replace(array(".", "-", " "), array("_", "_", "_"), $user_name); ?>" value="1"></td></tr><?php
		}
	}

	// Check unverified users
	foreach (get_all_users() as $user_id=>$user_name) {
		if (((date("U") - (int)get_user_setting($user_id, 'reg_timestamp')) > 604800) && !get_user_setting($user_id, 'verified')) {
			$userName = getUserFullName($user_id);
			?><tr><td><?php echo $user_name, " - ", $userName, ":&nbsp;&nbsp;", WT_I18N::translate('User didn\'t verify within 7 days.');
			$ucnt++;
			?></td><td><input type="checkbox" checked="checked" name="<?php echo "del_", str_replace(array(".", "-", " "), array("_",  "_", "_"), $user_name); ?>" value="1"></td></tr><?php
		}
	}

	// Check users not verified by admin
	foreach (get_all_users() as $user_id=>$user_name) {
		if (!get_user_setting($user_id, 'verified_by_admin') && get_user_setting($user_id, 'verified')) {
			$userName = getUserFullName($user_id);
			?><tr><td><?php echo $user_name, " - ", $userName, ":&nbsp;&nbsp;", WT_I18N::translate('User not verified by administrator.');
			?></td><td><input type="checkbox" name="<?php echo "del_", str_replace(array(".", "-", " "), array("_", "_", "_"), $user_name); ?>" value="1"></td></tr><?php
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
		?><input type="submit" value="<?php echo WT_I18N::translate('Continue'); ?>">&nbsp;&nbsp;<?php
	} ?>
	<input type="button" value="<?php echo WT_I18N::translate('Back'); ?>" onclick="window.location='admin_users.php';">
	</p>
	</form><?php
	break;
case 'cleanup2':
	foreach (get_all_users() as $user_id=>$user_name) {
		$var = "del_".str_replace(array(".", "-", " "), array("_", "_", "_"), $user_name);
		if (safe_POST($var)=='1') {
			delete_user($user_id);
			AddToLog("deleted user ->{$user_name}<-", 'auth');
			echo WT_I18N::translate('Deleted user: '); echo $user_name, "<br>";
		} else {
			$tempArray = unserialize(get_user_setting($user_id, 'canedit'));
			if (is_array($tempArray)) {
				foreach ($tempArray as $gedid=>$data) {
					$var = "delg_".str_replace(array(".", "-", " "), "_", $gedid);
					if (safe_POST($var)=='1' && get_user_gedcom_setting($user_id, $gedid, 'canedit')) {
						set_user_gedcom_setting($user_id, $gedid, 'canedit', null);
						echo $gedid, ":&nbsp;&nbsp;", WT_I18N::translate('Unset GEDCOM rights for '), $user_name, "<br>";
					}
				}
			}
			$tempArray = unserialize(get_user_setting($user_id, 'rootid'));
			if (is_array($tempArray)) {
				foreach ($tempArray as $gedid=>$data) {
					$var = "delg_".str_replace(array(".", "-", " "), "_", $gedid);
					if (safe_POST($var)=='1' && get_user_gedcom_setting($user_id, $gedid, 'rootid')) {
						set_user_gedcom_setting($user_id, $gedid, 'rootid', null);
						echo $gedid, ":&nbsp;&nbsp;", WT_I18N::translate('Unset root ID for '), $user_name, "<br>";
					}
				}
			}
			$tempArray = unserialize(get_user_setting($user_id, 'gedcomid'));
			if (is_array($tempArray)) {
				foreach ($tempArray as $gedid=>$data) {
					$var = "delg_".str_replace(array(".", "-", " "), "_", $gedid);
					if (safe_POST($var)=='1' && get_user_gedcom_setting($user_id, $gedid, 'gedcomid')) {
						set_user_gedcom_setting($user_id, $gedid, 'gedcomid', null);
						echo $gedid, ":&nbsp;&nbsp;", WT_I18N::translate('Unset GEDCOM ID for '), $user_name, "<br>";
					}
				}
			}
		}
	}
	break;
case 'listusers':
default:
	echo
		'<table id="list">',
			'<thead>',
				'<tr>',
					'<th style="margin:0 -2px 1px 1px; padding:6px 0 5px;"> </th>',
					'<th> user-id </th>',
					'<th>', WT_I18N::translate('Username'), '</th>',
					'<th>', WT_I18N::translate('Real name'), '</th>',
					'<th>', WT_I18N::translate('Email'), '</th>',
					'<th> </th>', /* COLSPAN does not work? */
					'<th>', WT_I18N::translate('Language'), '</th>',
					'<th> date_registered </th>',
					'<th>', WT_I18N::translate('Date registered'), '</th>',
					'<th> last_login </th>',
					'<th>', WT_I18N::translate('Last logged in'), '</th>',
					'<th>', WT_I18N::translate('Verified'), '</th>',
					'<th>', WT_I18N::translate('Approved'), '</th>',
					'<th style="margin:0 -2px 1px 1px; padding:3px 0 4px;"> </th>',
				'</tr>',
			'</thead>',
			'<tbody>',
			'</tbody>',
		'</table>';
	
	echo '<p><a href="#" onclick="modalDialog(\'index_edit.php?user_id=-1'.'\', \'', WT_I18N::translate('Change the default blocks for new users'), '\');">', WT_I18N::translate('Change the default blocks for new users'), '</a></p>';
	
	$controller
		->addExternalJavaScript(WT_STATIC_URL.'js/jquery/jquery.dataTables.min.js')
		->addInlineJavaScript('
			var oTable = jQuery("#list").dataTable({
				"sDom": \'<"H"pf<"dt-clear">irl>t<"F"pl>\',
				'.WT_I18N::datatablesI18N().',
				"bProcessing"     : true,
				"bServerSide"     : true,
				"sAjaxSource"     : "'.WT_SCRIPT_NAME.'?action=loadrows",
				"bJQueryUI": true,
				"bAutoWidth":false,
				"iDisplayLength": '.get_user_setting(WT_USER_ID, 'admin_users_page_size', 10).',
				"sPaginationType": "full_numbers",
				"aaSorting": [[2,"asc"]],
				"aoColumns": [
					/* details           */ { bSortable:false, sClass:"icon-open" },
					/* user-id           */ { bVisible:false },
					/* user_name         */ null,
					/* real_name         */ null,
					/* email             */ null,
					/* email link        */ { bSortable:false },
					/* language          */ null,
					/* registered (sort) */ { bVisible:false },
					/* registered        */ { iDataSort:7 },
					/* last_login (sort) */ { bVisible:false },
					/* last_login        */ { iDataSort:9 },
					/* verified          */ { sClass:"center" },
					/* approved          */ { sClass:"center" },
					/* delete            */ { bSortable:false }
				],
				"fnDrawCallback": function() {
					// Our JSON responses include JavaScript as well as HTML.  This does not get
					// executed (except for some versions of Firefox?).  So, extract it, and add
					// it to its own DOM element
					jQuery("#list script").each(function() {
						var script=document.createElement("script");
						script.type="text/javascript";
						jQuery("#list script").appendTo("body"); 
						document.body.appendChild(script);
					}).remove();
				}				
			});
			
			/* When clicking on the +/- icon, we expand/collapse the details block */
			jQuery("#list tbody td.icon-close").live("click", function () {
				var nTr=this.parentNode;
				jQuery(this).removeClass("icon-close");
				oTable.fnClose(nTr);
				jQuery(this).addClass("icon-open");
			});
			jQuery("#list tbody td.icon-open").live("click", function () {
				var nTr=this.parentNode;
				jQuery(this).removeClass("icon-open");
				var aData=oTable.fnGetData(nTr);
				jQuery.get("'.WT_SCRIPT_NAME.'?action=load1row&user_id="+aData[1], function(data) {
					oTable.fnOpen(nTr, data, "details");
				});
				jQuery(this).addClass("icon-close");
			});
			oTable.fnFilter("'.safe_GET('filter', WT_REGEX_USERNAME).'");
		');
	break;
}
