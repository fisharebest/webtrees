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

namespace Fisharebest\Webtrees\Statistics\Repository;

use function count;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\UserRepositoryInterface;
use Fisharebest\Webtrees\Tree;

/**
 * A repository providing methods for user related statistics.
 */
class UserRepository implements UserRepositoryInterface
{
    /**
     * @var Tree
     */
    private $tree;
    /**
     * @var UserService
     */
    private $user_service;

    /**
     * Constructor.
     *
     * @param Tree        $tree
     * @param UserService $user_service
     */
    public function __construct(Tree $tree, UserService $user_service)
    {
        $this->tree         = $tree;
        $this->user_service = $user_service;
    }

    /**
     * Who is currently logged in?
     *
     * @param string $type "list" or "nolist"
     *
     * @return string
     */
    private function usersLoggedInQuery($type = 'nolist'): string
    {
        $content   = '';
        $anonymous = 0;
        $logged_in = [];

        foreach ($this->user_service->allLoggedIn() as $user) {
            if (Auth::isAdmin() || $user->getPreference('visibleonline')) {
                $logged_in[] = $user;
            } else {
                $anonymous++;
            }
        }

        $count_logged_in = count($logged_in);

        if ($count_logged_in === 0 && $anonymous === 0) {
            $content .= I18N::translate('No signed-in and no anonymous users');
        }

        if ($anonymous > 0) {
            $content .= '<b>' . I18N::plural('%s anonymous signed-in user', '%s anonymous signed-in users', $anonymous, I18N::number($anonymous)) . '</b>';
        }

        if ($count_logged_in > 0) {
            if ($anonymous) {
                if ($type === 'list') {
                    $content .= '<br><br>';
                } else {
                    $content .= ' ' . I18N::translate('and') . ' ';
                }
            }
            $content .= '<b>' . I18N::plural('%s signed-in user', '%s signed-in users', $count_logged_in, I18N::number($count_logged_in)) . '</b>';
            if ($type === 'list') {
                $content .= '<ul>';
            } else {
                $content .= ': ';
            }
        }

        if (Auth::check()) {
            foreach ($logged_in as $user) {
                if ($type === 'list') {
                    $content .= '<li>';
                }

                $individual = Individual::getInstance($this->tree->getUserPreference($user, 'gedcomid'), $this->tree);

                if ($individual instanceof Individual && $individual->canShow()) {
                    $content .= '<a href="' . e($individual->url()) . '">' . e($user->realName()) . '</a>';
                } else {
                    $content .= e($user->realName());
                }

                $content .= ' - ' . e($user->userName());

                if (($user->getPreference('contactmethod') !== 'none')
                    && (Auth::id() !== $user->id())
                ) {
                    if ($type === 'list') {
                        $content .= '<br>';
                    }
                    $content .= '<a href="' . e(route('message', ['to' => $user->userName(), 'ged' => $this->tree->name()])) . '" class="btn btn-link" title="' . I18N::translate('Send a message') . '">' . view('icons/email') . '</a>';
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
     * @inheritDoc
     */
    public function usersLoggedIn(): string
    {
        return $this->usersLoggedInQuery('nolist');
    }

    /**
     * @inheritDoc
     */
    public function usersLoggedInList(): string
    {
        return $this->usersLoggedInQuery('list');
    }

    /**
     * Returns true if the given user is visible to others.
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    private function isUserVisible(UserInterface $user): bool
    {
        return Auth::isAdmin() || $user->getPreference('visibleonline');
    }

    /**
     * @inheritDoc
     */
    public function usersLoggedInTotal(): int
    {
        return count($this->user_service->allLoggedIn());
    }

    /**
     * @inheritDoc
     */
    public function usersLoggedInTotalAnon(): int
    {
        $anonymous = 0;

        foreach ($this->user_service->allLoggedIn() as $user) {
            if (!$this->isUserVisible($user)) {
                ++$anonymous;
            }
        }

        return $anonymous;
    }

    /**
     * @inheritDoc
     */
    public function usersLoggedInTotalVisible(): int
    {
        $visible = 0;

        foreach ($this->user_service->allLoggedIn() as $user) {
            if ($this->isUserVisible($user)) {
                ++$visible;
            }
        }

        return $visible;
    }

    /**
     * @inheritDoc
     */
    public function userId(): string
    {
        return (string) Auth::id();
    }

    /**
     * @inheritDoc
     */
    public function userName(string $visitor_text = ''): string
    {
        if (Auth::check()) {
            return e(Auth::user()->userName());
        }

        // if #username:visitor# was specified, then "visitor" will be returned when the user is not logged in
        return e($visitor_text);
    }

    /**
     * @inheritDoc
     */
    public function userFullName(): string
    {
        return Auth::check() ? '<span dir="auto">' . e(Auth::user()->realName()) . '</span>' : '';
    }

    /**
     * Returns the user count.
     *
     * @return int
     */
    private function getUserCount(): int
    {
        return count($this->user_service->all());
    }

    /**
     * Returns the administrator count.
     *
     * @return int
     */
    private function getAdminCount(): int
    {
        return count($this->user_service->administrators());
    }

    /**
     * @inheritDoc
     */
    public function totalUsers(): string
    {
        return I18N::number($this->getUserCount());
    }

    /**
     * @inheritDoc
     */
    public function totalAdmins(): string
    {
        return I18N::number($this->getAdminCount());
    }

    /**
     * @inheritDoc
     */
    public function totalNonAdmins(): string
    {
        return I18N::number($this->getUserCount() - $this->getAdminCount());
    }
}
