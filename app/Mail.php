<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Fisharebest\Webtrees;

use Zend_Mail;
use Zend_Mail_Transport_File;
use Zend_Mail_Transport_Sendmail;
use Zend_Mail_Transport_Smtp;

/**
 * Send mail messages.
 */
class Mail {
	const EOL = "<br>\r\n"; // End-of-line that works for both TEXT and HTML messages

	/**
	 * Send an external email message
	 * Caution! gmail may rewrite the "From" header unless you have added the address to your account.
	 *
	 * @param Tree   $tree
	 * @param string $to_email
	 * @param string $to_name
	 * @param string $replyto_email
	 * @param string $replyto_name
	 * @param string $subject
	 * @param string $message
	 *
	 * @return bool
	 */
	public static function send(Tree $tree, $to_email, $to_name, $replyto_email, $replyto_name, $subject, $message) {
		try {
			$mail = new Zend_Mail('UTF-8');
			$mail
				->setSubject($subject)
				->setBodyHtml($message)
				->setBodyText(Filter::unescapeHtml($message))
				->setFrom(Site::getPreference('SMTP_FROM_NAME'), $tree->getPreference('title'))
				->addTo($to_email, $to_name)
				->setReplyTo($replyto_email, $replyto_name)
				->send(self::transport());
		} catch (\Exception $ex) {
			Log::addErrorLog('Mail: ' . $ex->getMessage());

			return false;
		}

		return true;
	}

	/**
	 * Send an automated system message (such as a password reminder) from a tree to a user.
	 *
	 * @param Tree   $tree
	 * @param User   $user
	 * @param string $subject
	 * @param string $message
	 *
	 * @return bool
	 */
	public static function systemMessage(Tree $tree, User $user, $subject, $message) {
		return self::send(
			$tree,
			$user->getEmail(), $user->getRealName(),
			Site::getPreference('SMTP_FROM_NAME'), $tree->getPreference('title'),
			$subject,
			$message
		);
	}

	/**
	 * Create a transport mechanism for sending mail
	 *
	 * @return Zend_Mail_Transport_File|Zend_Mail_Transport_Smtp
	 */
	public static function transport() {
		switch (Site::getPreference('SMTP_ACTIVE')) {
		case 'internal':
			return new Zend_Mail_Transport_Sendmail;
		case 'external':
			$config = array(
				'name' => Site::getPreference('SMTP_HELO'),
				'port' => Site::getPreference('SMTP_PORT'),
			);
			if (Site::getPreference('SMTP_AUTH')) {
				$config['auth']     = 'login';
				$config['username'] = Site::getPreference('SMTP_AUTH_USER');
				$config['password'] = Site::getPreference('SMTP_AUTH_PASS');
			}
			if (Site::getPreference('SMTP_SSL') !== 'none') {
				$config['ssl'] = Site::getPreference('SMTP_SSL');
			}

			return new Zend_Mail_Transport_Smtp(Site::getPreference('SMTP_HOST'), $config);
		default:
			// For testing
			return new Zend_Mail_Transport_File;
		}
	}
}
