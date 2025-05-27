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

namespace Fisharebest\Webtrees\Services;

use Closure;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Http\RequestHandlers\ContactPage;
use Fisharebest\Webtrees\Http\RequestHandlers\MessagePage;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\User;
use Fisharebest\Webtrees\Validator;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Psr\Http\Message\ServerRequestInterface;

use function max;
use function time;

/**
 * Functions for managing users.
 */
class UserService
{
    /**
     * Find the user with a specified user_id.
     */
    public function find(int|null $user_id): User|null
    {
        return Registry::cache()->array()
            ->remember('user-' . $user_id, static fn (): User|null => DB::table('user')
                ->where('user_id', '=', $user_id)
                ->get()
                ->map(User::rowMapper())
                ->first());
    }

    /**
     * Find the user with a specified email address.
     */
    public function findByEmail(string $email): User|null
    {
        return DB::table('user')
            ->where('email', '=', $email)
            ->get()
            ->map(User::rowMapper())
            ->first();
    }

    /**
     * Find the user with a specified user_name or email address.
     */
    public function findByIdentifier(string $identifier): User|null
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
     * @return Collection<int,User>
     */
    public function findByIndividual(Individual $individual): Collection
    {
        return DB::table('user')
            ->join('user_gedcom_setting', 'user_gedcom_setting.user_id', '=', 'user.user_id')
            ->where('gedcom_id', '=', $individual->tree()->id())
            ->where('setting_value', '=', $individual->xref())
            ->where('setting_name', '=', UserInterface::PREF_TREE_ACCOUNT_XREF)
            ->select(['user.*'])
            ->get()
            ->map(User::rowMapper());
    }

    /**
     * Find the user with a specified password reset token.
     */
    public function findByToken(string $token): User|null
    {
        return DB::table('user')
            ->join('user_setting AS us1', 'us1.user_id', '=', 'user.user_id')
            ->where('us1.setting_name', '=', 'password-token')
            ->where('us1.setting_value', '=', $token)
            ->join('user_setting AS us2', 'us2.user_id', '=', 'user.user_id')
            ->where('us2.setting_name', '=', 'password-token-expire')
            ->where('us2.setting_value', '>', time())
            ->select(['user.*'])
            ->get()
            ->map(User::rowMapper())
            ->first();
    }

    /**
     * Find the user with a specified user_name.
     */
    public function findByUserName(string $user_name): User|null
    {
        return DB::table('user')
            ->where('user_name', '=', $user_name)
            ->get()
            ->map(User::rowMapper())
            ->first();
    }

    /**
     * Callback to sort users by their last-login (or registration) time.
     *
     * @return Closure(UserInterface,UserInterface):int
     */
    public function sortByLastLogin(): Closure
    {
        return static function (UserInterface $user1, UserInterface $user2) {
            $registered_at1 = (int) $user1->getPreference(UserInterface::PREF_TIMESTAMP_REGISTERED);
            $logged_in_at1  = (int) $user1->getPreference(UserInterface::PREF_TIMESTAMP_ACTIVE);
            $registered_at2 = (int) $user2->getPreference(UserInterface::PREF_TIMESTAMP_REGISTERED);
            $logged_in_at2  = (int) $user2->getPreference(UserInterface::PREF_TIMESTAMP_ACTIVE);

            return max($registered_at1, $logged_in_at1) <=> max($registered_at2, $logged_in_at2);
        };
    }

    /**
     * Callback to filter users who have not logged in since a given time.
     *
     * @param int $timestamp
     *
     * @return Closure(UserInterface):bool
     */
    public function filterInactive(int $timestamp): Closure
    {
        return static function (UserInterface $user) use ($timestamp): bool {
            $registered_at = (int) $user->getPreference(UserInterface::PREF_TIMESTAMP_REGISTERED);
            $logged_in_at  = (int) $user->getPreference(UserInterface::PREF_TIMESTAMP_ACTIVE);

            return max($registered_at, $logged_in_at) < $timestamp;
        };
    }

    /**
     * Get a list of all users.
     *
     * @return Collection<int,User>
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
     * @return Collection<int,User>
     */
    public function administrators(): Collection
    {
        return DB::table('user')
            ->join('user_setting', 'user_setting.user_id', '=', 'user.user_id')
            ->where('user_setting.setting_name', '=', UserInterface::PREF_IS_ADMINISTRATOR)
            ->where('user_setting.setting_value', '=', '1')
            ->where('user.user_id', '>', 0)
            ->orderBy('real_name')
            ->select(['user.*'])
            ->get()
            ->map(User::rowMapper());
    }

    /**
     * Get a list of all managers.
     *
     * @return Collection<int,User>
     */
    public function managers(): Collection
    {
        return DB::table('user')
            ->join('user_gedcom_setting', 'user_gedcom_setting.user_id', '=', 'user.user_id')
            ->where('user_gedcom_setting.setting_name', '=', UserInterface::PREF_TREE_ROLE)
            ->where('user_gedcom_setting.setting_value', '=', UserInterface::ROLE_MANAGER)
            ->where('user.user_id', '>', 0)
            ->orderBy('real_name')
            ->distinct()
            ->select(['user.*'])
            ->get()
            ->map(User::rowMapper());
    }

    /**
     * Get a list of all moderators.
     *
     * @return Collection<int,User>
     */
    public function moderators(): Collection
    {
        return DB::table('user')
            ->join('user_gedcom_setting', 'user_gedcom_setting.user_id', '=', 'user.user_id')
            ->where('user_gedcom_setting.setting_name', '=', UserInterface::PREF_TREE_ROLE)
            ->where('user_gedcom_setting.setting_value', '=', UserInterface::ROLE_MODERATOR)
            ->where('user.user_id', '>', 0)
            ->orderBy('real_name')
            ->distinct()
            ->select(['user.*'])
            ->get()
            ->map(User::rowMapper());
    }

    /**
     * Get a list of all verified users.
     *
     * @return Collection<int,User>
     */
    public function unapproved(): Collection
    {
        return DB::table('user')
            ->leftJoin('user_setting', static function (JoinClause $join): void {
                $join
                    ->on('user_setting.user_id', '=', 'user.user_id')
                    ->where('user_setting.setting_name', '=', UserInterface::PREF_IS_ACCOUNT_APPROVED);
            })
            ->where(static function (Builder $query): void {
                $query
                    ->where('user_setting.setting_value', '<>', '1')
                    ->orWhereNull('user_setting.setting_value');
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
     * @return Collection<int,User>
     */
    public function unverified(): Collection
    {
        return DB::table('user')
            ->leftJoin('user_setting', static function (JoinClause $join): void {
                $join
                    ->on('user_setting.user_id', '=', 'user.user_id')
                    ->where('user_setting.setting_name', '=', UserInterface::PREF_IS_EMAIL_VERIFIED);
            })
            ->where(static function (Builder $query): void {
                $query
                    ->where('user_setting.setting_value', '<>', '1')
                    ->orWhereNull('user_setting.setting_value');
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
     * @return Collection<int,User>
     */
    public function allLoggedIn(): Collection
    {
        return DB::table('user')
            ->join('session', 'session.user_id', '=', 'user.user_id')
            ->where('user.user_id', '>', 0)
            ->orderBy('real_name')
            ->distinct()
            ->select(['user.*'])
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
    public function create(string $user_name, string $real_name, string $email, #[\SensitiveParameter] string $password): User
    {
        DB::table('user')->insert([
            'user_name' => $user_name,
            'real_name' => $real_name,
            'email'     => $email,
            'password'  => password_hash($password, PASSWORD_DEFAULT),
            'secret'    => '',
        ]);

        $user_id = DB::lastInsertId();

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
     * @param User                   $contact_user
     * @param ServerRequestInterface $request
     *
     * @return string
     */
    public function contactLink(User $contact_user, ServerRequestInterface $request): string
    {
        $tree = Validator::attributes($request)->tree();
        $user = Validator::attributes($request)->user();

        if ($contact_user->getPreference(UserInterface::PREF_CONTACT_METHOD) === MessageService::CONTACT_METHOD_MAILTO) {
            $url = 'mailto:' . $contact_user->email();
        } elseif ($user instanceof User) {
            // Logged-in users send direct messages
            $url = route(MessagePage::class, [
                'to' => $contact_user->userName(),
                'tree' => $tree->name(),
                'url'  => (string) $request->getUri(),
            ]);
        } else {
            // Visitors use the contact form.
            $url = route(ContactPage::class, [
                'to'   => $contact_user->userName(),
                'tree' => $tree->name(),
                'url'  => (string) $request->getUri(),
            ]);
        }

        return '<a href="' . e($url) . '" dir="auto" rel="nofollow">' . e($contact_user->realName()) . '</a>';
    }
}
