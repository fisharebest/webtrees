<?php
// User and Authentication functions
//
// This file contains functions for working with users and authenticating them.
// It also handles the internal mail messages, news/journal, and storage of My Page
// customizations.  Assumes that a database connection has already been established.
//
// You can extend webtrees to work with other systems by implementing the functions in this file.
// Other possible options are to use LDAP for authentication.
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
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

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

// authenticate a username and password
//
// On success, store the user-id in the session and return it
// On failure, return an error code
function authenticateUser($user_name, $password) {
	global $WT_SESSION;
	
	// If no cookies are available, then we cannot log in.
	if (!isset($_COOKIE[WT_SESSION_NAME])) {
		return -5;
	}

	if ($user_id=get_user_id($user_name)) {
		if (check_user_password($user_id, $password)) {
			$is_admin=get_user_setting($user_id, 'canadmin');
			$verified=get_user_setting($user_id, 'verified');
			$approved=get_user_setting($user_id, 'verified_by_admin');
			if ($verified && $approved || $is_admin) {
				// Whenever we change our authorisation level change the session ID
				Zend_Session::regenerateId();
				$WT_SESSION->wt_user = $user_id;
				AddToLog('Login successful ->'.$user_name.'<-', 'auth');
				return $user_id;
			} elseif (!$is_admin && !$verified) {
				AddToLog('Login failed ->'.$user_name.'<- not verified', 'auth');
				return -1;
			} elseif (!$is_admin && !$approved) {
				AddToLog('Login failed ->'.$user_name.'<- not approved', 'auth');
				return -2;
			}
		} else {
			AddToLog('Login failed ->'.$user_name.'<- bad password', 'auth');
			return -3;
		}
	}
	AddToLog('Login failed ->'.$user_name.'<- bad username', 'auth');
	return -4;
}

/**
 * logs a user out of the system
 * @param string $user_id logout a specific user
 */
function userLogout($user_id) {
	AddToLog('Logout '.getUserName($user_id), 'auth');
	// If we are logging ourself out, then end our session too.
	if (WT_USER_ID==$user_id) {
		Zend_Session::destroy();
	}
}

/**
 * get the current user's ID and Name
 *
 * Returns 0 and NULL if we are not logged in.
 *
 * If you want to embed webtrees within a content management system, you would probably
 * rewrite these functions to extract the data from the parent system, and then
 * populate webtrees' user/user_setting/user_gedcom_setting tables as appropriate.
 *
 */

function getUserId() {
	global $WT_SESSION;

	return (int)($WT_SESSION->wt_user);
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
		return get_user_setting($user_id, 'canadmin');
	} else {
		return false;
	}
}

/**
 * check if given username is an admin for the given gedcom
 */
function userGedcomAdmin($user_id=WT_USER_ID, $ged_id=WT_GED_ID) {
	if ($user_id) {
		return WT_Tree::get($ged_id)->userPreference($user_id, 'canedit')=='admin' || userIsAdmin($user_id);
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
			$tmp=WT_Tree::get($ged_id)->userPreference($user_id, 'canedit');
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

	if ($user_id) {
		if (userIsAdmin($user_id)) {
			return true;
		} else {
			$tmp=WT_Tree::get($ged_id)->userPreference($user_id, 'canedit');
			return $tmp=='admin' || $tmp=='accept' || $tmp=='edit';
		}
	} else {
		return false;
	}
}

// Get the full name for a user
function getUserFullName($user_id) {
	return WT_DB::prepare("SELECT SQL_CACHE real_name FROM `##user` WHERE user_id=?")->execute(array($user_id))->fetchOne();
}

// Set the full name for a user
function setUserFullName($user_id, $real_name) {
	return WT_DB::prepare("UPDATE `##user` SET real_name=? WHERE user_id=?")->execute(array($real_name, $user_id));
}

// Get the email for a user
function getUserEmail($user_id) {
	return WT_DB::prepare("SELECT SQL_CACHE email FROM `##user` WHERE user_id=?")->execute(array($user_id))->fetchOne();
}

// Set the email for a user
function setUserEmail($user_id, $email) {
	return WT_DB::prepare("UPDATE `##user` SET email=? WHERE user_id=?")->execute(array($email, $user_id));
}

// add a message into the log-file
// Note that while transfering data from PhpGedView to WT, we delete the WT users and
// replace with PhpGedView users.  Hence the current user_id is not always available.
function AddToLog($log_message, $log_type='error') {
	global $WT_REQUEST;
	WT_DB::prepare(
		"INSERT INTO `##log` (log_type, log_message, ip_address, user_id, gedcom_id) VALUES (?, ?, ?, ?, ?)"
	)->execute(array(
		$log_type,
		$log_message,
		$WT_REQUEST->getClientIp(),
		defined('WT_USER_ID') && WT_USER_ID && WT_SCRIPT_NAME!='admin_pgv_to_wt.php' ? WT_USER_ID : null,
		defined('WT_GED_ID') ? WT_GED_ID : null
	));
}

//----------------------------------- AddToSearchLog
//-- requires a string to add into the searchlog-file
function AddToSearchLog($log_message, $geds) {
	global $WT_REQUEST;
	foreach (WT_Tree::getAll() as $tree) {
		WT_DB::prepare(
			"INSERT INTO `##log` (log_type, log_message, ip_address, user_id, gedcom_id) VALUES ('search', ?, ?, ?, ?)"
		)->execute(array(
			(count(WT_Tree::getAll())==count($geds) ? 'Global search: ' : 'Gedcom search: ').$log_message,
			$WT_REQUEST->getClientIp(),
			WT_USER_ID ? WT_USER_ID : null,
			$tree->tree_id
		));
	}
}

//----------------------------------- addMessage
//-- stores a new message in the database
function addMessage($message) {
	global $TEXT_DIRECTION, $WEBTREES_EMAIL, $WT_REQUEST;

	$user_id_from=get_user_id($message['from']);
	$user_id_to  =get_user_id($message['to']);

	require_once WT_ROOT.'includes/functions/functions_mail.php';

	// Switch to the "from" user's language
	WT_I18N::init(get_user_setting($user_id_from, 'language'));

	//-- setup the message body for the "from" user
	$copy_email = $message['body'];
	if (isset($message['from_name']))
		$copy_email = WT_I18N::translate('Your Name:')." ".$message['from_name']."\r\n".WT_I18N::translate('Email Address:')." ".$message['from_email']."\r\n\r\n".$copy_email;
	if (!empty($message['url'])) {
		if (strpos($message['url'],WT_SERVER_NAME.WT_SCRIPT_PATH)!==0) {
			$message['url']=WT_SERVER_NAME.WT_SCRIPT_PATH.$message['url'];
		}
		$copy_email .= "\r\n\r\n--------------------------------------\r\n\r\n".WT_I18N::translate('This message was sent while viewing the following URL: ')."\r\n".$message['url']."\r\n";
	}
	$copy_email .= "\r\n=--------------------------------------=\r\nIP ADDRESS: ".$WT_REQUEST->getClientIp()."\r\n";
	$copy_email .= "LANGUAGE: ".WT_LOCALE."\r\n";
	$copy_subject = "[".WT_I18N::translate('webtrees Message').($TEXT_DIRECTION=='ltr'?"] ":" [").$message['subject'];
	$from ='';
	if (!$user_id_from) {
		$from = $message['from'];
		$copy_email = WT_I18N::translate('You sent the following message to a webtrees administrator:')."\r\n\r\n".$copy_email;
		$fromFullName = $message['from'];
	} else {
		$fromFullName = getUserFullName($user_id_from);
		$from = hex4email($fromFullName, 'UTF-8')." <".getUserEmail($user_id_from).">";
		$toFullName=getUserFullName($user_id_to);
		$copy_email = WT_I18N::translate('You sent the following message to a webtrees user:').' '.$toFullName."\r\n\r\n".$copy_email;

	}
	if ($message['method']!='messaging') {
		$oryginal_subject = "[".WT_I18N::translate('webtrees Message').($TEXT_DIRECTION=='ltr'?"] ":" [").$message['subject'];
		if (!$user_id_from) {
			$oryginal_email = WT_I18N::translate('The following message has been sent to your webtrees user account from ');
			if (!empty($message['from_name'])) {
				$oryginal_email .= $message['from_name']."\r\n\r\n".$message['body'];
			} else {
				$oryginal_email .= $from."\r\n\r\n".$message['body'];
			}
		} else {
			$oryginal_email = WT_I18N::translate('The following message has been sent to your webtrees user account from ');
			$oryginal_email .= $fromFullName."\r\n\r\n".$message['body'];
		}
		if (!isset($message['no_from'])) {
			if (stristr($from, $WEBTREES_EMAIL)) {
				$from = getUserEmail(get_gedcom_setting(WT_GED_ID, 'WEBMASTER_USER_ID'));
			}
			// copy messages should be from:  $WEBTREES_EMAIL
			$copy_from = $WEBTREES_EMAIL;
			if (!empty($copy_from)) {
				// send the copy message to sender
				if (!webtreesMail($from, $copy_from, $copy_subject, $copy_email)) {
					return false;
				}
			}
		}
	}

	//-- Load the "to" users language
	WT_I18N::init(get_user_setting($user_id_to, 'language'));
	if (isset($message['from_name']))
		$message['body'] = WT_I18N::translate('Your Name:')." ".$message['from_name']."\r\n".WT_I18N::translate('Email Address:')." ".$message['from_email']."\r\n\r\n".$message['body'];
	if (!userIsAdmin($user_id_from)) {
		if (!empty($message['url']))
			$message['body'] .= "\r\n\r\n--------------------------------------\r\n\r\n".WT_I18N::translate('This message was sent while viewing the following URL: ')."\r\n".$message['url']."\r\n";
		$message['body'] .= "\r\n=--------------------------------------=\r\nIP ADDRESS: ".$WT_REQUEST->getClientIp()."\r\n";
		$message['body'] .= "LANGUAGE: ".WT_LOCALE."\r\n";
	}
	if (empty($message['created']))
		$message['created'] = gmdate ("D, d M Y H:i:s T");
	if ($message['method']!='messaging3' && $message['method']!='mailto' && $message['method']!='none') {
		WT_DB::prepare("INSERT INTO `##message` (sender, ip_address, user_id, subject, body) VALUES (? ,? ,? ,? ,?)")
			->execute(array($message['from'], $WT_REQUEST->getClientIp(), get_user_id($message['to']), $message['subject'], $message['body']));
	}
	if ($message['method']!='messaging') {
		$oryginal_subject = "[".WT_I18N::translate('webtrees Message').($TEXT_DIRECTION=='ltr'?"] ":" [").$message['subject'];
		if (!$user_id_from) {
			$oryginal_email = WT_I18N::translate('The following message has been sent to your webtrees user account from ');
			if (!empty($message['from_name'])) {
				$oryginal_email .= $message['from_name']."\r\n\r\n".$message['body'];
			} else {
				$oryginal_email .= $from."\r\n\r\n".$message['body'];
			}
		} else {
			$oryginal_email = WT_I18N::translate('The following message has been sent to your webtrees user account from ');
			$oryginal_email .= $fromFullName."\r\n\r\n".$message['body'];
		}
		$toFullName=getUserFullName($user_id_to);
		$to = hex4email($toFullName, 'UTF-8'). " <".getUserEmail($user_id_to).">";
		if (getUserEmail($user_id_to)) {
			// send the original message
			if (!webtreesMail($to, $from, $oryginal_subject, $oryginal_email)) {
				return false;
			}
		}
	}

	WT_I18N::init(WT_LOCALE); // restore language settings if needed

	return true;
}

//-- deletes a message in the database
function deleteMessage($message_id) {
	WT_DB::prepare("DELETE FROM `##message` WHERE message_id=?")->execute(array($message_id));
}

//-- Return an array of a users messages
function getUserMessages($user_id) {
	return
		WT_DB::prepare("SELECT message_id, sender, subject, body, UNIX_TIMESTAMP(created) AS created FROM `##message` WHERE user_id=? ORDER BY message_id DESC")
		->execute(array($user_id))
		->fetchAll();
}

/**
 * Adds a news item to the database
 *
 * This function adds a news item represented by the $news array to the database.
 * If the $news array has an ['id'] field then the function assumes that it is
 * as update of an older news item.
 *
 * @author John Finlay
 * @param array $news a news item array
 */
function addNews($news) {
	if (array_key_exists('id', $news)) {
		WT_DB::prepare("UPDATE `##news` SET subject=?, body=?, updated=FROM_UNIXTIME(?) WHERE news_id=?")
		->execute(array($news['title'], $news['text'], $news['date'], $news['id']));
	} else {
		WT_DB::prepare("INSERT INTO `##news` (user_id, gedcom_id, subject, body) VALUES (NULLIF(?, ''), NULLIF(?, '') ,? ,?)")
		->execute(array($news['user_id'], $news['gedcom_id'],  $news['title'], $news['text']));
	}
}

/**
 * Deletes a news item from the database
 *
 * @author John Finlay
 * @param int $news_id the id number of the news item to delete
 */
function deleteNews($news_id) {
	return (bool)WT_DB::prepare("DELETE FROM `##news` WHERE news_id=?")->execute(array($news_id));
}

// Gets the news items for the given user or gedcom
function getUserNews($user_id) {
	$rows=
		WT_DB::prepare("SELECT SQL_CACHE news_id, user_id, gedcom_id, UNIX_TIMESTAMP(updated) AS updated, subject, body FROM `##news` WHERE user_id=? ORDER BY updated DESC")
		->execute(array($user_id))
		->fetchAll();

	$news=array();
	foreach ($rows as $row) {
		$news[$row->news_id]=array(
			'id'=>$row->news_id,
			'user_id'=>$row->user_id,
			'gedcom_id'=>$row->gedcom_id,
			'date'=>$row->updated,
			'title'=>$row->subject,
			'text'=>$row->body,
		);
	}
	return $news;
}

function getGedcomNews($gedcom_id) {
	$rows=
		WT_DB::prepare("SELECT SQL_CACHE news_id, user_id, gedcom_id, UNIX_TIMESTAMP(updated) AS updated, subject, body FROM `##news` WHERE gedcom_id=? ORDER BY updated DESC")
		->execute(array($gedcom_id))
		->fetchAll();

	$news=array();
	foreach ($rows as $row) {
		$news[$row->news_id]=array(
			'id'=>$row->news_id,
			'user_id'=>$row->user_id,
			'gedcom_id'=>$row->gedcom_id,
			'date'=>$row->updated,
			'title'=>$row->subject,
			'text'=>$row->body,
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
	$row=
		WT_DB::prepare("SELECT SQL_CACHE news_id, user_id, gedcom_id, UNIX_TIMESTAMP(updated) AS updated, subject, body FROM `##news` WHERE news_id=?")
		->execute(array($news_id))
		->fetchOneRow();

	if ($row) {
		return array(
			'id'=>$row->news_id,
			'user_id'=>$row->user_id,
			'gedcom_id'=>$row->gedcom_id,
			'date'=>$row->updated,
			'title'=>$row->subject,
			'text'=>$row->body,
		);
	} else {
		return null;
	}
}
