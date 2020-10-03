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

use Closure;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;
use stdClass;

/**
 * Provide an interface to the wt_user table.
 */
class User implements UserInterface
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

    /** @var  int The primary key of this user. */
    private $user_id;

    /** @var  string The login name of this user. */
    private $user_name;

    /** @var  string The real (display) name of this user. */
    private $real_name;

    /** @var  string The email address of this user. */
    private $email;

    /** @var string[] Cached copy of the wt_user_setting table. */
    private $preferences = [];

    /**
     * User constructor.
     *
     * @param int    $user_id
     * @param string $user_name
     * @param string $real_name
     * @param string $email
     */
    public function __construct(int $user_id, string $user_name, string $real_name, string $email)
    {
        $this->user_id   = $user_id;
        $this->user_name = $user_name;
        $this->real_name = $real_name;
        $this->email     = $email;
    }

    /**
     * The user‘s internal identifier.
     *
     * @return int
     */
    public function id(): int
    {
        return $this->user_id;
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
     * Set the email address of this user.
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email): User
    {
        if ($this->email !== $email) {
            $this->email = $email;

            DB::table('user')
                ->where('user_id', '=', $this->user_id)
                ->update([
                    'email' => $email,
                ]);
        }

        return $this;
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
     * Set the real name of this user.
     *
     * @param string $real_name
     *
     * @return User
     */
    public function setRealName($real_name): User
    {
        if ($this->real_name !== $real_name) {
            $this->real_name = $real_name;

            DB::table('user')
                ->where('user_id', '=', $this->user_id)
                ->update([
                    'real_name' => $real_name,
                ]);
        }

        return $this;
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
     * Set the login name for this user.
     *
     * @param string $user_name
     *
     * @return $this
     */
    public function setUserName($user_name): self
    {
        if ($this->user_name !== $user_name) {
            $this->user_name = $user_name;

            DB::table('user')
                ->where('user_id', '=', $this->user_id)
                ->update([
                    'user_name' => $user_name,
                ]);
        }

        return $this;
    }

    /**
     * Fetch a user option/setting from the wt_user_setting table.
     * Since we'll fetch several settings for each user, and since there aren’t
     * that many of them, fetch them all in one database query
     *
     * @param string $setting_name
     * @param string $default
     *
     * @return string
     */
    public function getPreference(string $setting_name, string $default = ''): string
    {
        $preferences = Registry::cache()->array()->remember('user-prefs-' . $this->user_id, function (): Collection {
            if ($this->user_id) {
                return DB::table('user_setting')
                    ->where('user_id', '=', $this->user_id)
                    ->pluck('setting_value', 'setting_name');
            }

            return new Collection();
        });

        return $preferences->get($setting_name, $default);
    }

    /**
     * Update a setting for the user.
     *
     * @param string $setting_name
     * @param string $setting_value
     *
     * @return void
     */
    public function setPreference(string $setting_name, string $setting_value): void
    {
        if ($this->user_id !== 0 && $this->getPreference($setting_name) !== $setting_value) {
            DB::table('user_setting')->updateOrInsert([
                'user_id'      => $this->user_id,
                'setting_name' => $setting_name,
            ], [
                'setting_value' => $setting_value,
            ]);

            $this->preferences[$setting_name] = $setting_value;
        }
    }

    /**
     * Set the password of this user.
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword(string $password): User
    {
        DB::table('user')
            ->where('user_id', '=', $this->user_id)
            ->update([
                'password' => password_hash($password, PASSWORD_DEFAULT),
            ]);

        return $this;
    }


    /**
     * Validate a supplied password
     *
     * @param string $password
     *
     * @return bool
     */
    public function checkPassword(string $password): bool
    {
        $password_hash = DB::table('user')
            ->where('user_id', '=', $this->id())
            ->value('password');

        if ($password_hash !== null && password_verify($password, $password_hash)) {
            if (password_needs_rehash($password_hash, PASSWORD_DEFAULT)) {
                $this->setPassword($password);
            }

            return true;
        }

        return false;
    }

    /**
     * A closure which will create an object from a database row.
     *
     * @return Closure
     */
    public static function rowMapper(): Closure
    {
        return static function (stdClass $row): User {
            return new self((int) $row->user_id, $row->user_name, $row->real_name, $row->email);
        };
    }
}
