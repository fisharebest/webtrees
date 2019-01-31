<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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
declare(strict_types=1);

namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Contracts\UserInterface;

/**
 * A tree can act as a user, for example to send email.
 */
class TreeUser implements UserInterface
{
    /**
     * @var Tree
     */
    private $tree;

    /**
     * TreeUser constructor.
     *
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
        return $this->tree->getPreference('WEBTREES_EMAIL', 'no-reply@example.com');
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
     * @param string $default
     *
     * @return string
     */
    public function getPreference(string $setting_name, string $default = ''): string
    {
        return $default;
    }

    /**
     * @param string $setting_name
     * @param string $setting_value
     *
     * @return UserInterface
     */
    public function setPreference(string $setting_name, string $setting_value): UserInterface
    {
        return $this;
    }
}
