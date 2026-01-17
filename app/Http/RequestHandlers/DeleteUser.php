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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Http\Exceptions\HttpAccessDeniedException;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Services\UserService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function response;

final class DeleteUser implements RequestHandlerInterface
{
    public function __construct(
        private readonly UserService $user_service,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $user_id = (int) $request->getAttribute('user_id');

        $user = $this->user_service->find($user_id);

        if ($user === null) {
            throw new HttpNotFoundException('User ID ' . $user_id . ' not found');
        }

        if (Auth::isAdmin($user)) {
            throw new HttpAccessDeniedException('Cannot delete an administrator');
        }

        Log::addAuthenticationLog('Deleted user: ' . $user->userName());
        $this->user_service->delete($user);

        return response();
    }
}
