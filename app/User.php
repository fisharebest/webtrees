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

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use stdClass;

/**
 * Provide an interface to the wt_user table.
 */
class User
{
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

    /** @var  User[]|null[] Only fetch users from the database once. */
    public static $cache = [];

    /**
     * Create a new user object from a row in the database.
     *
     * @param stdClass $user A row from the wt_user table
     */
    public function __construct(stdClass $user)
    {
        $this->user_id   = (int) $user->user_id;
        $this->user_name = $user->user_name;
        $this->real_name = $user->real_name;
        $this->email     = $user->email;
    }

    /**
     * Create a new user.
     * The calling code needs to check for duplicates identifiers before calling
     * this function.
     *
     * @param string $user_name
     * @param string $real_name
     * @param string $email
     * @param string $password
     *
     * @return User
     */
    public static function create($user_name, $real_name, $email, $password): User
    {
        DB::table('user')->insert([
            'user_name' => $user_name,
            'real_name' => $real_name,
            'email'     => $email,
            'password'  => password_hash($password, PASSWORD_DEFAULT),
        ]);

        // Set default blocks for this user
        $user = self::findByIdentifier($user_name);

        (new Builder(DB::connection()))->from('block')->insertUsing(
            ['user_id', 'location', 'block_order', 'module_name'],
            function (Builder $query) use ($user): void {
                $query
                    ->select([DB::raw($user->getuserId()), 'location', 'block_order', 'module_name'])
                    ->from('block')
                    ->where('user_id', '=', -1);
            }
        );

        return $user;
    }

    /**
     * Delete a user
     *
     * @return void
     */
    public function delete()
    {
        // Don't delete the logs, just set the user to null.
        DB::table('log')
            ->where('user_id', '=', $this->user_id)
            ->update(['user_id' => null]);

        // Take over the user’s pending changes. (What else could we do with them?)
        DB::table('change')
            ->where('user_id', '=', $this->user_id)
            ->where('status', '=', 'rejected')
            ->delete();

        DB::table('change')
            ->where('user_id', '=', $this->user_id)
            ->update(['user_id' => Auth::id()]);

        // Take over the user's contact details
        DB::table('gedcom_setting')
            ->where('setting_value', '=', $this->user_id)
            ->whereIn('setting_name', ['CONTACT_USER_ID', 'WEBMASTER_USER_ID'])
            ->update(['setting_value' => Auth::id()]);

        // Delete settings and preferences
        DB::table('block_setting')
            ->join('block', 'block_setting.block_id', '=', 'block.block_id')
            ->where('user_id', '=', $this->user_id)
            ->delete();

        DB::table('block')->where('user_id', '=', $this->user_id)->delete();
        DB::table('user_gedcom_setting')->where('user_id', '=', $this->user_id)->delete();
        DB::table('user_setting')->where('user_id', '=', $this->user_id)->delete();
        DB::table('message')->where('user_id', '=', $this->user_id)->delete();
        DB::table('user')->where('user_id', '=', $this->user_id)->delete();

        unset(self::$cache[$this->user_id]);
    }

    /**
     * Find the user with a specified user_id.
     *
     * @param int|null $user_id
     *
     * @return User|null
     */
    public static function find($user_id)
    {
        if (!array_key_exists($user_id, self::$cache)) {
            $row = DB::table('user')
                ->where('user_id', '=', $user_id)
                ->first();

            if ($row) {
                self::$cache[$user_id] = new self($row);
            } else {
                self::$cache[$user_id] = null;
            }
        }

        return self::$cache[$user_id];
    }

    /**
     * Find the user with a specified email address.
     *
     * @param string $email
     *
     * @return User|null
     */
    public static function findByEmail($email)
    {
        $user_id = (int) DB::table('user')
            ->where('email', '=', $email)
            ->value('user_id');

        return self::find($user_id);
    }

    /**
     * Find the user with a specified user_name or email address.
     *
     * @param string $identifier
     *
     * @return User|null
     */
    public static function findByIdentifier($identifier)
    {
        $user_id = (int) DB::table('user')
            ->where('user_name', '=', $identifier)
            ->orWhere('email', '=', $identifier)
            ->value('user_id');

        return self::find($user_id);
    }

    /**
     * Find the user(s) with a specified genealogy record.
     *
     * @param Individual $individual
     *
     * @return User[]
     */
    public static function findByIndividual(Individual $individual): array
    {
        $user_ids = DB::table('user_gedcom_setting')
            ->where('gedcom_id', '=', $individual->tree()->id())
            ->where('setting_value', '=', $individual->xref())
            ->where('setting_name', '=', 'gedcomid')
            ->pluck('user_id');

        return $user_ids->map(function (string $user_id): User {
            return self::find((int) $user_id);
        })->all();
    }

    /**
     * Find the user with a specified user_name.
     *
     * @param string $user_name
     *
     * @return User|null
     */
    public static function findByUserName($user_name)
    {
        $user_id = (int) DB::table('user')
            ->where('user_name', '=', $user_name)
            ->value('user_id');

        return self::find($user_id);
    }

    /**
     * Get a list of all users.
     *
     * @return User[]
     */
    public static function all(): array
    {
        $rows = Database::prepare(
            "SELECT user_id, user_name, real_name, email" .
            " FROM `##user`" .
            " WHERE user_id > 0" .
            " ORDER BY real_name"
        )->fetchAll();

        return array_map(function (stdClass $row): User {
            return new static($row);
        }, $rows);
    }

    /**
     * Get a list of all administrators.
     *
     * @return User[]
     */
    public static function administrators(): array
    {
        $rows = Database::prepare(
            "SELECT user_id, user_name, real_name, email" .
            " FROM `##user`" .
            " JOIN `##user_setting` USING (user_id)" .
            " WHERE user_id > 0 AND setting_name = 'canadmin' AND setting_value = '1'" .
            " ORDER BY real_name"
        )->fetchAll();

        return array_map(function (stdClass $row): User {
            return new static($row);
        }, $rows);
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
        $password_hash = Database::prepare(
            "SELECT password FROM `##user` WHERE user_id = ?"
        )->execute([$this->user_id])->fetchOne();

        if ($password_hash !== null && password_verify($password, $password_hash)) {
            if (password_needs_rehash($password_hash, PASSWORD_DEFAULT)) {
                $this->setPassword($password);
            }

            return true;
        }

        return false;
    }

    /**
     * Get a list of all managers.
     *
     * @return User[]
     */
    public static function managers(): array
    {
        $rows = Database::prepare(
            "SELECT user_id, user_name, real_name, email" .
            " FROM `##user` JOIN `##user_gedcom_setting` USING (user_id)" .
            " WHERE setting_name = 'canedit' AND setting_value='admin'" .
            " GROUP BY user_id, real_name" .
            " ORDER BY real_name"
        )->fetchAll();

        return array_map(function (stdClass $row): User {
            return new static($row);
        }, $rows);
    }

    /**
     * Get a list of all moderators.
     *
     * @return User[]
     */
    public static function moderators(): array
    {
        $rows = Database::prepare(
            "SELECT user_id, user_name, real_name, email" .
            " FROM `##user` JOIN `##user_gedcom_setting` USING (user_id)" .
            " WHERE setting_name = 'canedit' AND setting_value='accept'" .
            " GROUP BY user_id, real_name" .
            " ORDER BY real_name"
        )->fetchAll();

        return array_map(function (stdClass $row): User {
            return new static($row);
        }, $rows);
    }

    /**
     * Get a list of all verified users.
     *
     * @return User[]
     */
    public static function unapproved(): array
    {
        $rows = Database::prepare(
            "SELECT user_id, user_name, real_name, email" .
            " FROM `##user` JOIN `##user_setting` USING (user_id)" .
            " WHERE setting_name = 'verified_by_admin' AND setting_value = '0'" .
            " ORDER BY real_name"
        )->fetchAll();

        return array_map(function (stdClass $row): User {
            return new static($row);
        }, $rows);
    }

    /**
     * Get a list of all verified users.
     *
     * @return User[]
     */
    public static function unverified(): array
    {
        $rows = Database::prepare(
            "SELECT user_id, user_name, real_name, email" .
            " FROM `##user` JOIN `##user_setting` USING (user_id)" .
            " WHERE setting_name = 'verified' AND setting_value = '0'" .
            " ORDER BY real_name"
        )->fetchAll();

        return array_map(function (stdClass $row): User {
            return new static($row);
        }, $rows);
    }

    /**
     * Get a list of all users who are currently logged in.
     *
     * @return User[]
     */
    public static function allLoggedIn(): array
    {
        $rows = Database::prepare(
            "SELECT DISTINCT user_id, user_name, real_name, email" .
            " FROM `##user`" .
            " JOIN `##session` USING (user_id)"
        )->fetchAll();

        return array_map(function (stdClass $row): User {
            return new static($row);
        }, $rows);
    }

    /**
     * Get the numeric ID for this user.
     *
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * Get the login name for this user.
     *
     * @return string
     */
    public function getUserName(): string
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
     * Get the real name of this user.
     *
     * @return string
     */
    public function getRealName(): string
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
     * Get the email address of this user.
     *
     * @return string
     */
    public function getEmail(): string
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
     * Set the password of this user.
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password): User
    {
        DB::table('user')
            ->where('user_id', '=', $this->user_id)
            ->update([
                'password' => password_hash($password, PASSWORD_DEFAULT),
            ]);

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
    public function getPreference($setting_name, $default = ''): string
    {
        if (empty($this->preferences) && $this->user_id !== 0) {
            $this->preferences = DB::table('user_setting')
                ->where('user_id', '=', $this->user_id)
                ->pluck('setting_value', 'setting_name')
                ->all();
        }

        return $this->preferences[$setting_name] ?? $default;
    }

    /**
     * Update a setting for the user.
     *
     * @param string $setting_name
     * @param string $setting_value
     *
     * @return User
     */
    public function setPreference($setting_name, $setting_value): User
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

        return $this;
    }
}
