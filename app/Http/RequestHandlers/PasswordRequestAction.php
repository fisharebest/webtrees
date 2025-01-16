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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Services\EmailService;
use Fisharebest\Webtrees\Services\RateLimitService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\SiteUser;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Fisharebest\Webtrees\Validator;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function e;
use function random_int;
use function redirect;
use function route;
use function view;

/**
 * Request a new password.
 */
class PasswordRequestAction implements RequestHandlerInterface, StatusCodeInterface
{
    private const TOKEN_LENGTH = 40;

    private const TOKEN_VALIDITY_SECONDS = 3600;

    private const RATE_LIMIT_REQUESTS = 5;

    private const RATE_LIMIT_SECONDS = 300;

    private EmailService $email_service;

    private RateLimitService $rate_limit_service;

    private UserService $user_service;

    /**
     * @param EmailService     $email_service
     * @param RateLimitService $rate_limit_service
     * @param UserService      $user_service
     */
    public function __construct(
        EmailService $email_service,
        RateLimitService $rate_limit_service,
        UserService $user_service
    ) {
        $this->email_service      = $email_service;
        $this->rate_limit_service = $rate_limit_service;
        $this->user_service       = $user_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree  = Validator::attributes($request)->treeOptional();
        $email = Validator::parsedBody($request)->string('email');
        $user  = $this->user_service->findByEmail($email);

        if ($user instanceof User) {
            $this->rate_limit_service->limitRateForUser($user, self::RATE_LIMIT_REQUESTS, self::RATE_LIMIT_SECONDS, 'rate-limit-pw-reset');

            $token  = Str::random(self::TOKEN_LENGTH);
            $expire = (string) (time() + self::TOKEN_VALIDITY_SECONDS);
            $url    = route(PasswordResetPage::class, [
                'token' => $token,
                'tree'  => $tree instanceof Tree ? $tree->name() : null,
            ]);

            $user->setPreference('password-token', $token);
            $user->setPreference('password-token-expire', $expire);

            $this->email_service->send(
                new SiteUser(),
                $user,
                new SiteUser(),
                I18N::translate('Request a new password'),
                view('emails/password-request-text', ['url' => $url, 'user' => $user]),
                view('emails/password-request-html', ['url' => $url, 'user' => $user])
            );

            Log::addAuthenticationLog('Password request for user: ' . $user->userName());
        } else {
            // Email takes a few seconds to send.  An instant response would allow
            // an attacker to use the speed of the response to infer whether an account exists.
            usleep(random_int(500000, 2000000));
        }

        // For security, send a success message even when we fail.
        $message1 = I18N::translate('A password reset link has been sent to “%s”.', e($email));
        $message2 = I18N::translate('This link is valid for one hour.');
        FlashMessages::addMessage($message1 . '<br>' . $message2, 'success');

        return redirect(route(LoginPage::class, ['tree' => $tree instanceof Tree ? $tree->name() : null]));
    }
}
