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

namespace Fisharebest\Webtrees\Http\Controllers\Auth;

use Exception;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Http\Controllers\AbstractBaseController;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Services\UpgradeService;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Illuminate\Database\Capsule\Manager as DB;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for user login and logout.
 */
class LoginController extends AbstractBaseController
{
    /**
     * Show a login page.
     *
     * @param Request   $request
     * @param Tree|null $tree
     *
     * @return Response
     */
    public function loginPage(Request $request, Tree $tree = null): Response
    {
        // Already logged in?
        if (Auth::check()) {
            $ged = $tree !== null ? $tree->name() : '';

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
     * @param Request        $request
     * @param UpgradeService $upgrade_service
     *
     * @return RedirectResponse
     */
    public function loginAction(Request $request, UpgradeService $upgrade_service): RedirectResponse
    {
        $username = $request->get('username', '');
        $password = $request->get('password', '');
        $url      = $request->get('url', '');

        try {
            $this->doLogin($username, $password);

            if (Auth::isAdmin() && $upgrade_service->isUpgradeAvailable()) {
                FlashMessages::addMessage(I18N::translate('A new version of webtrees is available.') . ' <a class="alert-link" href="' . e(route('upgrade')) . '">' . I18N::translate('Upgrade to webtrees %s.', '<span dir="ltr">' . $upgrade_service->latestVersion() . '</span>') . '</a>');
            }

            // If there was no referring page, redirect to "my page".
            if ($url === '') {
                // Switch to a tree where we have a genealogy record (or keep to the current/default).
                $ged = (string) DB::table('gedcom')
                    ->join('user_gedcom_setting', 'gedcom.gedcom_id', '=', 'user_gedcom_setting.gedcom_id')
                    ->where('user_id', '=', Auth::id())
                    ->value('gedcom_name');

                $url = route('tree-page', ['ged' => $ged]);
            }

            // Redirect to the target URL
            return new RedirectResponse($url);
        } catch (Exception $ex) {
            // Failed to log in.
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
     * @return void
     * @throws Exception
     */
    private function doLogin(string $username, string $password)
    {
        if (!$_COOKIE) {
            Log::addAuthenticationLog('Login failed (no session cookies): ' . $username);
            throw new Exception(I18N::translate('You cannot sign in because your browser does not accept cookies.'));
        }

        $user = User::findByIdentifier($username);

        if ($user === null) {
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
        Auth::user()->setPreference('sessiontime', (string) WT_TIMESTAMP);

        Session::put('locale', Auth::user()->getPreference('language'));
        Session::put('theme_id', Auth::user()->getPreference('theme'));
        I18N::init(Auth::user()->getPreference('language'));
    }

    /**
     * Perform a logout.
     *
     * @param Tree|null $tree
     *
     * @return RedirectResponse
     */
    public function logoutAction(Tree $tree = null): RedirectResponse
    {
        if (Auth::check()) {
            Log::addAuthenticationLog('Logout: ' . Auth::user()->getUserName() . '/' . Auth::user()->getRealName());
            Auth::logout();
            FlashMessages::addMessage(I18N::translate('You have signed out.'), 'info');
        }

        if ($tree === null) {
            return new RedirectResponse(route('tree-page'));
        }

        return new RedirectResponse(route('tree-page', ['ged' => $tree->name()]));
    }
}
