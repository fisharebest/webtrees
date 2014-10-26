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

		$success = $success && WT_Mail::send(
			// “From:” header
			$WT_TREE,
			// “To:” header
			$sender_email,
			$sender_real_name,
			// “Reply-To:” header
			WT_Site::getPreference('SMTP_FROM_NAME'),
			$WT_TREE->getPreference('title'),
			// Message body
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

		$success = $success && WT_Mail::send(
			// “From:” header
			$WT_TREE,
			// “To:” header
			$recipient->getEmail(),
			$recipient->getRealName(),
			// “Reply-To:” header
			$sender_email,
			$sender_real_name,
			// Message body
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
