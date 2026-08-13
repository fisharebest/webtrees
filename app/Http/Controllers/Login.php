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

use Exception;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UpgradeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Fisharebest\Webtrees\Validator;
use Psr\Clock\ClockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function e;
use function redirect;
use function route;

final class Login
{
    use ViewResponseTrait;

    public function __construct(
        private readonly TreeService $tree_service,
        private readonly UpgradeService $upgrade_service,
        private readonly UserService $user_service,
        private readonly ClockInterface $clock,
    ) {
    }

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $tree = Validator::attributes($request)->treeOptional();
        $user = Validator::attributes($request)->user();

        // Already logged in?
        if ($user instanceof User) {
            return redirect(route(UserPage::class, ['tree' => $tree instanceof Tree ? $tree->name() : '']));
        }

        $url      = Validator::queryParams($request)->isLocalUrl()->string('url', route(HomePage::class));
        $username = Validator::queryParams($request)->string('username', '');

        // No tree?  perhaps we came here from a page without one.
        if ($tree === null) {
            $default = Site::getPreference('DEFAULT_GEDCOM');
            $tree    = $this->tree_service->all()->get($default) ?? $this->tree_service->all()->first();

            if ($tree instanceof Tree) {
                return redirect(route(self::class, ['tree' => $tree->name(), 'url' => $url]));
            }
        }

        $title = I18N::translate('Sign in');

        switch (Site::getPreference('WELCOME_TEXT_AUTH_MODE')) {
            case '1':
            default:
                $welcome = I18N::translate('Anyone with a user account can access this website.');
                break;
            case '2':
                $welcome = I18N::translate('You need to be an authorized user to access this website.');
                break;
            case '3':
                $welcome = I18N::translate('You need to be a family member to access this website.');
                break;
            case '4':
                $welcome = Site::getPreference('WELCOME_TEXT_AUTH_MODE_' . I18N::languageTag());

                if ($welcome === '') {
                    // No value for this language?  Try the default language.
                    $language = Site::getPreference('LANGUAGE');
                    $welcome  = Site::getPreference('WELCOME_TEXT_AUTH_MODE_' . $language);
                }

                break;
        }

        if (Site::getPreference('USE_REGISTRATION_MODULE') === '1') {
            $welcome .= '<br>' . I18N::translate('You can apply for an account using the link below.');
        }

        $can_register = Site::getPreference('USE_REGISTRATION_MODULE') === '1';

        return $this->viewResponse('login-page', [
            'can_register' => $can_register,
            'title'        => $title,
            'url'          => $url,
            'tree'         => $tree,
            'username'     => $username,
            'welcome'      => $welcome,
        ]);
    }

    public function post(ServerRequestInterface $request): ResponseInterface
    {
        $tree        = Validator::attributes($request)->treeOptional();
        $default_url = route(HomePage::class);
        $username    = Validator::parsedBody($request)->string('username');
        $password    = Validator::parsedBody($request)->string('password');
        $url         = Validator::parsedBody($request)->isLocalUrl()->string('url', $default_url);

        try {
            $this->doLogin($username, $password);

            if (Auth::isAdmin() && $this->upgrade_service->isUpgradeAvailable()) {
                FlashMessages::addMessage(I18N::translate('A new version of webtrees is available.') . ' <a class="alert-link" href="' . e(route(UpgradeWizardPage::class)) . '">' . I18N::translate('Upgrade to webtrees %s.', '<span dir="ltr">' . $this->upgrade_service->latestVersion() . '</span>') . '</a>');
            }

            // Redirect to the target URL
            return redirect($url);
        } catch (Exception $ex) {
            // Failed to log in.
            FlashMessages::addMessage($ex->getMessage(), 'danger');

            return redirect(route(self::class, [
                'tree'     => $tree?->name(),
                'username' => $username,
                'url'      => $url,
            ]));
        }
    }

    /**
     * Log in, if we can.  Throw an exception, if we can't.
     */
    private function doLogin(string $username, #[\SensitiveParameter] string $password): void
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

        Auth::login($user);
        Log::addAuthenticationLog('Login: ' . Auth::user()->userName() . '/' . Auth::user()->realName());
        Auth::user()->setPreference(UserInterface::PREF_TIMESTAMP_ACTIVE, (string) $this->clock->now()->getTimestamp());

        Session::put('language', Auth::user()->getPreference(UserInterface::PREF_LANGUAGE, 'en-US'));
        Session::put('theme', Auth::user()->getPreference(UserInterface::PREF_THEME));
        I18N::init(Auth::user()->getPreference(UserInterface::PREF_LANGUAGE, 'en-US'));
    }
}
