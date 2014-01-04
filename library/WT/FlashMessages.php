<?php
// Flash messages allow us to generate messages in one context, and
// display them in another.
//
// webtrees: Web based Family History software
// Copyright (C) 2014 Greg Roach
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

class WT_FlashMessages {
	public static function addMessage($message) {
		$flash_messenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');

		$flash_messenger->addMessage($message);
	}

	public static function getMessages() {
		$flash_messenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');

		$messages = array();

		// Get messages from previous requests
		foreach ($flash_messenger->getMessages() as $message) {
			$messages[] = $message;
		}

		// Get messages from the current request
		foreach ($flash_messenger->getCurrentMessages() as $message) {
			$messages[] = $message;
		}
    $flash_messenger->clearCurrentMessages();

		return $messages;
	}

	// Most theres will want a simple block of HTML to display
	public static function getHtmlMessages() {
		$html = '';

		foreach (self::getMessages() as $message) {
			$html .= '<p class="ui-state-highlight">' . $message . '</p>';
		}

		if ($html) {
			$html = '<div id="flash-messages">' . $html . '</div>';
		}

		return $html;
	}


}
