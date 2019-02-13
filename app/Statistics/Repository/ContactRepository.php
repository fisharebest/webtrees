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

use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\ContactRepositoryInterface;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;

/**
 * A repository providing methods for contact related statistics.
 */
class ContactRepository implements ContactRepositoryInterface
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
     * @inheritDoc
     */
    public function contactWebmaster(): string
    {
        $user_id = (int) $this->tree->getPreference('WEBMASTER_USER_ID');
        $user    = $this->user_service->find($user_id);

        if ($user instanceof User) {
            return $this->user_service->contactLink($user);
        }

        return '';
    }

    /**
     * @inheritDoc
     */
    public function contactGedcom(): string
    {
        $user_id = (int) $this->tree->getPreference('CONTACT_USER_ID');
        $user    = $this->user_service->find($user_id);

        if ($user instanceof User) {
            return $this->user_service->contactLink($user);
        }

        return '';
    }
}
