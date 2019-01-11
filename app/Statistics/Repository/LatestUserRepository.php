<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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

namespace Fisharebest\Webtrees\Statistics\Repository;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Functions\FunctionsDate;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\LatestUserRepositoryInterface;
use Fisharebest\Webtrees\User;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;

/**
 * A repository providing methods for latest user related statistics.
 */
class LatestUserRepository implements LatestUserRepositoryInterface
{
    /**
     * Find the newest user on the site.
     *
     * If no user has registered (i.e. all created by the admin), then
     * return the current user.
     *
     * @return User
     */
    private function latestUserQuery(): User
    {
        static $user;

        if ($user instanceof User) {
            return $user;
        }

        $user_id = DB::table('user as u')
            ->select(['u.user_id'])
            ->leftJoin('user_setting as us', function (JoinClause $join) {
                $join->on(function (Builder $query) {
                    $query->whereColumn('u.user_id', '=', 'us.user_id')
                        ->where('us.setting_name', '=', 'reg_timestamp');
                });
            })
            ->orderByDesc('us.setting_value')
            ->value('user_id');

        $user = User::find($user_id) ?? Auth::user();

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function latestUserId(): string
    {
        return (string) $this->latestUserQuery()->id();
    }

    /**
     * @inheritDoc
     */
    public function latestUserName(): string
    {
        return e($this->latestUserQuery()->getUserName());
    }

    /**
     * @inheritDoc
     */
    public function latestUserFullName(): string
    {
        return e($this->latestUserQuery()->getRealName());
    }

    /**
     * @inheritDoc
     */
    public function latestUserRegDate(string $format = null): string
    {
        $format = $format ?? I18N::dateFormat();
        $user   = $this->latestUserQuery();

        return FunctionsDate::timestampToGedcomDate(
            (int) $user->getPreference('reg_timestamp')
        )->display(false, $format);
    }

    /**
     * @inheritDoc
     */
    public function latestUserRegTime(string $format = null): string
    {
        $format = $format ?? str_replace('%', '', I18N::timeFormat());
        $user   = $this->latestUserQuery();

        return date($format, (int) $user->getPreference('reg_timestamp'));
    }

    /**
     * @inheritDoc
     */
    public function latestUserLoggedin(string $yes = null, string $no = null): string
    {
        $yes  = $yes ?? I18N::translate('yes');
        $no   = $no ?? I18N::translate('no');
        $user = $this->latestUserQuery();

        $is_logged_in = DB::table('session')
            ->selectRaw('1')
            ->where('user_id', '=', $user->id())
            ->first();

        return $is_logged_in ? $yes : $no;
    }
}
