<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller to allow the user to edit their account details.
 */
class AccountController extends AbstractBaseController {
	/**
	 * Help for dates.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function edit(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		/** @var User $user */
		$user = $request->attributes->get('user');

		$allow_user_themes    = (bool) Site::getPreference('ALLOW_USER_THEMES');
		$my_individual_record = Individual::getInstance($tree->getUserPreference(Auth::user(), 'gedcomid'), $tree);
		$contact_methods      = FunctionsEdit::optionsContactMethods();
		$default_individual   = Individual::getInstance($tree->getUserPreference(Auth::user(), 'rootid'), $tree);
		$installed_languages  = FunctionsEdit::optionsInstalledLanguages();
		$show_delete_option   = !$user->getPreference('canadmin');
		$themes               = $this->themeNames();
		$timezone_ids         = DateTimeZone::listIdentifiers();
		$timezones            = array_combine($timezone_ids, $timezone_ids);
		$title                = I18N::translate('My account');

		return $this->viewResponse('edit-account-page', [
			'allow_user_themes'    => $allow_user_themes,
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
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function update(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		/** @var User $user */
		$user = $request->attributes->get('user');

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
		if ($user_name !== $user->getUserName()) {
			if (User::findByUserName($user_name) === null) {
				$user->setUserName($user_name);
			} else {
				FlashMessages::addMessage(I18N::translate('Duplicate username. A user with that username already exists. Please choose another username.'));
			}
		}

		// Change the email
		if ($email !== $user->getEmail()) {
			if (User::findByEmail($email) === null) {
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

		$tree->setUserPreference($user,'rootid', $rootid);

		// Switch to the new theme now
		Session::put('theme_id', $theme);

		// Switch to the new language now
		Session::put('locale', $language);

		return new RedirectResponse(route('my-account', ['ged' => $tree->getName()]));
	}

	/**
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function delete(Request $request): RedirectResponse {
		/** @var User $user */
		$user = $request->attributes->get('user');

		// An administrator can only be deleted by another administrator
		if (!$user->getPreference('canadmin')) {
			$currentUser = Auth::user();
			Auth::logout();
			$currentUser->delete();
		}

		return new RedirectResponse(route('my-account'));
	}

	/**
	 * @return array
	 */
	private function themeNames(): array {
		$default_option = [
			'' => /* I18N: default option in list of themes */
				I18N::translate('<default theme>'),
		];

		return $default_option + Theme::themeNames();
	}
}
