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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Psr\Http\Message\ServerRequestInterface;
use function app;

/**
 * Functions for managing users.
 */
class UserService
{
    /**
     * Find the user with a specified user_id.
     *
     * @param int|null $user_id
     *
     * @return User|null
     */
    public function find($user_id): ?User
    {
        return app('cache.array')->rememberForever(__CLASS__ . $user_id, static function () use ($user_id): ?User {
            return DB::table('user')
                ->where('user_id', '=', $user_id)
                ->get()
                ->map(User::rowMapper())
                ->first();
        });
    }

    /**
     * Find the user with a specified email address.
     *
     * @param string $email
     *
     * @return User|null
     */
    public function findByEmail($email): ?User
    {
        return DB::table('user')
            ->where('email', '=', $email)
            ->get()
            ->map(User::rowMapper())
            ->first();
    }

    /**
     * Find the user with a specified user_name or email address.
     *
     * @param string $identifier
     *
     * @return User|null
     */
    public function findByIdentifier($identifier): ?User
    {
        return DB::table('user')
            ->where('user_name', '=', $identifier)
            ->orWhere('email', '=', $identifier)
            ->get()
            ->map(User::rowMapper())
            ->first();
    }

    /**
     * Find the user(s) with a specified genealogy record.
     *
     * @param Individual $individual
     *
     * @return Collection
     * @return User[]
     */
    public function findByIndividual(Individual $individual): Collection
    {
        return DB::table('user')
            ->join('user_gedcom_setting', 'user_gedcom_setting.user_id', '=', 'user.user_id')
            ->where('gedcom_id', '=', $individual->tree()->id())
            ->where('setting_value', '=', $individual->xref())
            ->where('setting_name', '=', 'gedcomid')
            ->select(['user.*'])
            ->get()
            ->map(User::rowMapper());
    }

    /**
     * Find the user with a specified user_name.
     *
     * @param string $user_name
     *
     * @return User|null
     */
    public function findByUserName($user_name): ?User
    {
        return DB::table('user')
            ->where('user_name', '=', $user_name)
            ->get()
            ->map(User::rowMapper())
            ->first();
    }

    /**
     * Get a list of all users.
     *
     * @return Collection
     * @return User[]
     */
    public function all(): Collection
    {
        return DB::table('user')
            ->where('user_id', '>', 0)
            ->orderBy('real_name')
            ->get()
            ->map(User::rowMapper());
    }

    /**
     * Get a list of all administrators.
     *
     * @return Collection
     * @return User[]
     */
    public function administrators(): Collection
    {
        return DB::table('user')
            ->join('user_setting', static function (JoinClause $join): void {
                $join
                    ->on('user_setting.user_id', '=', 'user.user_id')
                    ->where('user_setting.setting_name', '=', 'canadmin')
                    ->where('user_setting.setting_value', '=', '1');
            })
            ->where('user.user_id', '>', 0)
            ->orderBy('real_name')
            ->select(['user.*'])
            ->get()
            ->map(User::rowMapper());
    }

    /**
     * Get a list of all managers.
     *
     * @return Collection
     * @return User[]
     */
    public function managers(): Collection
    {
        return DB::table('user')
            ->join('user_gedcom_setting', static function (JoinClause $join): void {
                $join
                    ->on('user_gedcom_setting.user_id', '=', 'user.user_id')
                    ->where('user_gedcom_setting.setting_name', '=', 'canedit')
                    ->where('user_gedcom_setting.setting_value', '=', 'admin');
            })
            ->where('user.user_id', '>', 0)
            ->orderBy('real_name')
            ->select(['user.*'])
            ->get()
            ->map(User::rowMapper());
    }

    /**
     * Get a list of all moderators.
     *
     * @return Collection
     * @return User[]
     */
    public function moderators(): Collection
    {
        return DB::table('user')
            ->join('user_gedcom_setting', static function (JoinClause $join): void {
                $join
                    ->on('user_gedcom_setting.user_id', '=', 'user.user_id')
                    ->where('user_gedcom_setting.setting_name', '=', 'canedit')
                    ->where('user_gedcom_setting.setting_value', '=', 'accept');
            })
            ->where('user.user_id', '>', 0)
            ->orderBy('real_name')
            ->select(['user.*'])
            ->get()
            ->map(User::rowMapper());
    }

    /**
     * Get a list of all verified users.
     *
     * @return Collection
     * @return User[]
     */
    public function unapproved(): Collection
    {
        return DB::table('user')
            ->join('user_setting', static function (JoinClause $join): void {
                $join
                    ->on('user_setting.user_id', '=', 'user.user_id')
                    ->where('user_setting.setting_name', '=', 'verified_by_admin')
                    ->where('user_setting.setting_value', '=', '0');
            })
            ->where('user.user_id', '>', 0)
            ->orderBy('real_name')
            ->select(['user.*'])
            ->get()
            ->map(User::rowMapper());
    }

    /**
     * Get a list of all verified users.
     *
     * @return Collection
     * @return User[]
     */
    public function unverified(): Collection
    {
        return DB::table('user')
            ->join('user_setting', static function (JoinClause $join): void {
                $join
                    ->on('user_setting.user_id', '=', 'user.user_id')
                    ->where('user_setting.setting_name', '=', 'verified')
                    ->where('user_setting.setting_value', '=', '0');
            })
            ->where('user.user_id', '>', 0)
            ->orderBy('real_name')
            ->select(['user.*'])
            ->get()
            ->map(User::rowMapper());
    }

    /**
     * Get a list of all users who are currently logged in.
     *
     * @return Collection
     * @return User[]
     */
    public function allLoggedIn(): Collection
    {
        return DB::table('user')
            ->join('session', 'session.user_id', '=', 'user.user_id')
            ->where('user.user_id', '>', 0)
            ->orderBy('real_name')
            ->select(['user.*'])
            ->distinct()
            ->get()
            ->map(User::rowMapper());
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
    public function create(string $user_name, string $real_name, string $email, string $password): User
    {
        DB::table('user')->insert([
            'user_name' => $user_name,
            'real_name' => $real_name,
            'email'     => $email,
            'password'  => password_hash($password, PASSWORD_DEFAULT),
        ]);

        $user_id = (int) DB::connection()->getPdo()->lastInsertId();

        return new User($user_id, $user_name, $real_name, $email);
    }

    /**
     * Delete a user
     *
     * @param User $user
     *
     * @return void
     */
    public function delete(User $user): void
    {
        // Don't delete the logs, just set the user to null.
        DB::table('log')
            ->where('user_id', '=', $user->id())
            ->update(['user_id' => null]);

        // Take over the user’s pending changes. (What else could we do with them?)
        DB::table('change')
            ->where('user_id', '=', $user->id())
            ->where('status', '=', 'rejected')
            ->delete();

        DB::table('change')
            ->where('user_id', '=', $user->id())
            ->update(['user_id' => Auth::id()]);

        // Delete settings and preferences
        DB::table('block_setting')
            ->join('block', 'block_setting.block_id', '=', 'block.block_id')
            ->where('user_id', '=', $user->id())
            ->delete();

        DB::table('block')->where('user_id', '=', $user->id())->delete();
        DB::table('user_gedcom_setting')->where('user_id', '=', $user->id())->delete();
        DB::table('user_setting')->where('user_id', '=', $user->id())->delete();
        DB::table('message')->where('user_id', '=', $user->id())->delete();
        DB::table('user')->where('user_id', '=', $user->id())->delete();
    }

    /**
     * @param User $contact_user
     *
     * @return string
     */
    public function contactLink(User $contact_user): string
    {
        $tree    = app(Tree::class);
        $user    = app(UserInterface::class);
        $request = app(ServerRequestInterface::class);

        if ($contact_user->getPreference('contactmethod') === 'mailto') {
            $url = 'mailto:' . $contact_user->email();
        } elseif ($user instanceof User) {
            // Logged-in users send direct messages
            $url = route('message', ['to' => $contact_user->userName()]);
        } else {
            // Visitors use the contact form.
            $url = route('contact', [
                'ged' => $tree ? $tree->name() : '',
                'to'  => $contact_user->userName(),
                'url' => $request->getUri(),
            ]);
        }

        return '<a href="' . e($url) . '" dir="auto">' . e($contact_user->realName()) . '</a>';
    }
}
