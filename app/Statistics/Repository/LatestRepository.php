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
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Functions\FunctionsDate;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\LatestRepositoryInterface;
use Fisharebest\Webtrees\User;

/**
 * Statistics submodule providing all LATEST related methods.
 */
class LatestRepository implements LatestRepositoryInterface
{
    /**
     * Find the newest user on the site.
     *
     * If no user has registered (i.e. all created by the admin), then
     * return the current user.
     *
     * @return User
     */
    private function latestUser(): User
    {
        static $user;

        if ($user instanceof User) {
            return $user;
        }

        $user_id = (int) Database::prepare(
            "SELECT u.user_id" .
            " FROM `##user` u" .
            " LEFT JOIN `##user_setting` us ON (u.user_id=us.user_id AND us.setting_name='reg_timestamp') " .
            " ORDER BY us.setting_value DESC LIMIT 1"
        )->execute()->fetchOne();

        $user = User::find($user_id) ?? Auth::user();

        return $user;
    }

    /**
     * Get the newest registered user's ID.
     *
     * @return string
     */
    public function latestUserId(): string
    {
        return (string) $this->latestUser()->id();
    }

    /**
     * Get the newest registered user's username.
     *
     * @return string
     */
    public function latestUserName(): string
    {
        return e($this->latestUser()->getUserName());
    }

    /**
     * Get the newest registered user's real name.
     *
     * @return string
     */
    public function latestUserFullName(): string
    {
        return e($this->latestUser()->getRealName());
    }

    /**
     * Get the date of the newest user registration.
     *
     * @param string|null $format
     *
     * @return string
     */
    public function latestUserRegDate(string $format = null): string
    {
        $format = $format ?? I18N::dateFormat();
        $user   = $this->latestUser();

        return FunctionsDate::timestampToGedcomDate(
            (int) $user->getPreference('reg_timestamp')
        )->display(false, $format);
    }

    /**
     * Find the timestamp of the latest user to register.
     *
     * @param string|null $format
     *
     * @return string
     */
    public function latestUserRegTime(string $format = null): string
    {
        $format = $format ?? str_replace('%', '', I18N::timeFormat());
        $user   = $this->latestUser();

        return date($format, (int) $user->getPreference('reg_timestamp'));
    }

    /**
     * Is the most recently registered user logged in right now?
     *
     * @param string|null $yes
     * @param string|null $no
     *
     * @return string
     */
    public function latestUserLoggedin(string $yes = null, string $no = null): string
    {
        $yes = $yes ?? I18N::translate('yes');
        $no  = $no ?? I18N::translate('no');

        $user = $this->latestUser();

        $is_logged_in = (bool) Database::prepare(
            "SELECT 1 FROM `##session` WHERE user_id = :user_id LIMIT 1"
        )->execute([
            'user_id' => $user->id()
        ])->fetchOne();

        return $is_logged_in ? $yes : $no;
    }
}
