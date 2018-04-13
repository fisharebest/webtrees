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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * User actions
 */
class UserController extends AbstractBaseController {
	/**
	 * Delete a user.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function delete(Request $request): Response {
		$user_id = (int) $request->get('user_id');

		$user = User::find($user_id);

		if ($user && Auth::isAdmin() && Auth::user() !== $user) {
			Log::addAuthenticationLog('Deleted user: ' . $user->getUserName());
			$user->delete();
		}

		return new Response;
	}

	/**
	 * Select a language.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function language(Request $request): Response {
		$language = $request->get('language');

		I18N::init($language);
		Session::put('locale', $language);
		Auth::user()->setPreference('language', $language);

		return new Response;
	}

	/**
	 * Masquerade as another user.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function masquerade(Request $request): Response {
		$user_id = (int) $request->get('user_id');

		$user = User::find($user_id);

		if ($user !== null && Auth::isAdmin() && Auth::user() !== $user) {
			Log::addAuthenticationLog('Masquerade as user: ' . $user->getUserName());
			Auth::login($user);
			Session::put('masquerade', '1');
		}

		return new Response;
	}

	/**
	 * Select a theme.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function theme(Request $request): Response {
		$theme = $request->get('theme');

		if (Site::getPreference('ALLOW_USER_THEMES') === '1' && array_key_exists($theme, Theme::themeNames())) {
			Session::put('theme_id', $theme);
			Auth::user()->setPreference('theme', $theme);
		}

		return new Response;
	}
}
