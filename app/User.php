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
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
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

    /**
     * Create a new user object from a row in the database.
     *
     * @param int    $user_id
     * @param string $user_name
     * @param string $real_name
     * @param string $email
     */
    private function __construct(int $user_id, string $user_name, string $real_name, string $email)
    {
        $this->user_id   = $user_id;
        $this->user_name = $user_name;
        $this->real_name = $real_name;
        $this->email     = $email;
    }

    /**
     * A closure which will create an object from a database row.
     *
     * @return Closure
     */
    public static function rowMapper(): Closure
    {
        return function (stdClass $row): User {
            return new static((int) $row->user_id, $row->user_name, $row->real_name, $row->email);
        };
    }

    /**
     * Create a dummy user from a tree, to send messages.
     *
     * @param Tree $tree
     *
     * @return User
     */
    public static function userFromTree(Tree $tree): User{
        return  new static(0, '', $tree->title(),$tree->getPreference('WEBTREES_EMAIL'));
    }

    /**
     * A dummy/null user for visitors.
     *
     * @param string $real_name
     * @param string $email
     *
     * @return User
     */
    public static function visitor(string $real_name = '', string $email = ''): User
    {
        return new static(0, '', $real_name, $email);
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
        return app('cache.array')->rememberForever(__CLASS__ . $user_id, function () use ($user_id) {
            return DB::table('user')
                ->where('user_id', '=', $user_id)
                ->get();
        })
        ->map(static::rowMapper())
        ->first();
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
        return DB::table('user')
            ->where('email', '=', $email)
            ->get()
            ->map(static::rowMapper())
            ->first();
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
        return DB::table('user')
            ->where('user_name', '=', $identifier)
            ->orWhere('email', '=', $identifier)
            ->get()
            ->map(static::rowMapper())
            ->first();
    }

    /**
     * Find the user(s) with a specified genealogy record.
     *
     * @param Individual $individual
     *
     * @return Collection|User[]
     */
    public static function findByIndividual(Individual $individual): Collection
    {
        return DB::table('user')
            ->join('user_gedcom_setting', 'user_gedcom_setting.user_id', '=', 'user.user_id')
            ->where('gedcom_id', '=', $individual->tree()->id())
            ->where('setting_value', '=', $individual->xref())
            ->where('setting_name', '=', 'gedcomid')
            ->select(['user.*'])
            ->get()
            ->map(static::rowMapper());
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
        return DB::table('user')
            ->where('user_name', '=', $user_name)
            ->get()
            ->map(static::rowMapper())
            ->first();
    }

    /**
     * Get a list of all users.
     *
     * @return Collection|User[]
     */
    public static function all(): Collection
    {
        return DB::table('user')
            ->where('user_id', '>', 0)
            ->orderBy('real_name')
            ->select(['user_id', 'user_name', 'real_name', 'email'])
            ->get()
            ->map(static::rowMapper());
    }

    /**
     * Get a list of all administrators.
     *
     * @return Collection|User[]
     */
    public static function administrators(): Collection
    {
        return DB::table('user')
            ->join('user_setting', function (JoinClause $join): void {
                $join
                    ->on('user_setting.user_id', '=', 'user.user_id')
                    ->where('user_setting.setting_name', '=', 'canadmin')
                    ->where('user_setting.setting_value', '=', '1');
            })
            ->where('user.user_id', '>', 0)
            ->orderBy('real_name')
            ->select(['user.user_id', 'user_name', 'real_name', 'email'])
            ->get()
            ->map(static::rowMapper());
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
            ->where('user_id', '=', $this->user_id)
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
     * Get a list of all managers.
     *
     * @return Collection|User[]
     */
    public static function managers(): Collection
    {
        return DB::table('user')
            ->join('user_gedcom_setting', function (JoinClause $join): void {
                $join
                    ->on('user_gedcom_setting.user_id', '=', 'user.user_id')
                    ->where('user_gedcom_setting.setting_name', '=', 'canedit')
                    ->where('user_gedcom_setting.setting_value', '=', 'admin');
            })
            ->where('user.user_id', '>', 0)
            ->orderBy('real_name')
            ->select(['user.user_id', 'user_name', 'real_name', 'email'])
            ->get()
            ->map(static::rowMapper());
    }

    /**
     * Get a list of all moderators.
     *
     * @return Collection|User[]
     */
    public static function moderators(): Collection
    {
        return DB::table('user')
            ->join('user_gedcom_setting', function (JoinClause $join): void {
                $join
                    ->on('user_gedcom_setting.user_id', '=', 'user.user_id')
                    ->where('user_gedcom_setting.setting_name', '=', 'canedit')
                    ->where('user_gedcom_setting.setting_value', '=', 'accept');
            })
            ->where('user.user_id', '>', 0)
            ->orderBy('real_name')
            ->select(['user.user_id', 'user_name', 'real_name', 'email'])
            ->get()
            ->map(static::rowMapper());
    }

    /**
     * Get a list of all verified users.
     *
     * @return Collection|User[]
     */
    public static function unapproved(): Collection
    {
        return DB::table('user')
            ->join('user_setting', function (JoinClause $join): void {
                $join
                    ->on('user_setting.user_id', '=', 'user.user_id')
                    ->where('user_setting.setting_name', '=', 'verified_by_admin')
                    ->where('user_setting.setting_value', '=', '0');
            })
            ->where('user.user_id', '>', 0)
            ->orderBy('real_name')
            ->select(['user.user_id', 'user_name', 'real_name', 'email'])
            ->get()
            ->map(static::rowMapper());
    }

    /**
     * Get a list of all verified users.
     *
     * @return Collection|User[]
     */
    public static function unverified(): Collection
    {
        return DB::table('user')
            ->join('user_setting', function (JoinClause $join): void {
                $join
                    ->on('user_setting.user_id', '=', 'user.user_id')
                    ->where('user_setting.setting_name', '=', 'verified')
                    ->where('user_setting.setting_value', '=', '0');
            })
            ->where('user.user_id', '>', 0)
            ->orderBy('real_name')
            ->select(['user.user_id', 'user_name', 'real_name', 'email'])
            ->get()
            ->map(static::rowMapper());
    }

    /**
     * Get a list of all users who are currently logged in.
     *
     * @return Collection|User[]
     */
    public static function allLoggedIn(): Collection
    {
        return DB::table('user')
            ->join('session', 'session.user_id', '=', 'user.user_id')
            ->where('user.user_id', '>', 0)
            ->orderBy('real_name')
            ->select(['user.user_id', 'user_name', 'real_name', 'email'])
            ->distinct()
            ->get()
            ->map(static::rowMapper());
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
