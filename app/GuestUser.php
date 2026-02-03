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

use Fisharebest\Webtrees\Contracts\UserInterface;

use function is_string;

/**
 * A site visitor.
 */
class GuestUser implements UserInterface
{
    private string $user_name;
    private string $real_name;
    private string $email;

    public function __construct(string $user_name = '_GUEST_', string $real_name = 'GUEST_USER', string $email = 'GUEST_USER')
    {
        $this->user_name = $user_name;
        $this->real_name = $real_name;
        $this->email = $email;
    }

    /**
     * The user‘s internal identifier.
     *
     * @return int
     */
    public function id(): int
    {
        return 0;
    }

    /**
     * The users email address.
     *
     * @return string
     */
    public function email(): string
    {
        return $this->email;
    }

    /**
     * The user‘s real name.
     *
     * @return string
     */
    public function realName(): string
    {
        return $this->real_name;
    }

    /**
     * The user‘s login name.
     *
     * @return string
     */
    public function userName(): string
    {
        return $this->user_name;
    }

    /**
     * @param string $setting_name
     * @param string $default
     *
     * @return string
     */
    public function getPreference(string $setting_name, string $default = ''): string
    {
        $preference = Session::get($this->userName() . $setting_name);

        return is_string($preference) ? $preference : $default;
    }

    /**
     * @param string $setting_name
     * @param string $setting_value
     *
     * @return void
     */
    public function setPreference(string $setting_name, string $setting_value): void
    {
        Session::put($this->userName() . $setting_name, $setting_value);
    }
}
