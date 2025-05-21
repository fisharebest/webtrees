<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

use Exception;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Services\UpgradeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Fisharebest\Webtrees\Site;

use function route;
use function time;

/**
 * Perform a login.
 */
class LoginAction implements RequestHandlerInterface
{
    private UpgradeService $upgrade_service;

    private UserService $user_service;

    /**
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
        $tree        = Validator::attributes($request)->treeOptional();
        $default_url = route(HomePage::class);
        $username    = Validator::parsedBody($request)->string('username');
        $password    = Validator::parsedBody($request)->string('password', '');
        $loginstage    = Validator::parsedBody($request)->string('loginstage', '1');
        $code2fa     = Validator::parsedBody($request)->string('code2fa','');       
        $url         = Validator::parsedBody($request)->isLocalUrl()->string('url', $default_url);
        $mfastatus   = Validator::parsedBody($request)->string('mfastatus', '0');
        $mfasuccess   = Validator::parsedBody($request)->string('mfasuccess', '0');

        try {
            if ($loginstage == "1") {
                $mfastatus = $this->doLogin($username, $password);
            } else {
              if ($mfastatus == "1") {  
                  $mfasuccess = $this->doLogin_mfa($username, $code2fa);
              }
            }          

            if (Auth::isAdmin() && $this->upgrade_service->isUpgradeAvailable()) {
                FlashMessages::addMessage(I18N::translate('A new version of webtrees is available.') . ' <a class="alert-link" href="' . e(route(UpgradeWizardPage::class)) . '">' . I18N::translate('Upgrade to webtrees %s.', '<span dir="ltr">' . $this->upgrade_service->latestVersion() . '</span>') . '</a>');
            }

            
            # Show the mfa page
            if ($mfastatus == "1" && $mfasuccess == "0") {
                return redirect(route(LoginPageMfa::class, [
                    'tree'     => $tree?->name(),
                    'username' => $username,                
                    'url'      => $url,
                ]));
            } else {
                $this->completeLogin($username);
                // Redirect to the target URL
                return redirect($url);
            }

        } catch (Exception $ex) {
            // Failed to log in.
            FlashMessages::addMessage($ex->getMessage(), 'danger');  
            if ($loginstage == "2") {          
                $loginclass = LoginPageMfa::class;
            } else {
                $loginclass = LoginPage::class;
            }
             
            return redirect(route($loginclass, [
                'tree'     => $tree?->name(),
                'username' => $username,                
                'url'      => $url,
            ]));
            
        }
    }

    /**
     * Check basic log in details, if we can.  Throw an exception, if we can't.
     *
     * @param string $username
     * @param string $password
     *
     * @return string
     * @throws Exception
     */
    private function doLogin(string $username, #[\SensitiveParameter] string $password): string
    {
        if ($_COOKIE === []) {
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

        if ($user->getPreference(UserInterface::PREF_IS_EMAIL_VERIFIED) !== '1') {
            Log::addAuthenticationLog('Login failed (not verified by user): ' . $username);
            throw new Exception(I18N::translate('This account has not been verified. Please check your email for a verification message.'));
        }

        if ($user->getPreference(UserInterface::PREF_IS_ACCOUNT_APPROVED) !== '1') {
            Log::addAuthenticationLog('Login failed (not approved by admin): ' . $username);
            throw new Exception(I18N::translate('This account has not been approved. Please wait for an administrator to approve it.'));
        }
        if ($user->getPreference(UserInterface::PREF_IS_STATUS_MFA) == "1" && Site::getPreference('SHOW_2FA_OPTION')) {
           # MFA switched on for site and has been enabled by user
           return "1";
        } else {
           return "0";
        }
    }

    /**
     * Verify login with 2FA if user has enable this.  Throw an exception, if we can't.
     *
     * @param string $username
     * @param string $code2fa
     *
     * @return string
     * @throws Exception
     */

    private function doLogin_mfa(string $username, string $code2fa): string
    {        
        if ($code2fa != '') {
            $user = $this->user_service->findByIdentifier($username);
            if (!$user->check2FAcode($code2fa)) {
                throw new Exception(I18N::translate('2FA code does not match. Please try again.'));
            }
        } else {
                throw new Exception(I18N::translate('2FA code must be entered as you have 2FA authentication enabled. Please try again.'));
        }        
        return "1";
    
    }

    /**
     * Complete login 
     *
     * @param string $username
     *
     * @return void
     * @throws Exception
     */    
    private function completeLogin(string $username): void
    {
        $user = $this->user_service->findByIdentifier($username);
        Auth::login($user);
        Log::addAuthenticationLog('Login: ' . Auth::user()->userName() . '/' . Auth::user()->realName());
        Auth::user()->setPreference(UserInterface::PREF_TIMESTAMP_ACTIVE, (string) time());
    
        Session::put('language', Auth::user()->getPreference(UserInterface::PREF_LANGUAGE, 'en-US'));
        Session::put('theme', Auth::user()->getPreference(UserInterface::PREF_THEME));
        I18N::init(Auth::user()->getPreference(UserInterface::PREF_LANGUAGE, 'en-US'));
    }
    
}
