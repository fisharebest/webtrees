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

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function e;
use function redirect;
use function route;

final class UsersCleanup
{
    use ViewResponseTrait;

    public function __construct(
        private readonly UserService $user_service,
    ) {
    }

    public function get(): ResponseInterface
    {
        $inactive_threshold   = Registry::timestampFactory()->now()->subMonths(6)->getTimestamp();
        $unverified_threshold = Registry::timestampFactory()->now()->subDays(7)->getTimestamp();

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

    public function post(ServerRequestInterface $request): ResponseInterface
    {
        $delete = Validator::parsedBody($request)->list('delete');

        foreach ($delete as $user_id) {
            $user = $this->user_service->find((int) $user_id);
            if ($user instanceof UserInterface) {
                $this->user_service->delete($user);

                Log::addAuthenticationLog('Deleted user: ' . $user->userName());

                FlashMessages::addMessage(I18N::translate('The user %s has been deleted.', e($user->userName())), 'success');
            }
        }

        return redirect(route(self::class));
    }
}
