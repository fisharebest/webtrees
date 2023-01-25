<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function redirect;

/**
 * Set a new password.
 */
class PasswordResetPage implements RequestHandlerInterface, StatusCodeInterface
{
    use ViewResponseTrait;

    private UserService $user_service;

    /**
     * PasswordResetForm constructor.
     *
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
        $tree  = Validator::attributes($request)->treeOptional();
        $token = $request->getAttribute('token');
        $title = I18N::translate('Set a new password');
        $user  = $this->user_service->findByToken($token);

        if ($user instanceof User) {
            return $this->viewResponse('password-reset-page', [
                'title' => $title,
                'tree'  => $tree,
                'user'  => $user,
                'token' => $token,
            ]);
        }

        $message1 = I18N::translate('The password reset link has expired.');
        $message2 = I18N::translate('Please try again.');
        $message  = $message1 . '<br>' . $message2;

        FlashMessages::addMessage($message, 'danger');

        return redirect(route(PasswordRequestPage::class, ['tree' => $tree instanceof Tree ? $tree->name() : null]));
    }
}
