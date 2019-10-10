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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Exception;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Carbon;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Http\Controllers\AbstractBaseController;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Services\UpgradeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Session;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Perform a login.
 */
class LoginAction extends AbstractBaseController
{
    /** @var UpgradeService */
    private $upgrade_service;

    /** @var UserService */
    private $user_service;

    /**
     * LoginController constructor.
     *
     * @param UpgradeService $upgrade_service
     * @param UserService    $user_service
     */
    public function __construct(UpgradeService $upgrade_service, UserService $user_service)
    {
        $this->upgrade_service = $upgrade_service;
        $this->user_service    = $user_service;
    }

    /**
     * Perform a login.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $username = $request->getParsedBody()['username'] ?? '';
        $password = $request->getParsedBody()['password'] ?? '';
        $url      = $request->getParsedBody()['url'] ?? '';

        try {
            $this->doLogin($username, $password);

            if (Auth::isAdmin() && $this->upgrade_service->isUpgradeAvailable()) {
                FlashMessages::addMessage(I18N::translate('A new version of webtrees is available.') . ' <a class="alert-link" href="' . e(route('upgrade')) . '">' . I18N::translate('Upgrade to webtrees %s.', '<span dir="ltr">' . $this->upgrade_service->latestVersion() . '</span>') . '</a>');
            }

            // If there was no referring page, redirect to "my page".
            if ($url === '') {
                // Switch to a tree where we have a genealogy record (or keep to the current/default).
                $tree = (string) DB::table('gedcom')
                    ->join('user_gedcom_setting', 'gedcom.gedcom_id', '=', 'user_gedcom_setting.gedcom_id')
                    ->where('user_id', '=', Auth::id())
                    ->value('gedcom_name');

                $url = route('tree-page', ['tree' => $tree]);
            }

            // Redirect to the target URL
            return redirect($url);
        } catch (Exception $ex) {
            // Failed to log in.
            return redirect(route(LoginPage::class, [
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
    private function doLogin(string $username, string $password): void
    {
        if (!$_COOKIE) {
            Log::addAuthenticationLog('Login failed (no session cookies): ' . $username);
            throw new Exception(I18N::translate('You cannot sign in because your browser does not accept cookies.'));
        }

        $user = $this->user_service->findByIdentifier($username);

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
        Log::addAuthenticationLog('Login: ' . Auth::user()->userName() . '/' . Auth::user()->realName());
        Auth::user()->setPreference('sessiontime', (string) Carbon::now()->unix());

        Session::put('language', Auth::user()->getPreference('language'));
        Session::put('theme', Auth::user()->getPreference('theme'));
        I18N::init(Auth::user()->getPreference('language'));
    }
}
