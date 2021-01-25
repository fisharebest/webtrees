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
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function redirect;
use function route;

/**
 * Edit user account details.
 */
class AccountUpdate implements RequestHandlerInterface
{
    /** @var UserService */
    private $user_service;

    /**
     * AccountController constructor.
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
        $tree = $request->getAttribute('tree');

        $user = $request->getAttribute('user');
        assert($user instanceof User);

        $params = (array) $request->getParsedBody();

        $contact_method = $params['contact-method'];
        $email          = $params['email'];
        $language       = $params['language'];
        $real_name      = $params['real_name'];
        $password       = $params['password'];
        $time_zone      = $params['timezone'];
        $user_name      = $params['user_name'];
        $visible_online = $params['visible-online'] ?? '';

        // Change the password
        if ($password !== '') {
            $user->setPassword($password);
        }

        // Change the username
        if ($user_name !== $user->userName()) {
            if ($this->user_service->findByUserName($user_name) === null) {
                $user->setUserName($user_name);
            } else {
                FlashMessages::addMessage(I18N::translate('Duplicate username. A user with that username already exists. Please choose another username.'));
            }
        }

        // Change the email
        if ($email !== $user->email()) {
            if ($this->user_service->findByEmail($email) === null) {
                $user->setEmail($email);
            } else {
                FlashMessages::addMessage(I18N::translate('Duplicate email address. A user with that email already exists.'));
            }
        }

        $user->setRealName($real_name);
        $user->setPreference(UserInterface::PREF_CONTACT_METHOD, $contact_method);
        $user->setPreference(UserInterface::PREF_LANGUAGE, $language);
        $user->setPreference(UserInterface::PREF_TIME_ZONE, $time_zone);
        $user->setPreference(UserInterface::PREF_IS_VISIBLE_ONLINE, $visible_online);

        if ($tree instanceof Tree) {
            $default_xref = $params['default-xref'];
            $tree->setUserPreference($user, UserInterface::PREF_TREE_DEFAULT_XREF, $default_xref);
        }

        // Switch to the new language now
        Session::put('language', $language);

        FlashMessages::addMessage(I18N::translate('The details for “%s” have been updated.', e($user->username())), 'success');

        return redirect(route(HomePage::class, ['tree' => $tree instanceof Tree ? $tree->name() : null]));
    }
}
