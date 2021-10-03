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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\EmailService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\SiteUser;
use Fisharebest\Webtrees\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function route;

/**
 * Edit a user.
 */
class UserEditAction implements RequestHandlerInterface
{
    private EmailService $email_service;

    private UserService $user_service;

    private TreeService $tree_service;

    /**
     * UserEditAction constructor.
     *
     * @param EmailService $email_service
     * @param TreeService  $tree_service
     * @param UserService  $user_service
     */
    public function __construct(
        EmailService $email_service,
        TreeService $tree_service,
        UserService $user_service
    ) {
        $this->email_service = $email_service;
        $this->tree_service  = $tree_service;
        $this->user_service  = $user_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $user = $request->getAttribute('user');

        $params = (array) $request->getParsedBody();

        $user_id        = (int) $params['user_id'];
        $username       = $params['username'] ?? '';
        $real_name      = $params['real_name'] ?? '';
        $email          = $params['email'] ?? '';
        $password       = $params['password'] ?? '';
        $theme          = $params['theme'] ?? '';
        $language       = $params['language'] ?? '';
        $timezone       = $params['timezone'] ?? '';
        $contact_method = $params['contact-method'] ?? '';
        $comment        = $params['comment'] ?? '';
        $auto_accept    = (bool) ($params[UserInterface::PREF_AUTO_ACCEPT_EDITS] ?? '');
        $canadmin       = (bool) ($params[UserInterface::PREF_IS_ADMINISTRATOR] ?? '');
        $visible_online = (bool) ($params['visible-online'] ?? '');
        $verified       = (bool) ($params[UserInterface::PREF_IS_EMAIL_VERIFIED] ?? '');
        $approved       = (bool) ($params['approved'] ?? '');

        $edit_user = $this->user_service->find($user_id);

        if ($edit_user === null) {
            throw new HttpNotFoundException(I18N::translate('%1$s does not exist', 'user_id:' . $user_id));
        }

        // We have just approved a user.  Tell them
        if ($approved && $edit_user->getPreference(UserInterface::PREF_IS_ACCOUNT_APPROVED) !== '1') {
            I18N::init($edit_user->getPreference(UserInterface::PREF_LANGUAGE));

            $base_url = $request->getAttribute('base_url');

            $this->email_service->send(
                new SiteUser(),
                $edit_user,
                Auth::user(),
                /* I18N: %s is a server name/URL */
                I18N::translate('New user at %s', $base_url),
                view('emails/approve-user-text', ['user' => $edit_user, 'base_url' => $base_url]),
                view('emails/approve-user-html', ['user' => $edit_user, 'base_url' => $base_url])
            );
        }

        $edit_user->setRealName($real_name);
        $edit_user->setPreference(UserInterface::PREF_THEME, $theme);
        $edit_user->setPreference(UserInterface::PREF_LANGUAGE, $language);
        $edit_user->setPreference(UserInterface::PREF_TIME_ZONE, $timezone);
        $edit_user->setPreference(UserInterface::PREF_CONTACT_METHOD, $contact_method);
        $edit_user->setPreference(UserInterface::PREF_NEW_ACCOUNT_COMMENT, $comment);
        $edit_user->setPreference(UserInterface::PREF_AUTO_ACCEPT_EDITS, (string) $auto_accept);
        $edit_user->setPreference(UserInterface::PREF_IS_VISIBLE_ONLINE, (string) $visible_online);
        $edit_user->setPreference(UserInterface::PREF_IS_EMAIL_VERIFIED, (string) $verified);
        $edit_user->setPreference(UserInterface::PREF_IS_ACCOUNT_APPROVED, (string) $approved);

        if ($password !== '') {
            $edit_user->setPassword($password);
        }

        // We cannot change our own admin status. Another admin will need to do it.
        if ($edit_user->id() !== $user->id()) {
            $edit_user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, $canadmin ? '1' : '');
        }

        foreach ($this->tree_service->all() as $tree) {
            $path_length = (int) $params['RELATIONSHIP_PATH_LENGTH' . $tree->id()];
            $gedcom_id   = $params['gedcomid' . $tree->id()] ?? '';
            $can_edit    = $params['canedit' . $tree->id()] ?? '';

            // Do not allow a path length to be set if the individual ID is not
            if ($gedcom_id === '') {
                $path_length = 0;
            }

            $tree->setUserPreference($edit_user, UserInterface::PREF_TREE_ACCOUNT_XREF, $gedcom_id);
            $tree->setUserPreference($edit_user, UserInterface::PREF_TREE_ROLE, $can_edit);
            $tree->setUserPreference($edit_user, UserInterface::PREF_TREE_PATH_LENGTH, (string) $path_length);
        }

        if ($edit_user->email() !== $email && $this->user_service->findByEmail($email) instanceof User) {
            FlashMessages::addMessage(I18N::translate('Duplicate email address. A user with that email already exists.') . $email, 'danger');

            return redirect(route('admin-users-edit', ['user_id' => $edit_user->id()]));
        }

        if ($edit_user->userName() !== $username && $this->user_service->findByUserName($username) instanceof User) {
            FlashMessages::addMessage(I18N::translate('Duplicate username. A user with that username already exists. Please choose another username.'), 'danger');

            return redirect(route(UserEditPage::class, ['user_id' => $edit_user->id()]));
        }

        $edit_user
            ->setEmail($email)
            ->setUserName($username);

        return redirect(route(UserListPage::class));
    }
}
