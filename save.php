<?php
// Callback function for inline editing.
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
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

define('WT_SCRIPT_NAME', 'save.php');
require './includes/session.php';

// The script must always end by calling one of these two functions.
function ok() {
	global $value;
	header('Content-type: text/html; charset=UTF-8');
	echo $value;
	exit;
}
function fail() {
	// Any 4xx code should work.  jeditable recommends 406
	header('HTTP/1.0 406 Not Acceptable');
	exit;
}

// The data item to updated must identified with a single "id" element.
// The id must be a valid CSS identifier, so it can be used in HTML.
// We use "[A-Za-z0-9_]+" separated by "-".

$id=safe_POST('id', '[a-zA-Z0-9_-]+');
list($table, $id1, $id2, $id3)=explode('-', $id.'---');

// The replacement value.
$value=safe_POST('value', WT_REGEX_UNSAFE);

// Every switch must have a default case, and every case must end in ok() or fail()

switch ($table) {
case 'site_setting':
	//////////////////////////////////////////////////////////////////////////////
	// Table name: WT_SITE_SETTING
	// ID format:  site_setting-{setting_name}
	//////////////////////////////////////////////////////////////////////////////

	// Authorisation
	if (!WT_USER_IS_ADMIN) {
		fail();
	}

	// Validation
	switch ($id1) {
	case 'MAX_EXECUTION_TIME':
	case 'SESSION_TIME':
	case 'SMTP_PORT':
		if (!is_numeric($value)) {
			fail();
		}
		break;
	case 'INDEX_DIRECTORY':
		if (!is_dir($value) || substr($value, -1)!='/') {
			fail();
		}
		break;
	case 'MEMORY_LIMIT':
		// Must specify K, M or G.
		if (!preg_match('/^[0-9]+[KMG]$/', $value)) {
			fail();
		}
		break;
	case 'STORE_MESSAGES':
	case 'USE_REGISTRATION_MODULE':
	case 'REQUIRE_ADMIN_AUTH_REGISTRATION':
	case 'ALLOW_USER_THEMES':
	case 'ALLOW_CHANGE_GEDCOM':
	case 'SMTP_AUTH':
		$value=(int)$value;
		break;
	case 'LOGIN_URL':
		if ($value=='') {
			$value=null; // Empty string is invalid - delete the row
		} elseif (!preg_match('/^https?:\/\//', $value)) {
			fail();
		}
		break;
	case 'THEME_DIR':
	case 'SERVER_URL':
	case 'SMTP_ACTIVE':
	case 'SMTP_AUTH_USER':
	case 'SMTP_FROM_NAME':
	case 'SMTP_HELO':
	case 'SMTP_HOST':
	case 'SMTP_SSL':
		break;
	case 'SMTP_AUTH_PASS':
		// The password will be displayed as "click to edit" on screen.
		// Accept the update, but pretend to fail.  This will leave the "click to edit" on screen
		if ($value) {
			set_site_setting($id1, $value);
		}
		fail();
	default:
		// An unrecognised setting
		fail();
	}

	// Authorised and valid - make update
	set_site_setting($id1, $value);
	ok();

case 'user':
	//////////////////////////////////////////////////////////////////////////////
	// Table name: WT_USER
	// ID format:  user-{column_name}-{user_id}
	//////////////////////////////////////////////////////////////////////////////

	// Authorisation
	if (!(WT_USER_IS_ADMIN || WT_USER_ID && WT_USER==$id2)) {
		fail();
	}

	// Validation
	switch ($id1) {
	case 'password':
		// The password will be displayed as "click to edit" on screen.
		// Accept the update, but pretend to fail.  This will leave the "click to edit" on screen
		if ($value) {
			set_user_password($id2, $value);
		}
		fail();
	case 'user_name':
	case 'real_name':
	case 'email':
		break;
	default:
		// An unrecognised setting
		fail();
	}

	// Authorised and valid - make update
	try {
		WT_DB::prepare("UPDATE `##user` SET {$id1}=? WHERE user_id=?")
			->execute(array($value, $id2));
		AddToLog('User ID: '.$id2. ' changed '.$id1.' to '.$value, 'auth');
		ok();
	} catch (PDOException $ex) {
		// Duplicate email or username?
		fail();
	}

case 'user_gedcom_setting':
	//////////////////////////////////////////////////////////////////////////////
	// Table name: WT_USER_GEDCOM_SETTING
	// ID format:  user_gedcom_setting-{user_id}-{gedcom_id}-{setting_name}
	//////////////////////////////////////////////////////////////////////////////

	// Authorisation
	if (!(WT_USER_IS_ADMIN || userGedcomAdmin($id2, $id3))) {
		fail();
	}

	// Validation
	switch($id3) {
	case 'rootid':
	case 'gedcomid':
	case 'canedit':
	case 'RELATIONSHIP_PATH_LENGTH':
		break;
	default:
		// An unrecognised setting
		fail();
	}

	// Authorised and valid - make update
	set_user_gedcom_setting($id1, $id2, $id3, $value);
	ok();

case 'user_setting':
	//////////////////////////////////////////////////////////////////////////////
	// Table name: WT_USER_SETTING
	// ID format:  user_setting-{user_id}-{setting_name}
	//////////////////////////////////////////////////////////////////////////////

	// Authorisation
	if (!(WT_USER_IS_ADMIN || WT_USER_ID && get_user_setting($id1, 'editaccount') && _array($id2, array('language','visible_online','contact_method')))) {
		fail();
	}

	// Validation
	switch ($id2) {
	case 'canadmin':
		// Cannot change our own admin status - either to add it or remove it
		if (WT_USER_ID==$id1) {
			fail();
		}
		break;
	case 'auto_accept':
	case 'editaccount':
	case 'verified':
	case 'verified_by_admin':
	case 'visibleonline':
	case 'max_relation_path':
		$value=(int)$value;
		break;
	case 'contactmethod':
	case 'comment':
	case 'language':
	case 'theme':
		break;
	default:
		// An unrecognised setting
		fail();
	}

	// Authorised and valid - make update
	set_user_setting($id1, $id2, $value);
	ok();

case 'module':
	//////////////////////////////////////////////////////////////////////////////
	// Table name: WT_MODULE
	// ID format:  module-{column}-{module_name}
	//////////////////////////////////////////////////////////////////////////////

	// Authorisation
	if (!WT_USER_IS_ADMIN) {
		fail();
	}

	switch($id1) {
	case 'status':
	case 'tab_order':
	case 'menu_order':
	case 'sidebar_order':
		WT_DB::prepare("UPDATE `##module` SET {$id1}=? WHERE module_name=?")
			->execute(array($value, $id2));
		ok();
	default:
		fail();
	}

default:
	// An unrecognised table
	fail();
}
