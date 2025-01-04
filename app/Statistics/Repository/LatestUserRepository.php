<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Statistics\Repository;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\LatestUserRepositoryInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;

use function date;
use function e;
use function str_replace;

/**
 * A repository providing methods for latest user related statistics.
 */
class LatestUserRepository implements LatestUserRepositoryInterface
{
    private UserService $user_service;

    public function __construct(UserService $user_service)
    {
        $this->user_service = $user_service;
    }

    public function latestUserId(): string
    {
        return (string) $this->latestUserQuery()->id();
    }

    private function latestUserQuery(): UserInterface
    {
        static $user;

        if ($user instanceof UserInterface) {
            return $user;
        }

        $user_id = DB::table('user as u')
            ->select(['u.user_id'])
            ->leftJoin('user_setting as us', static function (JoinClause $join): void {
                $join->on(static function (Builder $query): void {
                    $query->whereColumn('u.user_id', '=', 'us.user_id')
                        ->where('us.setting_name', '=', UserInterface::PREF_TIMESTAMP_REGISTERED);
                });
            })
            ->orderByDesc('us.setting_value')
            ->value('user_id');

        if ($user_id !== null) {
            $user_id = (int) $user_id;
        }

        return $this->user_service->find($user_id) ?? Auth::user();
    }

    public function latestUserName(): string
    {
        return e($this->latestUserQuery()->userName());
    }

    public function latestUserFullName(): string
    {
        return e($this->latestUserQuery()->realName());
    }

    public function latestUserRegDate(string|null $format = null): string
    {
        $format ??= I18N::dateFormat();
        $user      = $this->latestUserQuery();
        $timestamp = (int) $user->getPreference(UserInterface::PREF_TIMESTAMP_REGISTERED);

        return Registry::timestampFactory()->make($timestamp)->format(strtr($format, ['%' => '']));
    }

    public function latestUserRegTime(string|null $format = null): string
    {
        $format ??= str_replace('%', '', I18N::timeFormat());
        $user = $this->latestUserQuery();

        return date($format, (int) $user->getPreference(UserInterface::PREF_TIMESTAMP_REGISTERED));
    }

    public function latestUserLoggedin(string|null $yes = null, string|null $no = null): string
    {
        $yes ??= I18N::translate('yes');
        $no ??= I18N::translate('no');
        $user = $this->latestUserQuery();

        $is_logged_in = DB::table('session')
            ->selectRaw('1')
            ->where('user_id', '=', $user->id())
            ->first();

        return $is_logged_in !== null ? $yes : $no;
    }
}
