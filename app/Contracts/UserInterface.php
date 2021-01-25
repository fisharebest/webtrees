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

namespace Fisharebest\Webtrees\Contracts;

/**
 * behavior required of a user object.
 */
interface UserInterface
{
    // For historic reasons, user preferences have inconsistent and confusing names.
    public const PREF_AUTO_ACCEPT_EDITS    = 'auto_accept';
    public const PREF_CONTACT_METHOD       = 'contactmethod';
    public const PREF_IS_ACCOUNT_APPROVED  = 'verified_by_admin';
    public const PREF_IS_ADMINISTRATOR     = 'canadmin';
    public const PREF_IS_EMAIL_VERIFIED    = 'verified';
    public const PREF_IS_VISIBLE_ONLINE    = 'visibleonline';
    public const PREF_LANGUAGE             = 'language';
    public const PREF_NEW_ACCOUNT_COMMENT  = 'comment';
    public const PREF_TIMESTAMP_REGISTERED = 'reg_timestamp';
    public const PREF_TIMESTAMP_ACTIVE     = 'sessiontime';
    public const PREF_TIME_ZONE            = 'TIMEZONE';
    public const PREF_THEME                = 'theme';
    public const PREF_VERIFICATION_TOKEN   = 'reg_hashcode';

    // For historic reasons, user-tree preferences have inconsistent and confusing names.
    public const PREF_TREE_ACCOUNT_XREF = 'gedcomid';
    public const PREF_TREE_DEFAULT_XREF = 'rootid';
    public const PREF_TREE_PATH_LENGTH  = 'RELATIONSHIP_PATH_LENGTH';
    public const PREF_TREE_ROLE         = 'canedit';

    // For historic reasons, roles have inconsistent and confusing names.
    public const ROLE_VISITOR   = 'none';
    public const ROLE_MEMBER    = 'access';
    public const ROLE_EDITOR    = 'edit';
    public const ROLE_MODERATOR = 'accept';
    public const ROLE_MANAGER   = 'admin';

    /**
     * The user‘s internal identifier
     *
     * @return int
     */
    public function id(): int;

    /**
     * The users email address.
     *
     * @return string
     */
    public function email(): string;

    /**
     * The user‘s real name.
     *
     * @return string
     */
    public function realName(): string;

    /**
     * The user‘s login name.
     *
     * @return string
     */
    public function userName(): string;

    /**
     * @param string $setting_name
     * @param string $default
     *
     * @return string
     */
    public function getPreference(string $setting_name, string $default = ''): string;

    /**
     * @param string $setting_name
     * @param string $setting_value
     *
     * @return void
     */
    public function setPreference(string $setting_name, string $setting_value): void;
}
