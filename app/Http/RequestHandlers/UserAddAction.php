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

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Site;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function route;

/**
 * Add a user.
 */
class UserAddAction implements RequestHandlerInterface
{
    private UserService $user_service;

    /**
     * UserAddAction constructor.
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
        $params = (array) $request->getParsedBody();

        $username  = $params['username'] ?? '';
        $real_name = $params['real_name'] ?? '';
        $email     = $params['email'] ?? '';
        $password  = $params['password'] ?? '';

        $errors = false;
        if ($this->user_service->findByUserName($username)) {
            FlashMessages::addMessage(I18N::translate('Duplicate username. A user with that username already exists. Please choose another username.'));
            $errors = true;
        }

        if ($this->user_service->findByEmail($email)) {
            FlashMessages::addMessage(I18N::translate('Duplicate email address. A user with that email already exists.'));
            $errors = true;
        }

        if ($errors) {
            $url = route(UserAddPage::class, [
                'email'     => $email,
                'real_name' => $real_name,
                'username'  => $username,
            ]);

            return redirect($url);
        }

        $new_user = $this->user_service->create($username, $real_name, $email, $password);
        $new_user->setPreference(UserInterface::PREF_IS_EMAIL_VERIFIED, '1');
        $new_user->setPreference(UserInterface::PREF_LANGUAGE, I18N::languageTag());
        $new_user->setPreference(UserInterface::PREF_TIME_ZONE, Site::getPreference('TIMEZONE'));
        $new_user->setPreference(UserInterface::PREF_TIMESTAMP_REGISTERED, date('U'));
        $new_user->setPreference(UserInterface::PREF_TIMESTAMP_ACTIVE, '0');

        Log::addAuthenticationLog('User ->' . $username . '<- created');

        $url = route(UserEditPage::class, [
            'user_id' => $new_user->id(),
        ]);

        return redirect($url);
    }
}
