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
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\UserRepositoryInterface;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;

/**
 * Statistics submodule providing all USER related methods.
 */
class UserRepository implements UserRepositoryInterface
{
    /**
     * @var Tree
     */
    private $tree;

    /**
     * Constructor.
     *
     * @param Tree $tree
     */
    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    /**
     * Who is currently logged in?
     *
     * @TODO - this is duplicated from the LoggedInUsersModule class.
     *
     * @param string $type
     *
     * @return string
     */
    private function usersLoggedInQuery($type = 'nolist'): string
    {
        $content = '';

        // List active users
        $NumAnonymous = 0;
        $loggedusers  = [];

        foreach (User::allLoggedIn() as $user) {
            if (Auth::isAdmin() || $user->getPreference('visibleonline')) {
                $loggedusers[] = $user;
            } else {
                $NumAnonymous++;
            }
        }

        $LoginUsers = count($loggedusers);
        if ($LoginUsers === 0 && $NumAnonymous === 0) {
            return I18N::translate('No signed-in and no anonymous users');
        }

        if ($NumAnonymous > 0) {
            $content .= '<b>' . I18N::plural('%s anonymous signed-in user', '%s anonymous signed-in users', $NumAnonymous, I18N::number($NumAnonymous)) . '</b>';
        }

        if ($LoginUsers > 0) {
            if ($NumAnonymous) {
                if ($type === 'list') {
                    $content .= '<br><br>';
                } else {
                    $content .= ' ' . I18N::translate('and') . ' ';
                }
            }
            $content .= '<b>' . I18N::plural('%s signed-in user', '%s signed-in users', $LoginUsers, I18N::number($LoginUsers)) . '</b>';
            if ($type === 'list') {
                $content .= '<ul>';
            } else {
                $content .= ': ';
            }
        }

        if (Auth::check()) {
            foreach ($loggedusers as $user) {
                if ($type === 'list') {
                    $content .= '<li>' . e($user->getRealName()) . ' - ' . e($user->getUserName());
                } else {
                    $content .= e($user->getRealName()) . ' - ' . e($user->getUserName());
                }
                if (Auth::id() !== $user->id() && $user->getPreference('contactmethod') !== 'none') {
                    if ($type === 'list') {
                        $content .= '<br>';
                    }
                    $content .= '<a href="' . e(route('message', ['to'  => $user->getUserName(), 'ged' => $this->tree->name()])) . '" class="btn btn-link" title="' . I18N::translate('Send a message') . '">' . view('icons/email') . '</a>';
                }
                if ($type === 'list') {
                    $content .= '</li>';
                }
            }
        }

        if ($type === 'list') {
            $content .= '</ul>';
        }

        return $content;
    }

    /**
     * NUmber of users who are currently logged in?
     *
     * @param string $type
     *
     * @return int
     */
    private function usersLoggedInTotalQuery($type = 'all'): int
    {
        $anon    = 0;
        $visible = 0;

        foreach (User::allLoggedIn() as $user) {
            if (Auth::isAdmin() || $user->getPreference('visibleonline')) {
                $visible++;
            } else {
                $anon++;
            }
        }

        if ($type === 'anon') {
            return $anon;
        }

        if ($type === 'visible') {
            return $visible;
        }

        return $visible + $anon;
    }

    /**
     * Who is currently logged in?
     *
     * @return string
     */
    public function usersLoggedIn(): string
    {
        return $this->usersLoggedInQuery();
    }

    /**
     * Who is currently logged in?
     *
     * @return string
     */
    public function usersLoggedInList(): string
    {
        return $this->usersLoggedInQuery('list');
    }

    /**
     * Who is currently logged in?
     *
     * @return int
     */
    public function usersLoggedInTotal(): int
    {
        return $this->usersLoggedInTotalQuery();
    }

    /**
     * Which visitors are currently logged in?
     *
     * @return int
     */
    public function usersLoggedInTotalAnon(): int
    {
        return $this->usersLoggedInTotalQuery('anon');
    }

    /**
     * Which visitors are currently logged in?
     *
     * @return int
     */
    public function usersLoggedInTotalVisible(): int
    {
        return $this->usersLoggedInTotalQuery('visible');
    }

    /**
     * Get the current user's ID.
     *
     * @return string
     */
    public function userId(): string
    {
        return (string) Auth::id();
    }

    /**
     * Get the current user's username.
     *
     * @param string $visitor_text
     *
     * @return string
     */
    public function userName(string $visitor_text = ''): string
    {
        if (Auth::check()) {
            return e(Auth::user()->getUserName());
        }

        // if #username:visitor# was specified, then "visitor" will be returned when the user is not logged in
        return e($visitor_text);
    }

    /**
     * Get the current user's full name.
     *
     * @return string
     */
    public function userFullName(): string
    {
        return Auth::check() ? '<span dir="auto">' . e(Auth::user()->getRealName()) . '</span>' : '';
    }
}
