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

namespace Fisharebest\Webtrees\Http\Controllers\Auth;

use Exception;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\DebugBar;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\Http\Controllers\AbstractBaseController;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for user login and logout.
 */
class LoginController extends AbstractBaseController {
	/**
	 * Show a login page.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function loginPage(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		// Already logged in?
		if (Auth::check()) {
			$ged = $tree !== null ? $tree->getName() : '';

			return new RedirectResponse(route('user-page', ['ged' => $ged]));
		}

		$error    = $request->get('error', '');
		$url      = $request->get('url', '');
		$username = $request->get('username', '');

		$title = I18N::translate('Sign in');

		switch (Site::getPreference('WELCOME_TEXT_AUTH_MODE')) {
			case 1:
			default:
				$welcome = I18N::translate('Anyone with a user account can access this website.');
				break;
			case 2:
				$welcome = I18N::translate('You need to be an authorized user to access this website.');
				break;
			case 3:
				$welcome = I18N::translate('You need to be a family member to access this website.');
				break;
			case 4:
				$welcome = Site::getPreference('WELCOME_TEXT_AUTH_MODE_' . WT_LOCALE);
				break;
		}

		if (Site::getPreference('USE_REGISTRATION_MODULE') === '1') {
			$welcome .= ' ' . I18N::translate('You can apply for an account using the link below.');
		}

		$can_register = Site::getPreference('USE_REGISTRATION_MODULE') === '1';

		return $this->viewResponse('login-page', [
			'can_register' => $can_register,
			'error'        => $error,
			'title'        => $title,
			'url'          => $url,
			'username'     => $username,
			'welcome'      => $welcome,
		]);
	}

	/**
	 * Perform a login.
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function loginAction(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$username = $request->get('username', '');
		$password = $request->get('password', '');
		$url      = $request->get('url', '');

		try {
			$this->doLogin($username, $password);

			if (Auth::isAdmin()) {
				$this->doCheckForUpgrade();
			}

			// If there was no referring page, redirect to "my page".
			if ($url === '') {
				// Switch to a tree where we have a genealogy record (or keep to the current/default).
				$ged = Database::prepare("SELECT gedcom_name FROM `##gedcom` JOIN `##user_gedcom_setting` USING (gedcom_id)" . " WHERE setting_name = 'gedcomid' AND user_id = :user_id" . " ORDER BY gedcom_id = :tree_id DESC")->execute([
					'user_id' => Auth::user()->getUserId(),
					'tree_id' => $tree ? $tree->getTreeId() : 0,
				])->fetchOne();

				$url = route('home-page', ['ged' => $ged]);
			}

			// Redirect to the target URL
			return new RedirectResponse($url);
		} catch (Exception $ex) {
			// Failed to log in.
			DebugBar::addThrowable($ex);

			return new RedirectResponse(route('login', [
				'username' => $username,
				'url'      => $url,
				'error'    => $ex->getMessage(),
			]));
		}
	}

	/**
	 * Log in, if we can.  Throw an exception, if we can't.
	 *
	 * @param string $username
	 * @param string $password
	 *
	 * @throws Exception
	 */
	private function doLogin(string $username, string $password) {
		if (!$_COOKIE) {
			Log::addAuthenticationLog('Login failed (no session cookies): ' . $username);
			throw new Exception(I18N::translate('You cannot sign in because your browser does not accept cookies.'));
		}

		$user = User::findByIdentifier($username);

		if (!$user) {
			Log::addAuthenticationLog('Login failed (no such user/email): ' . $username);
			throw new Exception(I18N::translate('The username or password is incorrect.'));
		}

		if (!$user->checkPassword($password)) {
			Log::addAuthenticationLog('Login failed (incorrect password): ' . $username);
			throw new Exception(I18N::translate('The username or password is incorrect.'));
		}

		if (!$user->getPreference('verified')) {
			Log::addAuthenticationLog('Login failed (not verified by user): ' . $username);
			throw new Exception(I18N::translate('This account has not been verified. Please check your email for a verification message.'));
		}

		if (!$user->getPreference('verified_by_admin')) {
			Log::addAuthenticationLog('Login failed (not approved by admin): ' . $username);
			throw new Exception(I18N::translate('This account has not been approved. Please wait for an administrator to approve it.'));
		}

		Auth::login($user);
		Log::addAuthenticationLog('Login: ' . Auth::user()->getUserName() . '/' . Auth::user()->getRealName());
		Auth::user()->setPreference('sessiontime', WT_TIMESTAMP);

		Session::put('locale', Auth::user()->getPreference('language'));
		Session::put('theme_id', Auth::user()->getPreference('theme'));
		I18N::init(Auth::user()->getPreference('language'));
	}

	/**
	 * Tell the user if a new version of webtrees exists.
	 */
	private function doCheckForUpgrade() {
		$latest_version_txt = Functions::fetchLatestVersion();

		if (preg_match('/^[0-9.]+\|[0-9.]+\|/', $latest_version_txt)) {
			list($latest_version) = explode('|', $latest_version_txt);

			if (version_compare(WT_VERSION, $latest_version) < 0) {
				FlashMessages::addMessage(I18N::translate('A new version of webtrees is available.') . ' <a class="alert-link" href="' . e('admin_site_upgrade.php') . '">' . I18N::translate('Upgrade to webtrees %s.', '<span dir="ltr">' . $latest_version . '</span>') . '</a>');
			}
		}
	}

	/**
	 * Perform a logout.
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function logoutAction(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		if (Auth::id()) {
			Log::addAuthenticationLog('Logout: ' . Auth::user()->getUserName() . '/' . Auth::user()->getRealName());
			Auth::logout();
			FlashMessages::addMessage(I18N::translate('You have signed out.'), 'info');
		}

		if ($tree === null) {
			return new RedirectResponse(route('tree-page'));
		} else {
			return new RedirectResponse(route('tree-page', ['ged' => $tree->getName()]));
		}
	}
}
