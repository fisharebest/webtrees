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

Zend_Session::writeClose();

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
		if ($value=='') {
			// Delete the existing value
			$value=null;
		} elseif (!is_numeric($value)) {
			fail();
		}
		break;
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
		if ($value=='') {
			// Delete the existing value
			$value=null;
		} elseif (!preg_match('/^[0-9]+[KMG]$/', $value)) {
			// A number must be followed by K, M or G.
			fail();
		}
		break;
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
			WT_Site::preference($id1, $value);
		}
		fail();
	default:
		// An unrecognised setting
		fail();
	}

	// Authorised and valid - make update
	WT_Site::preference($id1, $value);
	ok();

case 'site_access_rule':
	//////////////////////////////////////////////////////////////////////////////
	// Table name: WT_SITE_ACCESS_RULE
	// ID format:  site_access_rule-{column_name}-{user_id}
	//////////////////////////////////////////////////////////////////////////////

	if (!WT_USER_IS_ADMIN) {
		fail();
	}
	switch ($id1) {
	case 'ip_address_start':
	case 'ip_address_end':
		WT_DB::prepare("UPDATE `##site_access_rule` SET {$id1}=INET_ATON(?) WHERE site_access_rule_id=?")
			->execute(array($value, $id2));
		$value=WT_DB::prepare(
			"SELECT INET_NTOA({$id1}) FROM `##site_access_rule` WHERE site_access_rule_id=?"
		)->execute(array($id2))->fetchOne();
		ok();
		break;
	case 'user_agent_pattern':
	case 'rule':
	case 'comment':
		WT_DB::prepare("UPDATE `##site_access_rule` SET {$id1}=? WHERE site_access_rule_id=?")
			->execute(array($value, $id2));
		ok();
	}
	fail();

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
	WT_Tree::get($id2)->userPreference($id1, $id3, $value);
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
	case 'verified_by_admin':
		// Approving for the first time?  Send a confirmation email
		if ($value && get_user_setting($id1, $id2)!=$value && get_user_setting($id1, 'sessiontime')==0) {
			require_once WT_ROOT.'includes/functions/functions_mail.php';
			WT_I18N::init(get_user_setting($id1, 'language'));
			webtreesMail(
				getUserEmail($id1),
				$WEBTREES_EMAIL,
				WT_I18N::translate('Approval of account at %s', WT_SERVER_NAME.WT_SCRIPT_PATH),
				WT_I18N::translate('The administrator at the webtrees site %s has approved your application for an account.  You may now login by accessing the following link: %s', WT_SERVER_NAME.WT_SCRIPT_PATH, WT_SERVER_NAME.WT_SCRIPT_PATH)
			);
		}
		break;
	case 'auto_accept':
	case 'editaccount':
	case 'verified':
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
