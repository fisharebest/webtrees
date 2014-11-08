<?php
// Administrative User Interface.
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009 PGV Development Team.
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
use WT\Log;
use WT\User;

define('WT_SCRIPT_NAME', 'admin_users.php');
require './includes/session.php';

$controller = new WT_Controller_Page();
$controller
	->restrictAccess(Auth::isAdmin())
	->setPageTitle(WT_I18N::translate('User administration'));

require_once WT_ROOT.'includes/functions/functions_edit.php';

// Valid values for form variables
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
$action             = WT_Filter::get('action',    null, 'listusers');
$usrlang            = WT_Filter::post('usrlang',  implode('|', array_keys(WT_I18N::installed_languages())), WT_LOCALE);
$username           = WT_Filter::post('username', WT_REGEX_USERNAME);
$filter             = WT_Filter::post('filter');
$ged                = WT_Filter::post('ged');

// Extract form variables
$realname           = WT_Filter::post('realname'   );
$pass1              = WT_Filter::post('pass1',        WT_REGEX_PASSWORD);
$pass2              = WT_Filter::post('pass2',        WT_REGEX_PASSWORD);
$emailaddress       = WT_Filter::postEmail('emailaddress');
$user_theme         = WT_Filter::post('user_theme',               implode('|', $ALL_THEME_DIRS));
$user_language      = WT_Filter::post('user_language',            implode('|', array_keys(WT_I18N::installed_languages())), WT_LOCALE);
$new_contact_method = WT_Filter::post('new_contact_method');
$new_comment        = WT_Filter::post('new_comment');
$new_auto_accept    = WT_Filter::postBool('new_auto_accept');
$canadmin           = WT_Filter::postBool('canadmin');
$visibleonline      = WT_Filter::postBool('visibleonline');
$editaccount        = WT_Filter::postBool('editaccount');
$verified           = WT_Filter::postBool('verified');
$verified_by_admin  = WT_Filter::postBool('verified_by_admin');

switch ($action) {
case 'loadrows':
	// Generate an AJAX/JSON response for datatables to load a block of rows
	$search = WT_Filter::postArray('search');
	$search = $search['value'];
	$start  = WT_Filter::postInteger('start');
	$length = WT_Filter::postInteger('length');

	$WHERE = " WHERE u.user_id > 0";
	$ARGS  = array();
	if ($search) {
		$WHERE .=
			" AND (".
			" user_name LIKE CONCAT('%', ?, '%') OR " .
			" real_name LIKE CONCAT('%', ?, '%') OR " .
			" email     LIKE CONCAT('%', ?, '%'))";
		$ARGS = array($search, $search, $search);
	}
	Auth::user()->setPreference('admin_users_page_size', $length);
	if ($length > 0) {
		$LIMIT = " LIMIT " . $start . ',' . $length;
	} else {
		$LIMIT = "";
	}
	$order = WT_Filter::postArray('order');
	if ($order) {
		$ORDER_BY = ' ORDER BY ';
		foreach ($order as $key => $value) {
			if ($key > 0) {
				$ORDER_BY .= ',';
			}
			// Datatables numbers columns 0, 1, 2, ...
			// MySQL numbers columns 1, 2, 3, ...
			switch ($value['dir']) {
			case 'asc':
				$ORDER_BY .= (1 + $value['column']) . ' ASC ';
				break;
			case 'desc':
				$ORDER_BY .= (1 + $value['column']) . ' DESC ';
				break;
			}
		}
	} else {
		$ORDER_BY = '1 ASC';
	}

	$sql=
		"SELECT SQL_CACHE SQL_CALC_FOUND_ROWS '', u.user_id, user_name, real_name, email, '', us1.setting_value, us2.setting_value, us2.setting_value, us3.setting_value, us3.setting_value, us4.setting_value, us5.setting_value".
		" FROM `##user` u".
		" LEFT JOIN `##user_setting` us1 ON (u.user_id=us1.user_id AND us1.setting_name='language')".
		" LEFT JOIN `##user_setting` us2 ON (u.user_id=us2.user_id AND us2.setting_name='reg_timestamp')".
		" LEFT JOIN `##user_setting` us3 ON (u.user_id=us3.user_id AND us3.setting_name='sessiontime')".
		" LEFT JOIN `##user_setting` us4 ON (u.user_id=us4.user_id AND us4.setting_name='verified')".
		" LEFT JOIN `##user_setting` us5 ON (u.user_id=us5.user_id AND us5.setting_name='verified_by_admin')".
		$WHERE.
		$ORDER_BY.
		$LIMIT;

	// This becomes a JSON list, not array, so need to fetch with numeric keys.
	$data=WT_DB::prepare($sql)->execute($ARGS)->fetchAll(PDO::FETCH_NUM);

	// Reformat various columns for display
	foreach ($data as &$datum) {
		$datum[0]='<a href="#" title="'.WT_I18N::translate('Details').'">&nbsp;</a>';
		// $aData[1] is the user ID
		$user_id  =$datum[1];
		$user_name=$datum[2];
		$datum[2]=edit_field_inline('user-user_name-'.$user_id, $datum[2]);
		$datum[3]=edit_field_inline('user-real_name-'.$user_id, $datum[3]);
		$datum[4]=edit_field_inline('user-email-'.    $user_id, $datum[4]);
		// $aData[5] is a link to an email icon
		if ($user_id != Auth::id()) {
			$datum[5]='<i class="icon-email" onclick="return message(\''.$user_name.'\', \'\', \'\');"></i>';
		}
		$datum[6]=edit_field_language_inline('user_setting-'.$user_id.'-language', $datum[6]);
		// $aData[7] is the sortable registration timestamp
		$datum[8]=$datum[8] ? format_timestamp($datum[8]) : '';
		if (date("U") - $datum[7] > 604800 && !$datum[11]) {
			$datum[8]='<span class="red">'.$datum[8].'</span>';
		}
		// $aData[9] is the sortable last-login timestamp
		if ($datum[9]) {
			$datum[10]=format_timestamp($datum[9]).'<br>'.WT_I18N::timeAgo(WT_TIMESTAMP - $datum[9]);
		} else {
			$datum[10]=WT_I18N::translate('Never');
		}
		$datum[11]=edit_field_yes_no_inline('user_setting-'.$user_id.'-verified-',          $datum[11]);
		$datum[12]=edit_field_yes_no_inline('user_setting-'.$user_id.'-verified_by_admin-', $datum[12]);
		// Add extra column for "delete" action
		if ($user_id != Auth::id()) {
			$datum[13]='<div class="icon-delete" onclick="delete_user(\'' . WT_I18N::translate('Are you sure you want to delete “%s”?', WT_Filter::escapeJs($user_name)) . '\', \'' . WT_Filter::escapeJs($user_id) . '\');"></div>';
		} else {
			// Do not delete ourself!
			$datum[13]='';
		}
	}

	// Total filtered/unfiltered rows
	$recordsFiltered = (int) WT_DB::prepare("SELECT FOUND_ROWS()")->fetchOne();
	$recordsTotal = User::count();

	Zend_Session::writeClose();
	header('Content-type: application/json');
	echo json_encode(array( // See http://www.datatables.net/usage/server-side
		'draw'            => WT_Filter::getInteger('draw'), // String, but always an integer
		'recordsTotal'    => $recordsTotal,
		'recordsFiltered' => $recordsFiltered,
		'data'            => $data
	));
	exit;
case 'load1row':
	// Generate an AJAX response for datatables to load expanded row
	$user_id = WT_Filter::getInteger('user_id');
	$user = User::find($user_id);
	Zend_Session::writeClose();
	header('Content-type: text/html; charset=UTF-8');
	echo '<h2>', WT_I18N::translate('Details'), '</h2>';
	echo '<dl>';
	echo '<dt>', WT_I18N::translate('Administrator'), '</dt>';
	echo '<dd>', edit_field_yes_no_inline('user_setting-'.$user_id.'-canadmin', $user->getPreference('canadmin')), '</dd>';

	echo '<dt>', WT_I18N::translate('Password'), '</dt>';
	echo '<dd>', edit_field_inline('user-password-'.$user_id, ''), '</dd>';

	echo '<dt>', WT_I18N::translate('Preferred contact method'), '</dt>';
	echo '<dd>', edit_field_contact_inline('user_setting-'.$user_id.'-contactmethod', $user->getPreference('contactmethod')), '</dd>';

	echo '<dt>', WT_I18N::translate('Allow this user to edit his account information'), '</dt>';
	echo '<dd>', edit_field_yes_no_inline('user_setting-'.$user_id.'-editaccount', $user->getPreference('editaccount')), '</dd>';

	echo '<dt>', WT_I18N::translate('Automatically approve changes made by this user'), '</dt>';
	echo '<dd>', edit_field_yes_no_inline('user_setting-'.$user_id.'-auto_accept', $user->getPreference('auto_accept')), '</dd>';

	echo '<dt>', WT_I18N::translate('Theme'), '</dt>';
	echo '<dd>', select_edit_control_inline('user_setting-'.$user_id.'-theme', array_flip(get_theme_names()), WT_I18N::translate('<default theme>'), $user->getPreference('theme')), '</dd>';

	echo '<dt>', WT_I18N::translate('Visible to other users when online'), '</dt>';
	echo '<dd>', edit_field_yes_no_inline('user_setting-'.$user_id.'-visibleonline', $user->getPreference('visibleonline')), '</dd>';

	echo '<dt>', WT_I18N::translate('Comments'), '</dt>';
	echo '<dd>', edit_field_inline('user_setting-'.$user_id.'-comment', $user->getPreference('comment')), '</dd>';

	echo '<dt>', WT_I18N::translate('My page'), '</dt>';
	echo '<dd><a href="#" onclick="modalDialog(\'index_edit.php?user_id='.$user_id.'\', \'', WT_I18N::translate('Change the blocks on this page'), '\');">', WT_I18N::translate('Change the blocks on this page'), '</a></dd>';

	// Masquerade as others users - but not other administrators
	if (!Auth::isAdmin($user)) {
		echo '<dt>', /* I18N: Pretend to be another user, by logging in as them */ WT_I18N::translate('Masquerade as this user'), '</dt>';
		echo '<dd><a href="#" onclick="return masquerade(', $user_id, ')">', /* I18N: verb: pretend to be someone else */ WT_I18N::translate('masquerade'), '</a></dd>';
	}

	echo '</dl>';

	// Column One - details

	echo
		'<div id="access">',
		'<h2>', WT_I18N::translate('Family tree access and settings'), '</h2>',
		'<table><tr>',
		'<th>', WT_I18N::translate('Family tree'), '</th>',
		'<th>', WT_I18N::translate('Default individual'), help_link('default_individual'), '</th>',
		'<th>', WT_I18N::translate('Individual record'), help_link('useradmin_gedcomid'), '</th>',
		'<th>', WT_I18N::translate('Role'), help_link('role'), '</th>',
		'<th>', WT_I18N::translate('Restrict to immediate family'), help_link('RELATIONSHIP_PATH_LENGTH'), '</th>',
		'</tr>';

	foreach (WT_Tree::getAll() as $tree) {
		echo
			'<tr><td>',
			$tree->tree_title_html,
			//Pedigree root person
			'</td><td>',
			// TODO: autocomplete/find/etc. for this field
			edit_field_inline('user_gedcom_setting-'.$user_id.'-'.$tree->tree_id.'-rootid', $tree->getUserPreference($user, 'rootid')),
			'</td><td>',
			// TODO: autocomplete/find/etc. for this field
			edit_field_inline('user_gedcom_setting-'.$user_id.'-'.$tree->tree_id.'-gedcomid', $tree->getUserPreference($user, 'gedcomid')),
			'</td><td>',
			select_edit_control_inline('user_gedcom_setting-'.$user_id.'-'.$tree->tree_id.'-canedit', $ALL_EDIT_OPTIONS, null, $tree->getUserPreference($user, 'canedit')),
			'</td><td>',
			select_edit_control_inline('user_gedcom_setting-'.$user_id.'-'.$tree->tree_id.'-RELATIONSHIP_PATH_LENGTH', array(0=>WT_I18N::translate('no'), 1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10), null, $tree->getUserPreference($user, 'RELATIONSHIP_PATH_LENGTH')),
			'</td></tr>';
	}
	echo '</table>';
	exit;

case 'createuser':
	if (!WT_Filter::checkCsrf()) {
		$action='createform';
	} elseif (User::findByIdentifier($username)) {
		WT_FlashMessages::addMessage(WT_I18N::translate('Duplicate user name.  A user with that user name already exists.  Please choose another user name.'));
		$action='createform';
	} elseif (User::findByIdentifier($emailaddress)) {
		WT_FlashMessages::addMessage(WT_I18N::translate('Duplicate email address.  A user with that email already exists.'));
		$action='createform';
	} elseif ($pass1!=$pass2) {
		WT_FlashMessages::addMessage(WT_I18N::translate('Passwords do not match.'));
		$action='createform';
	} else {
		$user = User::create($username, $realname, $emailaddress, $pass1);
		$user
			->setPreference('reg_timestamp',     date('U'))
			->setPreference('sessiontime',       '0')
			->setPreference('theme',             $user_theme)
			->setPreference('language',          $user_language)
			->setPreference('contactmethod',     $new_contact_method)
			->setPreference('comment',           $new_comment)
			->setPreference('auto_accept',       $new_auto_accept ? '1' : '0')
			->setPreference('canadmin',          $canadmin ? '1' : '0')
			->setPreference('visibleonline',     $visibleonline ? '1' : '0')
			->setPreference('editaccount',       $editaccount ? '1' : '0')
			->setPreference('verified',          $verified ? '1' : '0')
			->setPreference('verified_by_admin', $verified_by_admin ? '1' : '0');

		foreach (WT_Tree::getAll() as $tree) {
			$tree->setUserPreference($user, 'gedcomid', WT_Filter::post('gedcomid'.$tree->tree_id, WT_REGEX_XREF));
			$tree->setUserPreference($user, 'rootid',   WT_Filter::post('rootid'.$tree->tree_id, WT_REGEX_XREF));
			$tree->setUserPreference($user, 'canedit',  WT_Filter::post('canedit'.$tree->tree_id, implode('|', array_keys($ALL_EDIT_OPTIONS))));
			if (WT_Filter::post('gedcomid'.$tree->tree_id, WT_REGEX_XREF)) {
				$tree->setUserPreference($user, 'RELATIONSHIP_PATH_LENGTH', WT_Filter::postInteger('RELATIONSHIP_PATH_LENGTH'.$tree->tree_id, 0, 10, 0));
			} else {
				// Do not allow a path length to be set if the individual ID is not
				$tree->setUserPreference($user, 'RELATIONSHIP_PATH_LENGTH', null);
			}
		}
		Log::addAuthenticationLog("User ->{$username}<- created");
		header('Location: ' . WT_SERVER_NAME . WT_SCRIPT_PATH . WT_SCRIPT_NAME);
		Zend_Session::writeClose();
		exit;
	}
}

$controller->pageHeader();

switch ($action) {
case 'createform':
	$controller
		->addExternalJavascript(WT_STATIC_URL . 'js/autocomplete.js')
		->addInlineJavascript('autocomplete();');

	init_calendar_popup();
	$controller->addInlineJavascript('
		function checkform(frm) {
			if (frm.username.value=="") {
				alert("'.WT_I18N::translate('You must enter a user name.').'");
				frm.username.focus();
				return false;
			}
			if (frm.realname.value=="") {
				alert("'.WT_I18N::translate('You must enter a real name.').'");
				frm.realname.focus();
				return false;
			}
			if (frm.pass1.value=="") {
				alert("'.WT_I18N::translate('You must enter a password.').'");
				frm.pass1.focus();
				return false;
			}
			if (frm.emailaddress.value=="") {
				alert("'.WT_I18N::translate('You must enter an email address.').'");
				frm.emailaddress.focus();
				return false;
			}
			if (frm.pass2.value=="") {
				alert("'.WT_I18N::translate('You must confirm the password.').'");
				frm.pass2.focus();
				return false;
			}
			if (frm.pass1.value.length < 6) {
				alert("'.WT_I18N::translate('Passwords must contain at least 6 characters.').'");
				frm.pass1.value = "";
				frm.pass2.value = "";
				frm.pass1.focus();
				return false;
			}
			return true;
		}
		jQuery(".relpath").change(function() {
			var fieldIDx = jQuery(this).attr("id");
			var idNum = fieldIDx.replace("RELATIONSHIP_PATH_LENGTH","");
			var newIDx = "gedcomid"+idNum;
			if (jQuery("#"+newIDx).val()=="") {
				alert("'.WT_I18N::translate('You must specify an individual record before you can restrict the user to their immediate family.').'");
				jQuery(this).val("");
			}
		});

		function regex_quote(str) {
			return str.replace(/[\\\\.?+*()[\](){}|]/g, "\\\\$&");
		}
	');

	echo '
	<form name="newform" method="post" action="admin_users.php?action=createuser" onsubmit="return checkform(this);" autocomplete="off">
		', WT_Filter::getCsrf(), '
		<table id="adduser">
			<tr>
				<td>', WT_I18N::translate('Real name'), help_link('real_name'), '</td>
				<td><input type="text" name="realname" style="width:95%;" required maxlength="64" value="', WT_Filter::escapeHtml($realname), '" autofocus></td>
				<td>', WT_I18N::translate('Administrator'), help_link('role'), '</td>
				<td><input type="checkbox" name="canadmin" value="1"></td>
			</tr>
			<tr>
				<td>', WT_I18N::translate('Username'), help_link('username'), '</td>
				<td><input type="text" name="username" style="width:95%;" required maxlength="32" value="', WT_Filter::escapeHtml($username), '"></td>
				<td>', WT_I18N::translate('Approved by administrator'), help_link('useradmin_verification'), '</td>
				<td><input type="checkbox" name="verified_by_admin" value="1" checked="checked"></td>
			</tr>
			<tr>
				<td>', WT_I18N::translate('Email address'), help_link('email'), '</td>
				<td><input type="email" name="emailaddress" style="width:95%;" required maxlength="64" value="', WT_Filter::escapeHtml($emailaddress), '"></td>
				<td>', WT_I18N::translate('Email verified'), help_link('useradmin_verification'), '</td>
				<td><input type="checkbox" name="verified" value="1" checked="checked"></td>
			</tr>
			<tr>
				<td>', WT_I18N::translate('Password'), help_link('password'), '</td>
				<td><input type="password" name="pass1" style="width:95%;" value="', WT_Filter::escapeHtml($pass1), '" required placeholder="',  WT_I18N::plural('Use at least %s character.', 'Use at least %s characters.', WT_MINIMUM_PASSWORD_LENGTH, WT_I18N::number(WT_MINIMUM_PASSWORD_LENGTH)), '" pattern="', WT_REGEX_PASSWORD, '" onchange="form.pass2.pattern = regex_quote(this.value);"></td>
				<td>', WT_I18N::translate('Automatically approve changes made by this user'), help_link('useradmin_auto_accept'), '</td>
				<td><input type="checkbox" name="new_auto_accept" value="1"></td>
			</tr>
				<td>', WT_I18N::translate('Confirm password'), help_link('password_confirm'), '</td>
				<td><input type="password" name="pass2" style="width:95%;" value="', WT_Filter::escapeHtml($pass2), '" required placeholder="', WT_I18N::translate('Type the password again.'), '" pattern="', WT_REGEX_PASSWORD, '"></td>
				<td>', WT_I18N::translate('Allow this user to edit his account information'), help_link('useradmin_editaccount'), '</td>
				<td><input type="checkbox" name="editaccount" value="1" checked="checked"></td>
			<tr>
				<td>', WT_I18N::translate('Preferred contact method'), '</td>
				<td>';
					echo edit_field_contact('new_contact_method', $new_contact_method);
				echo '</td>
				<td>', WT_I18N::translate('Visible to other users when online'), help_link('useradmin_visibleonline'), '</td>
				<td><input type="checkbox" name="visibleonline" value="1" checked="checked"></td>
			</tr>
			<tr>
			</tr>
			<tr>
				<td>', WT_I18N::translate('Language'), '</td>
				<td>', edit_field_language('user_language', $user_language), '</td>';
				if (WT_Site::getPreference('ALLOW_USER_THEMES')) {
					echo '<td>', WT_I18N::translate('Theme'), help_link('THEME'), '</td>
					<td>
						<select name="new_user_theme">
						<option value="" selected="selected">', WT_Filter::escapeHtml(WT_I18N::translate('<default theme>')), '</option>';
							foreach (get_theme_names() as $themename=>$themedir) {
								echo '<option value="', $themedir, '">', $themename, '</option>';
							}
						echo '</select>
					</td>';
				}
			echo '</tr>';
			if (Auth::isAdmin()) {
			echo '<tr>
				<td>', WT_I18N::translate('Admin comments on user'), '</td>
				<td colspan="3"><textarea style="width:95%;" rows="5" name="new_comment" value="', WT_Filter::escapeHtml($new_comment), '"></textarea></td>
			</tr>';
			}
			echo '<tr>
				<th colspan="4">', WT_I18N::translate('Family tree access and settings'), '</th>
			</tr>
			<tr>
				<td colspan="4">
					<table id="adduser2">
						<tr>
							<th>', WT_I18N::translate('Family tree'), '</th>
							<th>', WT_I18N::translate('Default individual'), help_link('default_individual'), '</th>
							<th>', WT_I18N::translate('Individual record'), help_link('useradmin_gedcomid'), '</th>
							<th>', WT_I18N::translate('Role'), help_link('role'), '</th>
							<th>', WT_I18N::translate('Restrict to immediate family'), help_link('RELATIONSHIP_PATH_LENGTH'), '</th>
						</tr>';
							foreach (WT_Tree::getAll() as $tree) {
								echo '<tr>',
									'<td>', $tree->tree_title_html, '</td>',
									//Pedigree root person
									'<td>';
										$varname='rootid'.$tree->tree_id;
										echo '<input data-autocomplete-type="INDI" data-autocomplete-ged="' . $tree->tree_name_html . '" type="text" size="12" name="', $varname, '" id="', $varname, '" value="', WT_Filter::escapeHtml(WT_Filter::post('rootid'.$tree->tree_id, WT_REGEX_XREF)), '"> ', print_findindi_link($varname, '', $tree->tree_name),
									'</td>',
									// GEDCOM INDI Record ID
									'<td>';
										$varname='gedcomid'.$tree->tree_id;
										echo '<input data-autocomplete-type="INDI" data-autocomplete-ged="' . $tree->tree_name_html . '" type="text" size="12" name="',$varname, '" id="',$varname, '" value="', WT_Filter::escapeHtml(WT_Filter::post('gedcomid'.$tree->tree_id, WT_REGEX_XREF)), '"> ', print_findindi_link($varname, '', $tree->tree_name),
									'</td>',
									'<td>';
										$varname='canedit'.$tree->tree_id;
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
										$varname = 'RELATIONSHIP_PATH_LENGTH'.$tree->tree_id;
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
					echo '</table>
				</td>
			</tr>
				<td colspan="4">
					<input type="submit" value="', WT_I18N::translate('Create user'), '">
				</td>
			</tr>
		</table>
	</form>';
	break;
case 'cleanup':
	?>
	<form name="cleanupform" method="post" action="admin_users.php?action=cleanup2">
	<table id="clean">
	<?php
	// Check for idle users
	$month = WT_Filter::getInteger('month', 1, 12, 6);
	echo "<tr><th>", WT_I18N::translate('Number of months since the last login for a user’s account to be considered inactive: '), "</th>";
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
	foreach (User::all() as $user) {
		if ($user->getPreference('sessiontime') == "0") {
			$datelogin = (int)$user->getPreference('reg_timestamp');
		} else {
			$datelogin = (int)$user->getPreference('sessiontime');
		}
		if ((mktime(0, 0, 0, (int)date("m")-$month, (int)date("d"), (int)date("Y")) > $datelogin) && $user->getPreference('verified') && $user->getPreference('verified_by_admin')) {
			?><tr><td><?php echo WT_Filter::escapeHtml($user->getUserName()), " - <p>", WT_Filter::escapeHtml($user->getRealName()), "</p>", WT_I18N::translate('User’s account has been inactive too long: ');
			echo timestamp_to_gedcom_date($datelogin)->display();
			$ucnt++;
			?></td><td><input type="checkbox" name="del_<?php echo $user->getUserId(); ?>" value="1"></td></tr><?php
		}
	}

	// Check unverified users
	foreach (User::all() as $user) {
		if (((date("U") - (int)$user->getPreference('reg_timestamp')) > 604800) && !$user->getPreference('verified')) {
			?><tr><td><?php echo WT_Filter::escapeHtml($user->getUserName()), " - ", WT_Filter::escapeHtml($user->getRealName()), ":&nbsp;&nbsp;", WT_I18N::translate('User didn’t verify within 7 days.');
			$ucnt++;
			?></td><td><input type="checkbox" checked="checked" name="del_<?php echo $user->getUserId(); ?>" value="1"></td></tr><?php
		}
	}

	// Check users not verified by admin
	foreach (User::all() as $user) {
		if (!$user->getPreference('verified_by_admin') && $user->getPreference('verified')) {
			?><tr><td><?php echo WT_Filter::escapeHtml($user->getUserName()), " - ", WT_Filter::escapeHtml($user->getRealName()), ":&nbsp;&nbsp;", WT_I18N::translate('User not verified by administrator.');
			?></td><td><input type="checkbox" name="del_<?php echo $user->getUserId(); ?>" value="1"></td></tr><?php
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
		?><input type="submit" value="<?php echo WT_I18N::translate('continue'); ?>">&nbsp;&nbsp;<?php
	} ?>
	</p>
	</form><?php
	break;
case 'cleanup2':
	foreach (User::all() as $user) {
		if (WT_Filter::post('del_' . $user->getUserId()) == '1') {
			Log::addAuthenticationLog('Deleted user: ' . $user->getUserName());
			echo WT_I18N::translate('Deleted user: '), $user->getUserName(), '<br>';
			$user->delete();
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

	$controller
		->addExternalJavascript(WT_JQUERY_DATATABLES_URL)
		->addExternalJavascript(WT_JQUERY_JEDITABLE_URL)
		->addInlineJavascript('
			jQuery("#list").dataTable({
				dom: \'<"H"pf<"dt-clear">irl>t<"F"pl>\',
				' . WT_I18N::datatablesI18N() . ',
				processing: true,
				serverSide: true,
				ajax: {
					"url": "' . WT_SCRIPT_NAME . '?action=loadrows",
					"type": "POST"
				},
				search: {
					search: "' . WT_Filter::escapeJs(WT_Filter::get('filter')) . '"
				},
				jQueryUI: true,
				autoWidth: false,
				pageLength: ' . Auth::user()->getPreference('admin_users_page_size', 10) . ',
				pagingType: "full_numbers",
				sorting: [[2,"asc"]],
				columns: [
					/* details           */ { sortable: false, class: "icon-open" },
					/* user-id           */ { visible: false },
					/* user_name         */ null,
					/* real_name         */ null,
					/* email             */ null,
					/* email link        */ { sortable: false },
					/* language          */ null,
					/* registered (sort) */ { visible: false },
					/* registered        */ { dataSort: 7 },
					/* last_login (sort) */ { visible: false },
					/* last_login        */ { dataSort: 9 },
					/* verified          */ { class: "center" },
					/* approved          */ { class: "center" },
					/* delete            */ { sortable: false }
				],
				"drawCallback": function() {
					// Our JSON responses include Javascript as well as HTML.  This does not get executed automatically…
					jQuery("#list script").each(function() {
						eval(this.text);
					});
				}
			});

			/* When clicking on the +/- icon, we expand/collapse the details block */
			jQuery("#list").on("click", ".icon-open, .icon-close", function () {
				var self = jQuery(this),
					aData,
					oTable = self.parents("table").dataTable();
				    nTr=self.parent();

				if(self.hasClass("icon-open")) {
					aData=oTable.fnGetData(nTr);
					jQuery.get("'.WT_SCRIPT_NAME.'?action=load1row&user_id="+aData[1], function(data) {
						oTable.fnOpen(nTr, data, "details");
					});
				} else {
					oTable.fnClose(nTr);
				}
				jQuery(this).toggleClass("icon-open icon-close");
			});
		');
	break;
}
