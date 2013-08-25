<?php
// Send mail messages
//
// webtrees: Web based Family History software
// Copyright (c) 2013 webtrees development team
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
			'DNS LOOKUP: ' . gethostbyaddr($WT_REQUEST->getClientIp()) . self::EOL .
			'LANGUAGE: '   . WT_LOCALE . self::EOL;
	}

	// Send an external email message
	public static function send($to_email, $to_name, $from_email, $from_name, $subject, $message) {
		$SMTP_ACTIVE   =WT_Site::preference('SMTP_ACTIVE');
		$SMTP_HOST     =WT_Site::preference('SMTP_HOST');
		$SMTP_HELO     =WT_Site::preference('SMTP_HELO');
		$SMTP_FROM_NAME=WT_Site::preference('SMTP_FROM_NAME'); // Is this needed?
		$SMTP_PORT     =WT_Site::preference('SMTP_PORT');
		$SMTP_AUTH     =WT_Site::preference('SMTP_AUTH');
		$SMTP_AUTH_USER=WT_Site::preference('SMTP_AUTH_USER');
		$SMTP_AUTH_PASS=WT_Site::preference('SMTP_AUTH_PASS');
		$SMTP_SSL      =WT_Site::preference('SMTP_SSL');

		// Create the mail transport mechanism
		switch (WT_Site::preference('SMTP_ACTIVE')) {
		case 'internal':
			$mail_transport = new Zend_Mail_Transport_Sendmail();
			break;
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
			if (WT_Site::preference('SMTP_SSL')) {
				$config['ssl']       = WT_Site::preference('SMTP_SSL');
			}

			$mail_transport = new Zend_Mail_Transport_Smtp(WT_Site::preference('SMTP_HOST'), $config);
			break;
		default:
			// For testing
			$mail_transport = new Zend_Mail_Transport_File();
			break;
		}

		// Create and send the message
		$mail = new Zend_Mail('UTF-8');
		$mail
			->setSubject($subject)
			->setBodyHtml($message)
			->setBodyText(WT_Filter::unescapeHtml($message))
			->setFrom($from_email, $from_name)
			->addTo($to_email, $to_name)
			->send($mail_transport);
	}
}
