<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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
use Fisharebest\Webtrees\Carbon;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Services\EmailService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\SiteUser;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function e;
use function redirect;
use function route;
use function view;

/**
 * Request a new password.
 */
class PasswordRequestAction implements RequestHandlerInterface, StatusCodeInterface
{
    private const TOKEN_LENGTH = 40;

    /** @var EmailService */
    private $email_service;

    /** @var UserService */
    private $user_service;

    /**
     * PasswordRequestForm constructor.
     *
     * @param EmailService $email_service
     * @param UserService  $user_service
     */
    public function __construct(EmailService $email_service, UserService $user_service)
    {
        $this->user_service  = $user_service;
        $this->email_service = $email_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');

        $params = (array) $request->getParsedBody();

        $email = $params['email'] ?? '';
        $user  = $this->user_service->findByEmail($email);

        if ($user instanceof User) {
            $token  = Str::random(self::TOKEN_LENGTH);
            $expire = (string) Carbon::now()->addHour()->getTimestamp();
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

            $message1 = I18N::translate('A password reset link has been sent to “%s”.', e($email));
            $message2 = I18N::translate('This link is valid for one hour.');
            FlashMessages::addMessage($message1 . '<br>' . $message2, 'success');
        } else {
            $message = I18N::translate('There is no user account with the email “%s”.', e($email));
            FlashMessages::addMessage($message, 'danger');
        }

        return redirect(route(LoginPage::class, ['tree' => $tree instanceof Tree ? $tree->name() : null]));
    }
}
