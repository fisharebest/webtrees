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

/**
 * Generate messages in one request and display them in the next.
 */
class FlashMessages {
	// Session storage key
	const FLASH_KEY = 'flash_messages';

	/**
	 * Add a new message to the session storage.
	 *
	 * @param string $text
	 * @param string $status "success", "info", "warning" or "danger"
	 */
	public static function addMessage($text, $status = 'info') {
		$message         = new \stdClass;
		$message->text   = $text;
		$message->status = $status;

		$messages   = Session::get(self::FLASH_KEY, array());
		$messages[] = $message;
		Session::put(self::FLASH_KEY, $messages);
	}

	/**
	 * Get the current messages, and remove them from session storage.
	 *
	 * @return string[]
	 */
	public static function getMessages() {
		$messages = Session::get(self::FLASH_KEY, array());
		Session::forget(self::FLASH_KEY);

		return $messages;
	}
}
