<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Carbon;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\UserService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Delete old/inactive users.
 */
class UsersCleanupPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private UserService $user_service;

    /**
     * @param UserService $user_service
     */
    public function __construct(UserService $user_service)
    {
        $this->user_service = $user_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $inactive_threshold   = Carbon::now()->subMonths(6)->getTimestamp();
        $unverified_threshold = Carbon::now()->subDays(7)->getTimestamp();

        $inactive_users = $this->user_service->all()
            ->filter($this->user_service->filterInactive($inactive_threshold))
            ->sort($this->user_service->sortByLastLogin());

        $unverified_users = $this->user_service->unverified()
            ->filter($this->user_service->filterInactive($unverified_threshold))
            ->sort($this->user_service->sortByLastLogin());

        $title = I18N::translate('Delete inactive users');

        $this->layout = 'layouts/administration';

        return $this->viewResponse('admin/users-cleanup', [
            'title'            => $title,
            'inactive_users'   => $inactive_users,
            'unverified_users' => $unverified_users,
        ]);
    }
}
