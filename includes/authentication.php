<?php
/**
 * User and Authentication functions
 *
 * This file contains functions for working with users and authenticating them.
 * It also handles the internal mail messages, favorites, news/journal, and storage of My Page
 * customizations.  Assumes that a database connection has already been established.
 *
 * You can extend webtrees to work with other systems by implementing the functions in this file.
 * Other possible options are to use LDAP for authentication.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
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
 * @subpackage DB
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_AUTHENTICATION_PHP', '');

/**
 * authenticate a username and password
 *
 * This function takes the given <var>$username</var> and <var>$password</var> and authenticates
 * them against the database.  The passwords are encrypted using the crypt() function.
 * The username is stored in the <var>$_SESSION["wt_user"]</var> session variable.
 * @param string $user_name the username for the user attempting to login
 * @param string $password the plain text password to test
 * @param boolean $basic true if the userName and password were retrived via Basic HTTP authentication. Defaults to false. At this point, this is only used for logging
 * @return the user_id if sucessful, false otherwise
 */
function authenticateUser($user_name, $password, $basic=false) {
	// If we were already logged in, log out first
	if (getUserId()) {
		userLogout(getUserId());
	}

	if ($user_id=get_user_id($user_name)) {
		$dbpassword=get_user_password($user_id);
		if (crypt($password, $dbpassword)==$dbpassword) {
			if (get_user_setting($user_id, 'verified')=='yes' && get_user_setting($user_id, 'verified_by_admin')=='yes' || get_user_setting($user_id, 'canadmin')=='Y') {
				set_user_setting($user_id, 'loggedin', 'Y');
				//-- reset the user's session
				$_SESSION = array();
				$_SESSION['wt_user'] = $user_id;
				AddToLog(($basic ? 'Basic HTTP Authentication' :'Login'). ' Successful', 'auth');
				return $user_id;
			}
		}
	}
	AddToLog(($basic ? 'Basic HTTP Authentication' : 'Login').' Failed ->'.$user_name.'<-', 'auth');
	return false;
}

/**
 * authenticate a username and password using Basic HTTP Authentication
 *
 * This function uses authenticateUser(), for authentication, but retrives the userName and password provided via basic auth.
 * @return bool return true if the user is already logged in or the basic HTTP auth username and password credentials match a user in the database return false if they don't
 * @TODO Security audit for this functionality
 * @TODO Do we really need a return value here?
 * @TODO should we reauthenticate the user even if already logged in?
 * @TODO do we need to set the user language and other jobs done in login.php? Should that loading be moved to a function called from the authenticateUser function?
 */
function basicHTTPAuthenticateUser() {
	$user_id = getUserId();
	if (empty($user_id)){ //not logged in.
		if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])
				|| (! authenticateUser($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'], true))) {
			header('WWW-Authenticate: Basic realm="' . i18n::translate('webtrees Authentication System') . '"');
			header('HTTP/1.0 401 Unauthorized');
			echo i18n::translate('You must enter a valid login ID and password to access this resource') ;
			exit;
		}
	} else { //already logged in or successful basic authentication
		return true; //probably not needed
	}
}

/**
 * logs a user out of the system
 * @param string $user_id	logout a specific user
 */
function userLogout($user_id) {
	set_user_setting($user_id, 'loggedin', 'N');
if ($user_id != "Anonymous" and $user_id != "") {
	AddToLog('Logout '.getUserName($user_id), 'auth');
	}
	// If we are logging ourself out, then end our session too.
	if (WT_USER_ID==$user_id) {
		session_destroy();
	}
}

/**
 * Updates the login time in the database of the given user
 * The login time is used to automatically logout users who have been
 * inactive for the defined session time
 * @param string $username	the username to update the login info for
 */
function userUpdateLogin($user_id) {
	set_user_setting($user_id, 'sessiontime', time());
}

/**
 * get the current user's ID and Name
 *
 * Returns 0 and NULL if we are not logged in.
 *
 * If you want to embed PGV within a content management system, you would probably
 * rewrite these functions to extract the data from the parent system, and then
 * populate PGV's user/user_setting/user_gedcom_setting tables as appropriate.
 *
 */

function getUserId() {
	if (empty($_SESSION['wt_user'])) {
		return 0;
	} else {
		return $_SESSION['wt_user'];
	}
}

function getUserName() {
	if (getUserID()) {
		return get_user_name(getUserID());
	} else {
		return null;
	}
}

/**
 * check if given username is an admin
 */
function userIsAdmin($user_id=WT_USER_ID) {
	if ($user_id) {
		return get_user_setting($user_id, 'canadmin')=='Y';
	} else {
		return false;
	}
}

/**
 * check if given username is an admin for the given gedcom
 */
function userGedcomAdmin($user_id=WT_USER_ID, $ged_id=WT_GED_ID) {
	if ($user_id) {
		return get_user_gedcom_setting($user_id, $ged_id, 'canedit')=='admin' || userIsAdmin($user_id);
	} else {
		return false;
	}
}

/**
 * check if the given user has access privileges on this gedcom
 */
function userCanAccess($user_id=WT_USER_ID, $ged_id=WT_GED_ID) {
	if ($user_id) {
		if (userIsAdmin($user_id)) {
			return true;
		} else {
			$tmp=get_user_gedcom_setting($user_id, $ged_id, 'canedit');
			return $tmp=='admin' || $tmp=='accept' || $tmp=='edit' || $tmp=='access';
		}
	} else {
		return false;
	}
}

/**
 * check if the given user has write privileges for the given gedcom
 */
function userCanEdit($user_id=WT_USER_ID, $ged_id=WT_GED_ID) {
	global $ALLOW_EDIT_GEDCOM;

	if ($ALLOW_EDIT_GEDCOM && $user_id) {
		if (userIsAdmin($user_id)) {
			return true;
		} else {
			$tmp=get_user_gedcom_setting($user_id, $ged_id, 'canedit');
			return $tmp=='admin' || $tmp=='accept' || $tmp=='edit';
		}
	} else {
		return false;
	}
}

/**
 * check if the given user can accept changes for the given gedcom
 *
 * takes a username and checks if the user has write privileges to
 * change the gedcom data and accept changes
 * @param string $username	the username of the user check privileges
 * @return boolean true if user can accept false if user cannot accept
 */
function userCanAccept($user_id=WT_USER_ID, $ged_id=WT_GED_ID) {
	global $ALLOW_EDIT_GEDCOM;

	// An admin can always accept changes, even if editing is disabled
	if (userGedcomAdmin($user_id, $ged_id)) {
		return true;
	}
	if ($ALLOW_EDIT_GEDCOM) {
		$tmp=get_user_gedcom_setting($user_id, $ged_id, 'canedit');
		return $tmp=='admin' || $tmp=='accept';
	} else {
		return false;
	}
}

/**
 * Should user's changed automatically be accepted
 */
function userAutoAccept($user_id=WT_USER_ID) {
	return get_user_setting($user_id, 'auto_accept')=='Y';
}

/**
 * Does an admin user exist?  Used to redirect to install/config page
 * during initial setup.
 */
function adminUserExists() {
	return admin_user_exists();
}

// Get the full name for a user
function getUserFullName($user_id) {
	global $TBLPREFIX;

	return WT_DB::prepare("SELECT real_name FROM {$TBLPREFIX}user WHERE user_id=?")->execute(array($user_id))->fetchOne();
}

// Set the full name for a user
function setUserFullName($user_id, $real_name) {
	global $TBLPREFIX;

	return WT_DB::prepare("UPDATE {$TBLPREFIX}user SET real_name=? WHERE user_id=?")->execute(array($real_name, $user_id));
}

// Get the email for a user
function getUserEmail($user_id) {
	global $TBLPREFIX;

	return WT_DB::prepare("SELECT email FROM {$TBLPREFIX}user WHERE user_id=?")->execute(array($user_id))->fetchOne();
}

// Set the email for a user
function setUserEmail($user_id, $email) {
	global $TBLPREFIX;

	return WT_DB::prepare("UPDATE {$TBLPREFIX}user SET email=? WHERE user_id=?")->execute(array($email, $user_id));
}

// Get the root person for this gedcom
function getUserRootId($user_id, $ged_id) {
	if ($user_id) {
		return get_user_gedcom_setting(WT_USER_ID, WT_GED_ID, 'rootid');
	} else {
		return getUserGedcomId($user_id, $ged_id);
	}
}

// Get the user's ID in the given gedcom
function getUserGedcomId($user_id, $ged_id) {
	if ($user_id) {
		return get_user_gedcom_setting(WT_USER_ID, WT_GED_ID, 'gedcomid');
	} else {
		return null;
	}
}

/**
 * add a message into the log-file
 */
function AddToLog($log_message, $log_type='error') {
	global $TBLPREFIX, $argc;

	WT_DB::prepare(
		"INSERT INTO {$TBLPREFIX}log (log_type, log_message, ip_address, user_id, gedcom_id) VALUES (?, ?, ?, ?, ?)"
	)->execute(array(
		$log_type,
		$log_message,
		$argc ? 'cli' : $_SERVER['REMOTE_ADDR'],
		getUserId() ? getUserId() : null,
		defined('WT_GED_ID') ? WT_GED_ID : null // logs raised before we select the gedcom won't have this.
	));
}

//----------------------------------- AddToSearchLog
//-- requires a string to add into the searchlog-file
function AddToSearchLog($log_message, $geds) {
	global $TBLPREFIX;

	$all_geds=get_all_gedcoms();
	foreach ($geds as $ged_id=>$ged_name) {
		WT_DB::prepare(
			"INSERT INTO {$TBLPREFIX}log (log_type, log_message, ip_address, user_id, gedcom_id) VALUES ('search', ?, ?, ?, ?)"
		)->execute(array(
			(count($all_geds)==count($geds) ? 'Global search: ' : 'Gedcom search: ').$log_message,
			$_SERVER['REMOTE_ADDR'],
			WT_USER_ID ? WT_USER_ID : null,
			$ged_id
		));
	}
}

//----------------------------------- AddToChangeLog
//-- requires a string to add into the changelog-file
function AddToChangeLog($log_message, $ged_id=WT_GED_ID) {
	global $TBLPREFIX;

	WT_DB::prepare(
		"INSERT INTO {$TBLPREFIX}log (log_type, log_message, ip_address, user_id, gedcom_id) VALUES ('change', ?, ?, ?, ?)"
	)->execute(array(
		$log_message,
		$_SERVER['REMOTE_ADDR'],
		WT_USER_ID ? WT_USER_ID : null,
		$ged_id
	));
}

//----------------------------------- addMessage
//-- stores a new message in the database
function addMessage($message) {
	global $TBLPREFIX, $WT_STORE_MESSAGES, $SERVER_URL;
	global $TEXT_DIRECTION;
	global $WEBTREES_EMAIL;

	//-- do not allow users to send a message to themselves
	if ($message["from"]==$message["to"]) {
		return false;
	}

	$user_id_from=get_user_id($message['from']);
	$user_id_to  =get_user_id($message['to']);

	require_once WT_ROOT.'includes/functions/functions_mail.php';

	if (!$user_id_to) {
		//-- the to user must be a valid user in the system before it will send any mails
		return false;
	}

	// Switch to the "from" user's language
	i18n::init(get_user_setting($user_id_from, 'language'));

	//-- setup the message body for the "from" user
	$email2 = $message["body"];
	if (isset($message["from_name"]))
		$email2 = i18n::translate('Your Name:')." ".$message["from_name"]."\r\n".i18n::translate('Email Address:')." ".$message["from_email"]."\r\n\r\n".$email2;
	if (!empty($message["url"]))
		$email2 .= "\r\n\r\n--------------------------------------\r\n\r\n".i18n::translate('This message was sent while viewing the following URL: ')."\r\n".$SERVER_URL.$message["url"]."\r\n";
	$email2 .= "\r\n=--------------------------------------=\r\nIP ADDRESS: ".$_SERVER['REMOTE_ADDR']."\r\n";
	$email2 .= "DNS LOOKUP: ".gethostbyaddr($_SERVER['REMOTE_ADDR'])."\r\n";
	$email2 .= "LANGUAGE: ".WT_LOCALE."\r\n";
	$subject2 = "[".i18n::translate('webtrees Message').($TEXT_DIRECTION=="ltr"?"] ":" [").$message["subject"];
	$from ="";
	if (!$user_id_from) {
		$from = $message["from"];
		$email2 = i18n::translate('You sent the following message to a webtrees administrator:')."\r\n\r\n".$email2;
		$fromFullName = $message["from"];
	} else {
		$fromFullName = getUserFullName($user_id_from);
		if (!get_site_setting('SIMPLE_MAIL'))
			$from = hex4email($fromFullName, 'UTF-8'). " <".getUserEmail($user_id_from).">";
		else
			$from = getUserEmail($user_id_from);
		$email2 = i18n::translate('You sent the following message to a webtrees user:')."\r\n\r\n".$email2;

	}
	if ($message["method"]!="messaging") {
		$subject1 = "[".i18n::translate('webtrees Message').($TEXT_DIRECTION=="ltr"?"] ":" [").$message["subject"];
		if (!$user_id_from) {
			$email1 = i18n::translate('The following message has been sent to your webtrees user account from ');
			if (!empty($message["from_name"])) {
				$email1 .= $message["from_name"]."\r\n\r\n".$message["body"];
			} else {
				$email1 .= $from."\r\n\r\n".$message["body"];
			}
		} else {
			$email1 = i18n::translate('The following message has been sent to your webtrees user account from ');
			$email1 .= $fromFullName."\r\n\r\n".$message["body"];
		}
		if (!isset($message["no_from"])) {
			if (stristr($from, $WEBTREES_EMAIL)){
				$from = getUserEmail(get_gedcom_setting(WT_GED_ID, 'WEBMASTER_USER_ID'));
			}
			if (!$user_id_from) {
				$header2 = $WEBTREES_EMAIL;
			} elseif (isset($to)) {
				$header2 = $to;
			}
			if (!empty($header2)) {
				pgvMail($from, $header2, $subject2, $email2);
			}
		}
	}

	//-- Load the "to" users language
	i18n::init(get_user_setting($user_id_to, 'language'));
	if (isset($message["from_name"]))
		$message["body"] = i18n::translate('Your Name:')." ".$message["from_name"]."\r\n".i18n::translate('Email Address:')." ".$message["from_email"]."\r\n\r\n".$message["body"];
	//-- [ webtrees-Feature Requests-1588353 ] Supress admin IP address in Outgoing PGV Email
	if (!userIsAdmin($user_id_from)) {
		if (!empty($message["url"]))
			$message["body"] .= "\r\n\r\n--------------------------------------\r\n\r\n".i18n::translate('This message was sent while viewing the following URL: ')."\r\n".$SERVER_URL.$message["url"]."\r\n";
		$message["body"] .= "\r\n=--------------------------------------=\r\nIP ADDRESS: ".$_SERVER['REMOTE_ADDR']."\r\n";
		$message["body"] .= "DNS LOOKUP: ".gethostbyaddr($_SERVER['REMOTE_ADDR'])."\r\n";
		$message["body"] .= "LANGUAGE: ".WT_LOCALE."\r\n";
	}
	if (empty($message["created"]))
		$message["created"] = gmdate ("D, d M Y H:i:s T");
	if ($WT_STORE_MESSAGES && ($message["method"]!="messaging3" && $message["method"]!="mailto" && $message["method"]!="none")) {
		WT_DB::prepare("INSERT INTO {$TBLPREFIX}messages (m_id, m_from, m_to, m_subject, m_body, m_created) VALUES (?, ? ,? ,? ,? ,?)")
			->execute(array(get_next_id("messages", "m_id"), $message["from"], $message["to"], $message["subject"], $message["body"], $message["created"]));
	}
	if ($message["method"]!="messaging") {
		$subject1 = "[".i18n::translate('webtrees Message').($TEXT_DIRECTION=="ltr"?"] ":" [").$message["subject"];
		if (!$user_id_from) {
			$email1 = i18n::translate('The following message has been sent to your webtrees user account from ');
			if (!empty($message["from_name"]))
				$email1 .= $message["from_name"]."\r\n\r\n".$message["body"];
			else
				$email1 .= $from."\r\n\r\n".$message["body"];
		} else {
			$email1 = i18n::translate('The following message has been sent to your webtrees user account from ');
			$email1 .= $fromFullName."\r\n\r\n".$message["body"];
		}
		if (!$user_id_to) {
			//-- the to user must be a valid user in the system before it will send any mails
			return false;
		} else {
			$toFullName=getUserFullName($user_id_to);
			if (!get_site_setting('SIMPLE_MAIL'))
				$to = hex4email($toFullName, 'UTF-8'). " <".getUserEmail($user_id_to).">";
			else
				$to = getUserEmail($user_id_to);
		}
		if (getUserEmail($user_id_to))
			pgvMail($to, $from, $subject1, $email1);
	}

	i18n::init(WT_LOCALE); // restore language settings if needed

	return true;
}

//----------------------------------- deleteMessage
//-- deletes a message in the database
function deleteMessage($message_id) {
	global $TBLPREFIX;

	return (bool)WT_DB::prepare("DELETE FROM {$TBLPREFIX}messages WHERE m_id=?")->execute(array($message_id));
}

//----------------------------------- getUserMessages
//-- Return an array of a users messages
function getUserMessages($username) {
	global $TBLPREFIX;

	$rows=
		WT_DB::prepare("SELECT * FROM {$TBLPREFIX}messages WHERE m_to=? ORDER BY m_id DESC")
		->execute(array($username))
		->fetchAll();

	$messages=array();
	foreach ($rows as $row) {
		$messages[]=array(
			"id"=>$row->m_id,
			"to"=>$row->m_to,
			"from"=>$row->m_from,
			"subject"=>$row->m_subject,
			"body"=>$row->m_body,
			"created"=>$row->m_created
		);
	}
	return $messages;
}

/**
 * stores a new favorite in the database
 * @param array $favorite	the favorite array of the favorite to add
 */
function addFavorite($favorite) {
	global $TBLPREFIX;

	// -- make sure a favorite is added
	if (empty($favorite["gid"]) && empty($favorite["url"]))
		return false;

	//-- make sure this is not a duplicate entry
	$sql = "SELECT 1 FROM {$TBLPREFIX}favorites WHERE";
	if (!empty($favorite["gid"])) {
		$sql.=" fv_gid=?";
		$vars=array($favorite["gid"]);
	} else {
		$sql.=" fv_url=?";
		$vars=array($favorite["url"]);
	}
	$sql.=" AND fv_file=? AND fv_username=?";
	$vars[]=$favorite["file"];
	$vars[]=$favorite["username"];

	if (WT_DB::prepare($sql)->execute($vars)->fetchOne()) {
		return false;
	}

	//-- add the favorite to the database
	return (bool)
		WT_DB::prepare("INSERT INTO {$TBLPREFIX}favorites (fv_id, fv_username, fv_gid, fv_type, fv_file, fv_url, fv_title, fv_note) VALUES (?, ? ,? ,? ,? ,? ,? ,?)")
			->execute(array(get_next_id("favorites", "fv_id"), $favorite["username"], $favorite["gid"], $favorite["type"], $favorite["file"], $favorite["url"], $favorite["title"], $favorite["note"]));
}

/**
 * deleteFavorite
 * deletes a favorite in the database
 * @param int $fv_id	the id of the favorite to delete
 */
function deleteFavorite($fv_id) {
	global $TBLPREFIX;

	return (bool)
		WT_DB::prepare("DELETE FROM {$TBLPREFIX}favorites WHERE fv_id=?")
		->execute(array($fv_id));
}

/**
 * Get a user's favorites
 * Return an array of a users messages
 * @param string $username		the username to get the favorites for
 */
function getUserFavorites($username) {
	global $TBLPREFIX;

	$rows=
		WT_DB::prepare("SELECT * FROM {$TBLPREFIX}favorites WHERE fv_username=?")
		->execute(array($username))
		->fetchAll();

	$favorites = array();
	foreach ($rows as $row) {
		if (get_id_from_gedcom($row->fv_file)) { // If gedcom exists
			$favorites[]=array(
				"id"=>$row->fv_id,
				"username"=>$row->fv_username,
				"gid"=>$row->fv_gid,
				"type"=>$row->fv_type,
				"file"=>$row->fv_file,
				"title"=>$row->fv_title,
				"note"=>$row->fv_note,
				"url"=>$row->fv_url
			);
		}
	}
	return $favorites;
}

/**
 * get blocks for the given username
 *
 * retrieve the block configuration for the given user
 * if no blocks have been set yet, and the username is a valid user (not a gedcom) then try and load
 * the defaultuser blocks.
 * @param string $username	the username or gedcom name for the blocks
 * @return array	an array of the blocks.  The two main indexes in the array are "main" and "right"
 */
function getBlocks($username) {
	global $TBLPREFIX;

	$blocks = array();
	$blocks["main"] = array();
	$blocks["right"] = array();

	$rows=
		WT_DB::prepare("SELECT * FROM {$TBLPREFIX}blocks WHERE b_username=? ORDER BY b_location, b_order")
		->execute(array($username))
		->fetchAll();

	if ($rows) {
		foreach ($rows as $row) {
			if (!isset($row->b_config))
				$row->b_config="";
			if ($row->b_location=="main")
				$blocks["main"][$row->b_order] = array($row->b_name, @unserialize($row->b_config));
			if ($row->b_location=="right")
				$blocks["right"][$row->b_order] = array($row->b_name, @unserialize($row->b_config));
		}
	} else {
		if (get_user_id($username)) {
			//-- if no blocks found, check for a default block setting
			//$rows=
				WT_DB::prepare("SELECT * FROM {$TBLPREFIX}blocks WHERE b_username=? ORDER BY b_location, b_order")
				->execute(array('defaultuser'))
				->fetchAll();

			foreach ($rows as $row) {
				if (!isset($row->b_config))
					$row->b_config="";
				if ($row->b_location=="main")
					$blocks["main"][$row->b_order] = array($row->b_name, @unserialize($row->b_config));
				if ($row->b_location=="right")
					$blocks["right"][$row->b_order] = array($row->b_name, @unserialize($row->b_config));
			}
		}
	}
	return $blocks;
}

/**
 * Set Blocks
 *
 * Sets the blocks for a gedcom or user portal
 * the $setdefault parameter tells the program to also store these blocks as the blocks used by default
 * @param String $username the username or gedcom name to update the blocks for
 * @param array $ublocks the new blocks to set for the user or gedcom
 * @param boolean $setdefault	if true tells the program to also set these blocks as the blocks for the defaultuser
 */
function setBlocks($username, $ublocks, $setdefault=false) {
	global $TBLPREFIX;

	WT_DB::prepare("DELETE FROM {$TBLPREFIX}blocks WHERE b_username=? AND b_name!=?")
		->execute(array($username, 'faq'));

	if ($setdefault) {
		WT_DB::prepare("DELETE FROM {$TBLPREFIX}blocks WHERE b_username=?")
			->execute(array('defaultuser'));
	}

	$statement=WT_DB::prepare("INSERT INTO {$TBLPREFIX}blocks (b_id, b_username, b_location, b_order, b_name, b_config) VALUES (?, ?, ?, ?, ?, ?)");

	foreach($ublocks["main"] as $order=>$block) {
		$statement->execute(array(get_next_id("blocks", "b_id"), $username, 'main', $order, $block[0], serialize($block[1])));

		if ($setdefault) {
			$statement->execute(array(get_next_id("blocks", "b_id"), 'defaultuser', 'main', $order, $block[0], serialize($block[1])));
		}
	}
	foreach($ublocks["right"] as $order=>$block) {
		$statement->execute(array(get_next_id("blocks", "b_id"), $username, 'right', $order, $block[0], serialize($block[1])));

		if ($setdefault) {
			$statement->execute(array(get_next_id("blocks", "b_id"), 'defaultuser', 'right', $order, $block[0], serialize($block[1])));
		}
	}
}

/**
 * Adds a news item to the database
 *
 * This function adds a news item represented by the $news array to the database.
 * If the $news array has an ["id"] field then the function assumes that it is
 * as update of an older news item.
 *
 * @author John Finlay
 * @param array $news a news item array
 */
function addNews($news) {
	global $TBLPREFIX;

	if (!isset($news["date"]))
		$news["date"] = client_time();
	if (!empty($news["id"])) {
		// In case news items are added from usermigrate, it will also contain an ID.
		// So we check first if the ID exists in the database. If not, insert instead of update.
		$exists=
			WT_DB::prepare("SELECT 1 FROM {$TBLPREFIX}news where n_id=?")
			->execute(array($news["id"]))
			->fetchOne();

		if (!$exists) {
			return (bool)
				WT_DB::prepare("INSERT INTO {$TBLPREFIX}news (n_id, n_username, n_date, n_title, n_text) VALUES (?, ? ,? ,? ,?)")
				->execute(array($news["id"], $news["username"], $news["date"], $news["title"], $news["text"]));
		} else {
			return (bool)
				WT_DB::prepare("UPDATE {$TBLPREFIX}news SET n_date=?, n_title=? , n_text=? WHERE n_id=?")
				->execute(array($news["date"], $news["title"], $news["text"], $news["id"]));
		}
	} else {
		return (bool)
			WT_DB::prepare("INSERT INTO {$TBLPREFIX}news (n_id, n_username, n_date, n_title, n_text) VALUES (?, ? ,? ,? ,?)")
			->execute(array(get_next_id("news", "n_id"), $news["username"], $news["date"], $news["title"], $news["text"]));
	}
}

/**
 * Deletes a news item from the database
 *
 * @author John Finlay
 * @param int $news_id the id number of the news item to delete
 */
function deleteNews($news_id) {
	global $TBLPREFIX;

	return (bool)WT_DB::prepare("DELETE FROM {$TBLPREFIX}news WHERE n_id=?")->execute(array($news_id));
}

/**
 * Gets the news items for the given user or gedcom
 *
 * @param String $username the username or gedcom file name to get news items for
 */
function getUserNews($username) {
	global $TBLPREFIX;

	$rows=
		WT_DB::prepare("SELECT * FROM {$TBLPREFIX}news WHERE n_username=? ORDER BY n_date DESC")
		->execute(array($username))
		->fetchAll();

	$news=array();
	foreach ($rows as $row) {
		$news[$row->n_id]=array(
			"id"=>$row->n_id,
			"username"=>$row->n_username,
			"date"=>$row->n_date,
			"title"=>$row->n_title,
			"text"=>$row->n_text,
			"anchor"=>"article".$row->n_id
		);
	}
	return $news;
}

/**
 * Gets the news item for the given news id
 *
 * @param int $news_id the id of the news entry to get
 */
function getNewsItem($news_id) {
	global $TBLPREFIX;

	$row=
		WT_DB::prepare("SELECT * FROM {$TBLPREFIX}news WHERE n_id=?")
		->execute(array($news_id))
		->fetchOneRow();

	if ($row) {
		return array(
			"id"=>$row->n_id,
			"username"=>$row->n_username,
			"date"=>$row->n_date,
			"title"=>$row->n_title,
			"text"=>$row->n_text,
			"anchor"=>"article".$row->n_id
		);
	} else {
		return null;
	}
}

?>
