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
use Fisharebest\Webtrees\Enums\Role;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Services\EmailService;
use Fisharebest\Webtrees\Services\MessageService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\SiteUser;
use Fisharebest\Webtrees\User;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function array_column;
use function array_map;
use function redirect;
use function route;

final class UserEdit
{
    use ViewResponseTrait;

    public function __construct(
        private UserInterface $user,
        private MessageService $message_service,
        private ModuleService $module_service,
        private TreeService $tree_service,
        private UserService $user_service,
        private EmailService $email_service,
    ) {
    }

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        $user_id = Validator::queryParams($request)->integer('user_id');
        $user    = $this->user_service->find($user_id);

        if ($user === null) {
            throw new HttpNotFoundException(I18N::translate('%s does not exist.', 'user_id:' . $user_id));
        }

        $roles = array_column(Role::cases(), null, 'value');
        $roles = array_map(static fn (Role $role): string => $role->label(), $roles);

        $theme_options = $this->module_service
            ->findByInterface(ModuleThemeInterface::class)
            ->map($this->module_service->titleMapper())
            ->prepend(I18N::translate('<default theme>'), '');

        return $this->viewResponse('admin/users-edit', [
            'contact_methods' => $this->message_service->contactMethods(),
            'languages'       => I18N::allLanguages(),
            'roles'           => $roles,
            'trees'           => $this->tree_service->all(),
            'theme_options'   => $theme_options,
            'title'           => I18N::translate('Edit the user'),
            'user'            => $user,
        ]);
    }

    public function post(ServerRequestInterface $request): ResponseInterface
    {
        $user_id        = Validator::parsedBody($request)->integer('user_id');
        $username       = Validator::parsedBody($request)->string('username');
        $real_name      = Validator::parsedBody($request)->string('real_name');
        $email          = Validator::parsedBody($request)->string('email');
        $password       = Validator::parsedBody($request)->string('password');
        $theme          = Validator::parsedBody($request)->string('theme');
        $language       = Validator::parsedBody($request)->string('language');
        $timezone       = Validator::parsedBody($request)->string('timezone');
        $contact_method = Validator::parsedBody($request)->string('contact-method');
        $comment        = Validator::parsedBody($request)->string('comment');
        $auto_accept    = Validator::parsedBody($request)->boolean('auto_accept', false);
        $canadmin       = Validator::parsedBody($request)->boolean('canadmin', false);
        $visible_online = Validator::parsedBody($request)->boolean('visible-online', false);
        $verified       = Validator::parsedBody($request)->boolean('verified', false);
        $approved       = Validator::parsedBody($request)->boolean('approved', false);

        $edit_user = $this->user_service->find($user_id);

        if ($edit_user === null) {
            throw new HttpNotFoundException(I18N::translate('%s does not exist.', 'user_id:' . $user_id));
        }

        // We have just approved a user.  Tell them
        if ($approved && $edit_user->getPreference(UserInterface::PREF_IS_ACCOUNT_APPROVED) !== '1') {
            I18N::init($edit_user->getPreference(UserInterface::PREF_LANGUAGE, 'en-US'));

            $base_url = Validator::attributes($request)->string('base_url');

            $this->email_service->send(
                new SiteUser(),
                $edit_user,
                $this->user,
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
        if ($edit_user->id() !== $this->user->id()) {
            $edit_user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, $canadmin ? '1' : '');
        }

        foreach ($this->tree_service->all() as $tree) {
            $path_length = Validator::parsedBody($request)->integer('RELATIONSHIP_PATH_LENGTH' . $tree->id(), 0);
            $gedcom_id   = Validator::parsedBody($request)->string('gedcomid' . $tree->id(), '');
            $can_edit    = Validator::parsedBody($request)->string('canedit' . $tree->id(), '');

            // Do not allow a path length to be set if the individual ID is not
            if ($gedcom_id === '') {
                $path_length = 0;
            }

            $tree->setUserPreference($edit_user, UserInterface::PREF_TREE_ACCOUNT_XREF, $gedcom_id);
            $tree->setUserPreference($edit_user, UserInterface::PREF_TREE_ROLE, $can_edit);
            $tree->setUserPreference($edit_user, UserInterface::PREF_TREE_PATH_LENGTH, (string) $path_length);
        }

        // Changing the email address - make sure it isn't used by another user.
        if ($edit_user->email() !== $email) {
            $existing = $this->user_service->findByEmail($email);

            if ($existing instanceof User && $existing->id() !== $edit_user->id()) {
                $message = I18N::translate('Duplicate email address. A user with that email already exists.') . ' ' . $existing->email();
                FlashMessages::addMessage($message, 'danger');

                return redirect(route(self::class, ['user_id' => $edit_user->id()]));
            }
        }

        // Changing the username - make sure it isn't used by another user
        if ($edit_user->userName() !== $username) {
            $existing = $this->user_service->findByUserName($username);

            if ($existing instanceof User && $existing->id() !== $edit_user->id()) {
                $message = I18N::translate('Duplicate username. A user with that username already exists. Please choose another username.') . ' ' . $existing->userName();
                FlashMessages::addMessage($message, 'danger');

                return redirect(route(self::class, ['user_id' => $edit_user->id()]));
            }
        }

        $edit_user
            ->setEmail($email)
            ->setUserName($username);

        return redirect(route(UserListPage::class));
    }
}
