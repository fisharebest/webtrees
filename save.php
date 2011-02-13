<?php
//
// Callback function for inline editing.
//
// webtrees: Web based Family History software
// Copyright (C) 2010 webtrees development team.
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
// @version $Id$

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
case 'block_setting':
	//////////////////////////////////////////////////////////////////////////////
	// Table name: WT_BLOCK_SETTING
	//////////////////////////////////////////////////////////////////////////////
	fail();

case 'gedcom_setting':
	//////////////////////////////////////////////////////////////////////////////
	// Table name: WT_GEDCOM_SETTING
	//////////////////////////////////////////////////////////////////////////////
	fail();

case 'ip_address':
	//////////////////////////////////////////////////////////////////////////////
	// Table name: WT_IP_ADDRESS
	//////////////////////////////////////////////////////////////////////////////
	fail();

case 'module_privacy':
	//////////////////////////////////////////////////////////////////////////////
	// Table name: WT_MODULE_PRIVACY
	//////////////////////////////////////////////////////////////////////////////
	fail();

case 'module_setting':
	//////////////////////////////////////////////////////////////////////////////
	// Table name: WT_MODULE_SETTING
	//////////////////////////////////////////////////////////////////////////////
	fail();

case 'site_setting':
	//////////////////////////////////////////////////////////////////////////////
	// Table name: WT_SITE_SETTING
	// ID format:  site_setting-{setting_name}
	// Access:     administrator only
	//////////////////////////////////////////////////////////////////////////////
	if (!WT_USER_IS_ADMIN) {
		fail();
	}
	switch ($id1) {
	case 'INDEX_DIRECTORY':
	case 'THEME_DIR':
	case 'ALLOW_CHANGE_GEDCOM':
	case 'ALLOW_USER_THEMES':
	case 'SMTP_AUTH':
	case 'SMTP_SIMPLE_MAIL':
	case 'STORE_MESSAGES':
	case 'REQUIRE_ADMIN_AUTH_REGISTRATION':
	case 'USE_REGISTRATION_MODULE':
	case 'MAX_EXECUTION_TIME':
	case 'SESSION_TIME':
	case 'SMTP_PORT':
	case 'SERVER_URL':
	case 'LOGIN_URL':
	case 'MEMORY_LIMIT':
	case 'SMTP_SSL':
	case 'SMTP_ACTIVE':
	case 'SMTP_HOST':
	case 'SMTP_HELO':
	case 'SMTP_AUTH_USER':
	case 'SMTP_AUTH_PASS':
	case 'SMTP_FROM_NAME':
		set_site_setting($id1, $value);
		ok();
	default:
		// An unrecognised setting
		fail();
	}

case 'user':
	//////////////////////////////////////////////////////////////////////////////
	// Table name: WT_USER
	// ID format:  user-{column_name}-{user_id}
	// Access:     administrator
	//             user (if they have "editaccount" rights)
	//////////////////////////////////////////////////////////////////////////////
	switch ($id1) {
	case 'user_name':
	case 'real_name':
	case 'email':
		if (WT_USER_IS_ADMIN || WT_USER==$id2 && get_user_setting($id2, 'editaccount')) {
			try {
				WT_DB::prepare("UPDATE `##user` SET {$id1}=? WHERE user_id=?")
					->execute(array($value, $id2));
			} catch (PDOException $ex) {
				// Duplicate email or username? How can we display an error message?
				fail();
			}
			ok();
		} else {
			// Not allowed
			fail();
		}
	default:
		// An unrecognised setting
		fail();
	}

case 'user_gedcom_setting':
	//////////////////////////////////////////////////////////////////////////////
	// Table name: WT_USER_GEDCOM_SETTING
	//////////////////////////////////////////////////////////////////////////////
	fail();

case 'user_setting':
	//////////////////////////////////////////////////////////////////////////////
	// Table name: WT_USER_SETTING
	// ID format:  user_setting-{setting_name}-{user_id}
	// Access:     administrator
	//             member (some fields only - if they have "editaccount" rights)
	//////////////////////////////////////////////////////////////////////////////
	switch ($id1) {
	case 'auto_accept':
	case 'canadmin':
	case 'editaccount':
	case 'verified':
	case 'verified_by_admin':
	case 'contactmethod':
	case 'max_relation_path':
	case 'comment':
		if (WT_USER_IS_ADMIN) {
			set_user_setting($id2, $id1, $value);
			ok();
		} else {
			// Not allowed
			fail();
		}
	case 'defaulttab':
	case 'language':
	case 'visible_online':
		if (WT_USER_IS_ADMIN || WT_USER==$id2 && get_user_setting($id2, 'editaccount')) {
			set_user_setting($id2, $id1, $value);
			ok();
		} else {
			// Not allowed
			fail();
		}
	default:
		// An unrecognised setting
		fail();
	}

default:
	// An unrecognised table
	fail();
}
