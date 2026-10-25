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

use DateTimeZone;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\MessageService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function array_combine;
use function assert;
use function redirect;
use function route;

final class Account
{
    use ViewResponseTrait;

    public function __construct(
        private UserInterface $user,
        private MessageService $message_service,
        private UserService $user_service
    ) {
    }

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $tree = Validator::attributes($request)->treeOptional();
        if ($tree instanceof Tree) {
            $my_individual_record = Registry::individualFactory()->make($tree->getUserPreference($this->user, UserInterface::PREF_TREE_ACCOUNT_XREF), $tree);
            $default_individual   = Registry::individualFactory()->make($tree->getUserPreference($this->user, UserInterface::PREF_TREE_DEFAULT_XREF), $tree);
        } else {
            $my_individual_record = null;
            $default_individual   = null;
        }

        $show_delete_option = $this->user->getPreference(UserInterface::PREF_IS_ADMINISTRATOR) !== '1';
        $timezone_ids       = DateTimeZone::listIdentifiers();
        $timezones          = array_combine($timezone_ids, $timezone_ids);
        $title              = I18N::translate('My account');

        return $this->viewResponse('edit-account-page', [
            'contact_methods'      => $this->message_service->contactMethods(),
            'default_individual'   => $default_individual,
            'languages'            => I18N::allLanguages(),
            'my_individual_record' => $my_individual_record,
            'show_delete_option'   => $show_delete_option,
            'timezones'            => $timezones,
            'title'                => $title,
            'tree'                 => $tree,
            'user'                 => $this->user,
        ]);
    }

    public function post(ServerRequestInterface $request): ResponseInterface
    {
        $tree = Validator::attributes($request)->treeOptional();
        assert($this->user instanceof User);

        $contact_method = Validator::parsedBody($request)->string('contact-method');
        $email          = Validator::parsedBody($request)->string('email');
        $language       = Validator::parsedBody($request)->string('language');
        $real_name      = Validator::parsedBody($request)->string('real_name');
        $password       = Validator::parsedBody($request)->string('password');
        $time_zone      = Validator::parsedBody($request)->string('timezone');
        $username       = Validator::parsedBody($request)->string('user_name');
        $visible_online = Validator::parsedBody($request)->boolean('visible-online', false);

        // Change the password
        if ($password !== '') {
            $this->user->setPassword($password);
        }

        // Changing the email address - make sure it isn't used by another user.
        if ($this->user->email() !== $email) {
            $existing = $this->user_service->findByEmail($email);

            if ($existing instanceof User && $existing->id() !== $this->user->id()) {
                $message = I18N::translate('Duplicate email address. A user with that email already exists.');
                FlashMessages::addMessage($message, 'danger');
            } else {
                $this->user->setEmail($email);
            }
        }

        // Changing the username - make sure it isn't used by another user
        if ($this->user->userName() !== $username) {
            $existing = $this->user_service->findByUserName($username);

            if ($existing instanceof User && $existing->id() !== $this->user->id()) {
                $message = I18N::translate('Duplicate username. A user with that username already exists. Please choose another username.');
                FlashMessages::addMessage($message, 'danger');
            } else {
                $this->user->setUserName($username);
            }
        }

        $this->user->setRealName($real_name);
        $this->user->setPreference(UserInterface::PREF_CONTACT_METHOD, $contact_method);
        $this->user->setPreference(UserInterface::PREF_LANGUAGE, $language);
        $this->user->setPreference(UserInterface::PREF_TIME_ZONE, $time_zone);
        $this->user->setPreference(UserInterface::PREF_IS_VISIBLE_ONLINE, (string) $visible_online);

        if ($tree instanceof Tree) {
            $default_xref = Validator::parsedBody($request)->string('default-xref');
            $tree->setUserPreference($this->user, UserInterface::PREF_TREE_DEFAULT_XREF, $default_xref);
        }

        // Switch to the new language now
        Session::put('language', $language);

        FlashMessages::addMessage(I18N::translate('The details for “%s” have been updated.', e($this->user->userName())), 'success');

        return redirect(route(HomePage::class, ['tree' => $tree]));
    }
}
