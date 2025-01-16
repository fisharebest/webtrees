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
use Fisharebest\Webtrees\Services\UserService;

use function assert;

/**
 * A tree can act as a user, for example to send email.
 */
class TreeUser implements UserInterface
{
    private Tree $tree;

    /**
     * @param Tree $tree
     */
    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
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
        $user_service = app(UserService::class);
        assert($user_service instanceof UserService);

        $contact_id   = (int) $this->getPreference('CONTACT_USER_ID');

        if ($contact_id !== 0) {
            $contact = $user_service->find($contact_id);

            if ($contact instanceof User) {
                return $contact->email();
            }
        }

        return Site::getPreference('SMTP_FROM_NAME');
    }

    /**
     * @param string $setting_name
     * @param string $default
     *
     * @return string
     */
    public function getPreference(string $setting_name, string $default = ''): string
    {
        return $default;
    }

    /**
     * The user‘s real name.
     *
     * @return string
     */
    public function realName(): string
    {
        return $this->tree->title();
    }

    /**
     * The user‘s login name.
     *
     * @return string
     */
    public function userName(): string
    {
        return '';
    }

    /**
     * @param string $setting_name
     * @param string $setting_value
     *
     * @return void
     */
    public function setPreference(string $setting_name, string $setting_value): void
    {
    }
}
