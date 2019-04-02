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
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

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
     * @param Tree          $tree
     * @param UserInterface $user
     *
     * @return ResponseInterface
     */
    public function edit(Tree $tree, UserInterface $user): ResponseInterface
    {
        $my_individual_record = Individual::getInstance($tree->getUserPreference(Auth::user(), 'gedcomid'), $tree);
        $contact_methods      = FunctionsEdit::optionsContactMethods();
        $default_individual   = Individual::getInstance($tree->getUserPreference(Auth::user(), 'rootid'), $tree);
        $installed_languages  = FunctionsEdit::optionsInstalledLanguages();
        $show_delete_option   = !$user->getPreference('canadmin');
        $themes               = $this->themeOptions();
        $timezone_ids         = DateTimeZone::listIdentifiers();
        $timezones            = array_combine($timezone_ids, $timezone_ids);
        $title                = I18N::translate('My account');

        return $this->viewResponse('edit-account-page', [
            'contact_methods'      => $contact_methods,
            'default_individual'   => $default_individual,
            'installed_languages'  => $installed_languages,
            'my_individual_record' => $my_individual_record,
            'show_delete_option'   => $show_delete_option,
            'themes'               => $themes,
            'timezones'            => $timezones,
            'title'                => $title,
            'user'                 => $user,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     * @param UserInterface          $user
     *
     * @return ResponseInterface
     */
    public function update(ServerRequestInterface $request, Tree $tree, UserInterface $user): ResponseInterface
    {
        $contact_method = (string) $request->get('contact_method');
        $email          = (string) $request->get('email');
        $language       = (string) $request->get('language');
        $real_name      = (string) $request->get('real_name');
        $password       = (string) $request->get('password');
        $rootid         = (string) $request->get('root_id');
        $theme          = (string) $request->get('theme');
        $time_zone      = (string) $request->get('timezone');
        $user_name      = (string) $request->get('user_name');
        $visible_online = (string) $request->get('visible_online');

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
            ->setPreference('theme', $theme)
            ->setPreference('TIMEZONE', $time_zone)
            ->setPreference('visibleonline', $visible_online);

        $tree->setUserPreference($user, 'rootid', $rootid);

        // Switch to the new theme now
        Session::put('theme_id', $theme);

        // Switch to the new language now
        Session::put('locale', $language);

        return redirect(route('my-account', ['ged' => $tree->name()]));
    }

    /**
     * @param UserInterface $user
     *
     * @return ResponseInterface
     */
    public function delete(UserInterface $user): ResponseInterface
    {
        // An administrator can only be deleted by another administrator
        if (!$user->getPreference('canadmin') && $user instanceof User) {
            $this->user_service->delete($user);
            Auth::logout();
        }

        return redirect(route('my-account'));
    }

    /**
     * @return Collection
     * @return string[]
     */
    private function themeOptions(): Collection
    {
        return $this->module_service
            ->findByInterface(ModuleThemeInterface::class)
            ->map($this->module_service->titleMapper())
            ->prepend(I18N::translate('<default theme>'), '');
    }
}
