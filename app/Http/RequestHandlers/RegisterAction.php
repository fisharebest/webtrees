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
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Services\CaptchaService;
use Fisharebest\Webtrees\Services\RateLimitService;
use Fisharebest\Webtrees\Services\RegistrationService;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Process a user registration.
 */
class RegisterAction implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private CaptchaService $captcha_service;

    private RateLimitService $rate_limit_service;

    private RegistrationService $registration_service;

    /**
     * @param CaptchaService       $captcha_service
     * @param RateLimitService     $rate_limit_service
     * @param RegistrationService  $registration_service
     */
    public function __construct(
        CaptchaService $captcha_service,
        RateLimitService $rate_limit_service,
        RegistrationService $registration_service
    ) {
        $this->captcha_service      = $captcha_service;
        $this->rate_limit_service   = $rate_limit_service;
        $this->registration_service = $registration_service;
    }

    /**
     * Perform a registration.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->checkRegistrationAllowed();

        $tree     = Validator::attributes($request)->treeOptional();
        $base_url = Validator::attributes($request)->string('base_url');
        $comments = Validator::parsedBody($request)->string('comments');
        $email    = Validator::parsedBody($request)->string('email');
        $password = Validator::parsedBody($request)->string('password');
        $realname = Validator::parsedBody($request)->string('realname');
        $username = Validator::parsedBody($request)->string('username');

        try {
            if ($this->captcha_service->isRobot($request)) {
                throw new Exception(I18N::translate('Please try again.'));
            }

            $error = $this->registration_service->checkRegistrationDetails($username, $email, $realname, $comments, $password, $base_url);

            if ($error !== null) {
                throw new Exception($error);
            }

            Session::forget('register_comments');
            Session::forget('register_email');
            Session::forget('register_realname');
            Session::forget('register_username');
        } catch (Exception $ex) {
            FlashMessages::addMessage($ex->getMessage(), 'danger');

            Session::put('register_comments', $comments);
            Session::put('register_email', $email);
            Session::put('register_realname', $realname);
            Session::put('register_username', $username);

            return redirect(route(RegisterPage::class));
        }

        $this->rate_limit_service->limitRateForSite(5, 300, 'rate-limit-registration');

        Log::addAuthenticationLog('User registration requested for: ' . $username);

        $user = $this->registration_service->register(
            $username,
            $email,
            $realname,
            $password,
            $comments,
            $base_url,
            $tree,
            Validator::attributes($request)->string('client-ip')
        );

        $title = I18N::translate('Request a new user account');

        return $this->viewResponse('register-success-page', [
            'title' => $title,
            'tree'  => $tree,
            'user'  => $user,
        ]);
    }

    /**
     * Check that visitors are allowed to register on this site.
     *
     * @return void
     * @throws HttpNotFoundException
     */
    private function checkRegistrationAllowed(): void
    {
        if (!$this->registration_service->registrationAllowed()) {
            throw new HttpNotFoundException();
        }
    }
}
