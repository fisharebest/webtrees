<?php
// Send mail messages
//
// webtrees: Web based Family History software
// Copyright (c) 2014 webtrees development team
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

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class WT_Mail {
	const EOL = "<br>\r\n"; // End-of-line that works for both TEXT and HTML messages

	// Audit information to add to email footer
	public static function auditFooter() {
		global $WT_REQUEST;

		return
			self::EOL .
			'---------------------------------------' . self::EOL .
			'IP ADDRESS: ' . $WT_REQUEST->getClientIp() . self::EOL .
			'LANGUAGE: '   . WT_LOCALE . self::EOL;
	}

	// Send an external email message
	// Caution! gmail may rewrite the "From" header unless you have added the address to your account.
	public static function send(WT_Tree $tree, $to_email, $to_name, $replyto_email, $replyto_name, $subject, $message) {
		try {
			$mail = new Zend_Mail('UTF-8');
			$mail
				->setSubject ($subject)
				->setBodyHtml($message)
				->setBodyText(WT_Filter::unescapeHtml($message))
				->setFrom    (WT_Site::preference('SMTP_FROM_NAME'), $tree->preference('title'))
				->addTo      ($to_email,                             $to_name)
				->setReplyTo ($replyto_email,                        $replyto_name)
				->send       (WT_Mail::transport());
		} catch (Exception $ex) {
			AddToLog('Mail: ' . $ex->getMessage(), 'error');
			return false;
		}
		return true;
	}

	// Send an automated system message (such as a password reminder) from a tree to a user.
	public static function system_message(WT_Tree $tree, $user_id, $subject, $message) {
		return self::send(
			$tree,
			getUserEmail($user_id),                getUserFullName($user_id),
			WT_Site::preference('SMTP_FROM_NAME'), $tree->preference('title'),
			$subject,
			$message
		);
	}


	// Create a transport mechanism for sending mail
	public static function transport() {
		switch (WT_Site::preference('SMTP_ACTIVE')) {
		case 'internal':
			return new Zend_Mail_Transport_Sendmail();
		case 'external':
			$config = array(
				'name' => WT_Site::preference('SMTP_HELO'),
				'port' => WT_Site::preference('SMTP_PORT'),
			);
			if (WT_Site::preference('SMTP_AUTH')) {
				$config['auth']     = 'login';
				$config['username'] = WT_Site::preference('SMTP_AUTH_USER');
				$config['password'] = WT_Site::preference('SMTP_AUTH_PASS');
			}
			if (WT_Site::preference('SMTP_SSL') !== 'none') {
				$config['ssl'] = WT_Site::preference('SMTP_SSL');
			}

			return new Zend_Mail_Transport_Smtp(WT_Site::preference('SMTP_HOST'), $config);
		default:
			// For testing
			return new Zend_Mail_Transport_File();
		}
	}
}
