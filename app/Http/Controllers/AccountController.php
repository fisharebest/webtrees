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

namespace Fisharebest\Webtrees\Http\Controllers;

use DateTimeZone;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function assert;

/**
 * Controller to allow the user to edit their account details.
 */
class AccountController extends AbstractBaseController
{
    /**
     * @var ModuleService
     */
    private $module_service;
    /**
     * @var UserService
     */
    private $user_service;

    /**
     * AccountController constructor.
     *
     * @param ModuleService $module_service
     * @param UserService   $user_service
     */
    public function __construct(ModuleService $module_service, UserService $user_service)
    {
        $this->module_service = $module_service;
        $this->user_service   = $user_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function edit(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree, new InvalidArgumentException());

        $user = $request->getAttribute('user');

        $my_individual_record = Individual::getInstance($tree->getUserPreference(Auth::user(), 'gedcomid'), $tree);
        $contact_methods      = FunctionsEdit::optionsContactMethods();
        $default_individual   = Individual::getInstance($tree->getUserPreference(Auth::user(), 'rootid'), $tree);
        $installed_languages  = $this->installedLanguages();
        $show_delete_option   = !$user->getPreference('canadmin');
        $timezone_ids         = DateTimeZone::listIdentifiers();
        $timezones            = array_combine($timezone_ids, $timezone_ids);
        $title                = I18N::translate('My account');

        return $this->viewResponse('edit-account-page', [
            'contact_methods'      => $contact_methods,
            'default_individual'   => $default_individual,
            'installed_languages'  => $installed_languages,
            'my_individual_record' => $my_individual_record,
            'show_delete_option'   => $show_delete_option,
            'timezones'            => $timezones,
            'title'                => $title,
            'user'                 => $user,
        ]);
    }

    /**
     * A list of installed languages (e.g. for an edit control).
     *
     * @return string[]
     */
    private function installedLanguages(): array
    {
        $languages = [];
        foreach (I18N::installedLocales() as $locale) {
            $languages[$locale->languageTag()] = $locale->endonym();
        }

        return $languages;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function update(ServerRequestInterface $request): ResponseInterface
    {
        $tree   = $request->getAttribute('tree');
        $user   = $request->getAttribute('user');
        $params = $request->getParsedBody();

        $contact_method = $params['contact_method'];
        $email          = $params['email'];
        $language       = $params['language'];
        $real_name      = $params['real_name'];
        $password       = $params['password'];
        $rootid         = $params['root_id'];
        $time_zone      = $params['timezone'];
        $user_name      = $params['user_name'];
        $visible_online = $params['visible_online'] ?? '';

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

        $user
            ->setRealName($real_name)
            ->setPreference('contactmethod', $contact_method)
            ->setPreference('language', $language)
            ->setPreference('TIMEZONE', $time_zone)
            ->setPreference('visibleonline', $visible_online);

        $tree->setUserPreference($user, 'rootid', $rootid);

        // Switch to the new language now
        Session::put('language', $language);

        return redirect(route('my-account', ['tree' => $tree->name()]));
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        $user = $request->getAttribute('user');

        // An administrator can only be deleted by another administrator
        if ($user instanceof User && !$user->getPreference('canadmin')) {
            $this->user_service->delete($user);
            Auth::logout();
        }

        return redirect(route('my-account'));
    }
}
