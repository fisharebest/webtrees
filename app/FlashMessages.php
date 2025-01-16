<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

use function is_array;

/**
 * Generate messages in one request and display them in the next.
 */
class FlashMessages
{
    // Session storage key
    private const string FLASH_KEY = 'flash_messages';

    /**
     * Add a message to the session storage.
     *
     * @param string $text
     * @param string $status "success", "info", "warning" or "danger"
     *
     * @return void
     */
    public static function addMessage(string $text, string $status = 'info'): void
    {
        $messages = Session::get(self::FLASH_KEY);
        $messages = is_array($messages) ? $messages : [];

        $messages[] = (object) [
            'text'   => $text,
            'status' => $status,
        ];

        Session::put(self::FLASH_KEY, $messages);
    }

    /**
     * Get the current messages, and remove them from session storage.
     *
     * @return array<object>
     */
    public static function getMessages(): array
    {
        $messages = Session::pull(self::FLASH_KEY);

        return is_array($messages) ? $messages : [];
    }
}
