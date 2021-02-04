<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees;

use stdClass;

/**
 * Generate messages in one request and display them in the next.
 */
class FlashMessages
{
    // Session storage key
    private const FLASH_KEY = 'flash_messages';

    /**
     * Add a message to the session storage.
     *
     * @param string $text
     * @param string $status "success", "info", "warning" or "danger"
     *
     * @return void
     */
    public static function addMessage(string $text, $status = 'info'): void
    {
        $message         = new stdClass();
        $message->text   = $text;
        $message->status = $status;

        $messages   = Session::get(self::FLASH_KEY, []);
        $messages[] = $message;
        Session::put(self::FLASH_KEY, $messages);
    }

    /**
     * Get the current messages, and remove them from session storage.
     *
     * @return stdClass[]
     */
    public static function getMessages(): array
    {
        return Session::pull(self::FLASH_KEY, []);
    }
}
