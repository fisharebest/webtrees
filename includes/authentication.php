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
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010 PGV Development Team.  All rights reserved.
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
use WT\User;

/**
 * Used in custom theme headers...
 *
 * @deprecated
 *
 * @param $user_id
 *
 * @return string
 */
function getUserFullName($user_id) {
	return User::find($user_id)->getRealName();
}

/**
 * Log a search query
 *
 * @param string    $log_message
 * @param integer[] $geds
 */
function AddToSearchLog($log_message, $geds) {
	global $WT_REQUEST;
	foreach (WT_Tree::getAll() as $tree) {
		WT_DB::prepare(
			"INSERT INTO `##log` (log_type, log_message, ip_address, user_id, gedcom_id) VALUES ('search', ?, ?, ?, ?)"
		)->execute(array(
			(count(WT_Tree::getAll()) == count($geds) ? 'Global search: ' : 'Gedcom search: ') . $log_message,
			$WT_REQUEST->getClientIp(),
			WT_USER_ID ? WT_USER_ID : null,
			$tree->tree_id
		));
	}
}

/**
 * Add a message to a user's inbox
 *
 * @param string[] $message
 *
 * @return bool
 */
function addMessage($message) {
	global $WT_TREE, $WT_REQUEST;

	$success = true;

	$sender    = User::findByIdentifier($message['from']);
	$recipient = User::findByIdentifier($message['to']);

	// Sender may not be a webtrees user
	if ($sender) {
		$sender_email     = $sender->getEmail();
		$sender_real_name = $sender->getRealName();
	} else {
		$sender_email     = $message['from'];
		$sender_real_name = $message['from_name'];
	}

	// Send a copy of the copy message back to the sender.
	if ($message['method'] != 'messaging') {
		// Switch to the sender’s language.
		if ($sender) {
			WT_I18N::init($sender->getPreference('language'));
		}

		$copy_email = $message['body'];
		if (!empty($message['url'])) {
			$copy_email .=
				WT_Mail::EOL . WT_Mail::EOL . '--------------------------------------' . WT_Mail::EOL .
				WT_I18N::translate('This message was sent while viewing the following URL: ') . $message['url'] . WT_Mail::EOL;
		}
		$copy_email .= WT_Mail::auditFooter();

		if ($sender) {
			// Message from a logged-in user
			$copy_email = WT_I18N::translate('You sent the following message to a webtrees user:') . ' ' . $recipient->getRealName() . WT_Mail::EOL . WT_Mail::EOL . $copy_email;
		} else {
			// Message from a visitor
			$copy_email = WT_I18N::translate('You sent the following message to a webtrees administrator:') . WT_Mail::EOL . WT_Mail::EOL . WT_Mail::EOL . $copy_email;
		}

		$success = $success && WT_Mail::send(// From:
				$WT_TREE, // To:
			$sender_email,
			$sender_real_name,
			// Reply-To:
			WT_Site::getPreference('SMTP_FROM_NAME'),
			$WT_TREE->getPreference('title'),
			// Message
			WT_I18N::translate('webtrees message') . ' - ' . $message['subject'],
			$copy_email
		);
	}

	// Switch to the recipient’s language.
	WT_I18N::init($recipient->getPreference('language'));
	if (isset($message['from_name'])) {
		$message['body'] =
			WT_I18N::translate('Your name:') . ' ' . $message['from_name'] . WT_Mail::EOL .
			WT_I18N::translate('Email address:') . ' ' . $message['from_email'] . WT_Mail::EOL . WT_Mail::EOL .
			$message['body'];
	}

	// Add another footer - unless we are an admin
	if (!Auth::isAdmin()) {
		if (!empty($message['url'])) {
			$message['body'] .=
				WT_Mail::EOL . WT_Mail::EOL .
				'--------------------------------------' . WT_Mail::EOL .
				WT_I18N::translate('This message was sent while viewing the following URL: ') . $message['url'] . WT_Mail::EOL;
		}
		$message['body'] .= WT_Mail::auditFooter();
	}

	if (empty($message['created'])) {
		$message['created'] = gmdate("D, d M Y H:i:s T");
	}

	if ($message['method'] != 'messaging3' && $message['method'] != 'mailto' && $message['method'] != 'none') {
		WT_DB::prepare("INSERT INTO `##message` (sender, ip_address, user_id, subject, body) VALUES (? ,? ,? ,? ,?)")
			->execute(array(
				$message['from'],
				$WT_REQUEST->getClientIp(),
				$recipient->getUserId(),
				$message['subject'],
				str_replace('<br>', '', $message['body']) // Remove the <br> that we added for the external email.  TODO: create different messages
			));
	}
	if ($message['method'] != 'messaging') {
		if ($sender) {
			$original_email = WT_I18N::translate('The following message has been sent to your webtrees user account from ');
			$original_email .= $sender->getRealName();
		} else {
			$original_email = WT_I18N::translate('The following message has been sent to your webtrees user account from ');
			if (!empty($message['from_name'])) {
				$original_email .= $message['from_name'];
			} else {
				$original_email .= $message['from'];
			}
		}
		$original_email .= WT_Mail::EOL . WT_Mail::EOL . $message['body'];

		$success = $success && WT_Mail::send(// From:
				$WT_TREE, // To:
			$recipient->getEmail(),
			$recipient->getRealName(),
			// Reply-To:
			$sender_email,
			$sender_real_name,
			// Message
			WT_I18N::translate('webtrees message') . ' - ' . $message['subject'],
			$original_email
		);
	}

	WT_I18N::init(WT_LOCALE); // restore language settings if needed

	return $success;
}

/**
 * Deletes a message in the database.
 *
 * @param integer $message_id
 */
function deleteMessage($message_id) {
	WT_DB::prepare("DELETE FROM `##message` WHERE message_id=?")->execute(array($message_id));
}

/**
 * Return an array of a users messages.
 *
 * @param integer $user_id
 *
 * @return stdClass[]
 */
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
 * @param integer $news_id the id number of the news item to delete
 *
 * @return boolean
 */
function deleteNews($news_id) {
	return (bool)WT_DB::prepare("DELETE FROM `##news` WHERE news_id=?")->execute(array($news_id));
}

/**
 * Gets the news items for the given user or gedcom.
 *
 * @param integer $user_id
 *
 * @return string[][]
 */
function getUserNews($user_id) {
	$rows =
		WT_DB::prepare("SELECT SQL_CACHE news_id, user_id, gedcom_id, UNIX_TIMESTAMP(updated) AS updated, subject, body FROM `##news` WHERE user_id=? ORDER BY updated DESC")
		->execute(array($user_id))
		->fetchAll();

	$news = array();
	foreach ($rows as $row) {
		$news[$row->news_id] = array(
			'id' => $row->news_id,
			'user_id' => $row->user_id,
			'gedcom_id' => $row->gedcom_id,
			'date' => $row->updated,
			'title' => $row->subject,
			'text' => $row->body,
		);
	}

	return $news;
}

/**
 * @param integer $gedcom_id
 *
 * @return string[][]
 */
function getGedcomNews($gedcom_id) {
	$rows=
		WT_DB::prepare("SELECT SQL_CACHE news_id, user_id, gedcom_id, UNIX_TIMESTAMP(updated) AS updated, subject, body FROM `##news` WHERE gedcom_id=? ORDER BY updated DESC")
		->execute(array($gedcom_id))
		->fetchAll();

	$news = array();
	foreach ($rows as $row) {
		$news[$row->news_id] = array(
			'id' => $row->news_id,
			'user_id' => $row->user_id,
			'gedcom_id' => $row->gedcom_id,
			'date' => $row->updated,
			'title' => $row->subject,
			'text' => $row->body,
		);
	}

	return $news;
}

/**
 * Gets the news item for the given news id
 *
 * @param integer $news_id the id of the news entry to get
 *
 * @return array|null
 */
function getNewsItem($news_id) {
	$row =
		WT_DB::prepare("SELECT SQL_CACHE news_id, user_id, gedcom_id, UNIX_TIMESTAMP(updated) AS updated, subject, body FROM `##news` WHERE news_id=?")
		->execute(array($news_id))
		->fetchOneRow();

	if ($row) {
		return array(
			'id' => $row->news_id,
			'user_id' => $row->user_id,
			'gedcom_id' => $row->gedcom_id,
			'date' => $row->updated,
			'title' => $row->subject,
			'text' => $row->body,
		);
	} else {
		return null;
	}
}
