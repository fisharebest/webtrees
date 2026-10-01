<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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
use Fisharebest\Database\Query\JoinOn;
use Fisharebest\Database\Query\Predicates;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Enums\ChangeStatus;
use Fisharebest\Webtrees\Enums\Role;
use Fisharebest\Webtrees\Http\Controllers\Contact;
use Fisharebest\Webtrees\Http\Controllers\Message;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\User;
use Fisharebest\Webtrees\Validator;
use Illuminate\Support\Collection;
use Psr\Clock\ClockInterface;
use Psr\Http\Message\ServerRequestInterface;

use function max;

/**
 * Functions for managing users.
 */
class UserService
{
    public function __construct(private readonly ClockInterface $clock)
    {
    }

    public function find(int|null $user_id): User|null
    {
        if ($user_id === null) {
            return null;
        }

        return Registry::cache()->array()
            ->remember('user-' . $user_id, static fn (): User|null => DB::query()
                ->select('*')
                ->from('user')
                ->whereEqual('user_id', $user_id)
                ->get(in: Collection::class)
                ->map(User::rowMapper())
                ->first());
    }

    public function findByEmail(string $email): User|null
    {
        return DB::query()
            ->select('*')
            ->from('user')
            ->whereEqual('email', $email)
            ->get(in: Collection::class)
            ->map(User::rowMapper())
            ->first();
    }

    public function findByIdentifier(string $identifier): User|null
    {
        return DB::query()
            ->select('*')
            ->from('user')
            ->whereEqual('user_name', $identifier)
            ->orWhereEqual('email', $identifier)
            ->get(in: Collection::class)
            ->map(User::rowMapper())
            ->first();
    }

    /**
     * @return Collection<int,User>
     */
    public function findByIndividual(Individual $individual): Collection
    {
        return DB::query()
            ->select('user.*')
            ->from('user')
            ->join('user_gedcom_setting', static fn (JoinOn $j): JoinOn => $j->on('user_gedcom_setting.user_id', 'user.user_id'))
            ->whereEqual('gedcom_id', $individual->tree()->id())
            ->whereEqual('setting_value', $individual->xref())
            ->whereEqual('setting_name', UserInterface::PREF_TREE_ACCOUNT_XREF)
            ->get(in: Collection::class)
            ->map(User::rowMapper());
    }

    public function findByToken(string $token): User|null
    {
        return DB::query()
            ->select('user.*')
            ->from('user')
            ->join('user_setting AS us1', static fn (JoinOn $j): JoinOn => $j->on('us1.user_id', 'user.user_id'))
            ->join('user_setting AS us2', static fn (JoinOn $j): JoinOn => $j->on('us2.user_id', 'user.user_id'))
            ->whereEqual('us1.setting_name', 'password-token')
            ->whereEqual('us1.setting_value', $token)
            ->whereEqual('us2.setting_name', 'password-token-expire')
            ->whereGreaterThan('us2.setting_value', $this->clock->now()->getTimestamp())
            ->get(in: Collection::class)
            ->map(User::rowMapper())
            ->first();
    }

    public function findByUserName(string $user_name): User|null
    {
        return DB::query()
            ->select('*')
            ->from('user')
            ->whereEqual('user_name', $user_name)
            ->get(in: Collection::class)
            ->map(User::rowMapper())
            ->first();
    }

    /**
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
     * @return Collection<int,User>
     */
    public function all(): Collection
    {
        return DB::query()
            ->select('*')
            ->from('user')
            ->whereGreaterThan('user_id', 0)
            ->orderBy('real_name')
            ->get(in: Collection::class)
            ->map(User::rowMapper());
    }

    /**
     * @return Collection<int,User>
     */
    public function administrators(): Collection
    {
        return DB::query()
            ->select('user.*')
            ->from('user')
            ->join('user_setting', static fn (JoinOn $j): JoinOn => $j->on('user_setting.user_id', 'user.user_id'))
            ->whereEqual('user_setting.setting_name', UserInterface::PREF_IS_ADMINISTRATOR)
            ->whereEqual('user_setting.setting_value', '1')
            ->whereGreaterThan('user.user_id', 0)
            ->orderBy('real_name')
            ->get(in: Collection::class)
            ->map(User::rowMapper());
    }

    /**
     * @return Collection<int,User>
     */
    public function managers(): Collection
    {
        return DB::query()
            ->select('user.*')
            ->distinct()
            ->from('user')
            ->join('user_gedcom_setting', static fn (JoinOn $j): JoinOn => $j->on('user_gedcom_setting.user_id', 'user.user_id'))
            ->whereEqual('user_gedcom_setting.setting_name', UserInterface::PREF_TREE_ROLE)
            ->whereEqual('user_gedcom_setting.setting_value', Role::Manager->value)
            ->whereGreaterThan('user.user_id', 0)
            ->orderBy('real_name')
            ->get(in: Collection::class)
            ->map(User::rowMapper());
    }

    /**
     * @return Collection<int,User>
     */
    public function moderators(): Collection
    {
        return DB::query()
            ->select('user.*')
            ->distinct()
            ->from('user')
            ->join('user_gedcom_setting', static fn (JoinOn $j): JoinOn => $j->on('user_gedcom_setting.user_id', 'user.user_id'))
            ->whereEqual('user_gedcom_setting.setting_name', UserInterface::PREF_TREE_ROLE)
            ->whereEqual('user_gedcom_setting.setting_value', Role::Moderator->value)
            ->whereGreaterThan('user.user_id', 0)
            ->orderBy('real_name')
            ->get(in: Collection::class)
            ->map(User::rowMapper());
    }

    /**
     * @return Collection<int,User>
     */
    public function unapproved(): Collection
    {
        return DB::query()
            ->select('user.*')
            ->from('user')
            ->leftJoin('user_setting', static fn (JoinOn $j): JoinOn => $j
                ->on('user_setting.user_id', 'user.user_id')
                ->on('user_setting.setting_name', DB::value(UserInterface::PREF_IS_ACCOUNT_APPROVED)))
            ->whereNested(static fn (Predicates $p): Predicates => $p
                ->notEqual('user_setting.setting_value', '1')
                ->orIsNull('user_setting.setting_value'))
            ->whereGreaterThan('user.user_id', 0)
            ->orderBy('real_name')
            ->get(in: Collection::class)
            ->map(User::rowMapper());
    }

    /**
     * @return Collection<int,User>
     */
    public function unverified(): Collection
    {
        return DB::query()
            ->select('user.*')
            ->from('user')
            ->leftJoin('user_setting', static fn (JoinOn $j): JoinOn => $j
                ->on('user_setting.user_id', 'user.user_id')
                ->on('user_setting.setting_name', DB::value(UserInterface::PREF_IS_EMAIL_VERIFIED)))
            ->whereNested(static fn (Predicates $p): Predicates => $p
                ->notEqual('user_setting.setting_value', '1')
                ->orIsNull('user_setting.setting_value'))
            ->whereGreaterThan('user.user_id', 0)
            ->orderBy('real_name')
            ->get(in: Collection::class)
            ->map(User::rowMapper());
    }

    /**
     * @return Collection<int,User>
     */
    public function allLoggedIn(): Collection
    {
        return DB::query()
            ->select('user.*')
            ->distinct()
            ->from('user')
            ->join('session', static fn (JoinOn $j): JoinOn => $j->on('session.user_id', 'user.user_id'))
            ->whereGreaterThan('user.user_id', 0)
            ->orderBy('real_name')
            ->get(in: Collection::class)
            ->map(User::rowMapper());
    }

    public function create(string $user_name, string $real_name, string $email, #[\SensitiveParameter] string $password): User
    {
        $user_id = DB::query(table: 'user')->insertGetId([
            'user_name' => $user_name,
            'real_name' => $real_name,
            'email'     => $email,
            'password'  => password_hash($password, PASSWORD_DEFAULT),
        ], 'user_id');

        return new User($user_id, $user_name, $real_name, $email);
    }

    public function delete(User $user): void
    {
        DB::query(table: 'session')
            ->whereEqual('user_id', $user->id())
            ->delete();

        // Don't delete the logs, just set the user to null.
        DB::query(table: 'log')
            ->whereEqual('user_id', $user->id())
            ->update(['user_id' => null]);

        // Take over the user's pending changes. (What else could we do with them?)
        DB::query(table: 'change')
            ->whereEqual('user_id', $user->id())
            ->whereEqual('status', ChangeStatus::Rejected->value)
            ->delete();

        DB::query(table: 'change')
            ->whereEqual('user_id', $user->id())
            ->update(['user_id' => Auth::id()]);

        // Delete settings and preferences
        DB::query(table: 'block_setting')
            ->whereInSubquery('block_id', DB::query()
                ->select('block_id')
                ->from('block')
                ->whereEqual('user_id', $user->id()))
            ->delete();

        DB::query(table: 'block')->whereEqual('user_id', $user->id())->delete();
        DB::query(table: 'user_gedcom_setting')->whereEqual('user_id', $user->id())->delete();
        DB::query(table: 'user_setting')->whereEqual('user_id', $user->id())->delete();
        DB::query(table: 'message')->whereEqual('user_id', $user->id())->delete();

        // SQL-Server cannot handle foreign key constraints with multiple paths, so we do this manually.
        DB::query(table: 'gedcom')->whereEqual('contact_user_id', $user->id())->update(['contact_user_id' => null]);
        DB::query(table: 'gedcom')->whereEqual('support_user_id', $user->id())->update(['support_user_id' => null]);

        DB::query(table: 'user')->whereEqual('user_id', $user->id())->delete();
    }

    public function contactLink(User $contact_user, ServerRequestInterface $request): string
    {
        $tree = Validator::attributes($request)->tree();
        $user = Validator::attributes($request)->user();

        if ($user instanceof User) {
            // Logged-in users send direct messages
            $url = route(Message::class, [
                'to'   => $contact_user->userName(),
                'tree' => $tree->name(),
                'url'  => (string) $request->getUri(),
            ]);
        } else {
            // Visitors use the contact form.
            $url = route(Contact::class, [
                'to'   => $contact_user->userName(),
                'tree' => $tree->name(),
                'url'  => (string) $request->getUri(),
            ]);
        }

        return '<a href="' . e($url) . '" dir="auto" rel="nofollow">' . e($contact_user->realName()) . '</a>';
    }
}
