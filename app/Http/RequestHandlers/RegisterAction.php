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
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\NoReplyUser;
use Fisharebest\Webtrees\Services\CaptchaService;
use Fisharebest\Webtrees\Services\EmailService;
use Fisharebest\Webtrees\Services\MessageService;
use Fisharebest\Webtrees\Services\RateLimitService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\SiteUser;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\TreeUser;
use Fisharebest\Webtrees\Validator;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function view;

/**
 * Process a user registration.
 */
class RegisterAction implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private CaptchaService $captcha_service;

    private EmailService $email_service;

    private RateLimitService $rate_limit_service;

    private UserService $user_service;

    /**
     * @param CaptchaService   $captcha_service
     * @param EmailService     $email_service
     * @param RateLimitService $rate_limit_service
     * @param UserService      $user_service
     */
    public function __construct(
        CaptchaService $captcha_service,
        EmailService $email_service,
        RateLimitService $rate_limit_service,
        UserService $user_service
    ) {
        $this->captcha_service    = $captcha_service;
        $this->email_service      = $email_service;
        $this->rate_limit_service = $rate_limit_service;
        $this->user_service       = $user_service;
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
        $comments = Validator::parsedBody($request)->string('comments');
        $email    = Validator::parsedBody($request)->string('email');
        $password = Validator::parsedBody($request)->string('password');
        $realname = Validator::parsedBody($request)->string('realname');
        $username = Validator::parsedBody($request)->string('username');

        try {
            if ($this->captcha_service->isRobot($request)) {
                throw new Exception(I18N::translate('Please try again.'));
            }

            $this->doValidateRegistration($request, $username, $email, $realname, $comments, $password);

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

        $user  = $this->user_service->create($username, $realname, $email, $password);
        $token = Str::random(32);

        $user->setPreference(UserInterface::PREF_LANGUAGE, I18N::languageTag());
        $user->setPreference(UserInterface::PREF_TIME_ZONE, Site::getPreference('TIMEZONE'));
        $user->setPreference(UserInterface::PREF_IS_EMAIL_VERIFIED, '');
        $user->setPreference(UserInterface::PREF_IS_ACCOUNT_APPROVED, '');
        $user->setPreference(UserInterface::PREF_TIMESTAMP_REGISTERED, date('U'));
        $user->setPreference(UserInterface::PREF_VERIFICATION_TOKEN, $token);
        $user->setPreference(UserInterface::PREF_CONTACT_METHOD, MessageService::CONTACT_METHOD_INTERNAL_AND_EMAIL);
        $user->setPreference(UserInterface::PREF_NEW_ACCOUNT_COMMENT, $comments);
        $user->setPreference(UserInterface::PREF_IS_VISIBLE_ONLINE, '1');
        $user->setPreference(UserInterface::PREF_AUTO_ACCEPT_EDITS, '');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '');
        $user->setPreference(UserInterface::PREF_TIMESTAMP_ACTIVE, '0');

        $base_url = Validator::attributes($request)->string('base_url');
        $reply_to = $tree instanceof Tree ? new TreeUser($tree) : new SiteUser();

        $verify_url = route(VerifyEmail::class, [
            'username' => $user->userName(),
            'token'    => $token,
            'tree'     => $tree?->name(),
        ]);

        // Send a verification message to the user.
        /* I18N: %s is a server name/URL */
        $this->email_service->send(
            new SiteUser(),
            $user,
            $reply_to,
            I18N::translate('Your registration at %s', $base_url),
            view('emails/register-user-text', ['user' => $user, 'base_url' => $base_url, 'verify_url' => $verify_url]),
            view('emails/register-user-html', ['user' => $user, 'base_url' => $base_url, 'verify_url' => $verify_url])
        );

        // Tell the administrators about the registration.
        foreach ($this->user_service->administrators() as $administrator) {
            I18N::init($administrator->getPreference(UserInterface::PREF_LANGUAGE, 'en-US'));

            /* I18N: %s is a server name/URL */
            $subject = I18N::translate('New registration at %s', $base_url);

            $body_text = view('emails/register-notify-text', [
                'user'     => $user,
                'comments' => $comments,
                'base_url' => $base_url,
                'tree'     => $tree,
            ]);

            $body_html = view('emails/register-notify-html', [
                'user'     => $user,
                'comments' => $comments,
                'base_url' => $base_url,
                'tree'     => $tree,
            ]);

            /* I18N: %s is a server name/URL */
            $this->email_service->send(
                new SiteUser(),
                $administrator,
                new NoReplyUser(),
                $subject,
                $body_text,
                $body_html
            );

            $mail1_method = $administrator->getPreference(UserInterface::PREF_CONTACT_METHOD);
            if (
                $mail1_method !== MessageService::CONTACT_METHOD_EMAIL &&
                $mail1_method !== MessageService::CONTACT_METHOD_MAILTO &&
                $mail1_method !== MessageService::CONTACT_METHOD_NONE
            ) {
                DB::table('message')->insert([
                    'sender'     => $user->email(),
                    'ip_address' => $request->getAttribute('client-ip'),
                    'user_id'    => $administrator->id(),
                    'subject'    => $subject,
                    'body'       => $body_text,
                ]);
            }
        }

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
        if (Site::getPreference('USE_REGISTRATION_MODULE') !== '1') {
            throw new HttpNotFoundException();
        }
    }

    /**
     * Check the registration details.
     *
     * @param ServerRequestInterface $request
     * @param string                 $username
     * @param string                 $email
     * @param string                 $realname
     * @param string                 $comments
     * @param string                 $password
     *
     * @return void
     * @throws Exception
     */
    private function doValidateRegistration(
        ServerRequestInterface $request,
        string $username,
        string $email,
        string $realname,
        string $comments,
        #[\SensitiveParameter] string $password
    ): void {
        // All fields are required
        if ($username === '' || $email === '' || $realname === '' || $comments === '' || $password === '') {
            throw new Exception(I18N::translate('All fields must be completed.'));
        }

        // Username already exists
        if ($this->user_service->findByUserName($username) !== null) {
            throw new Exception(I18N::translate('Duplicate username. A user with that username already exists. Please choose another username.'));
        }

        // Email already exists
        if ($this->user_service->findByEmail($email) !== null) {
            throw new Exception(I18N::translate('Duplicate email address. A user with that email already exists.'));
        }

        $base_url = Validator::attributes($request)->string('base_url');

        // No external links
        if (preg_match('/(?!' . preg_quote($base_url, '/') . ')(((?:http|https):\/\/)[a-zA-Z0-9.-]+)/', $comments, $match)) {
            throw new Exception(I18N::translate('You are not allowed to send messages that contain external links.') . ' ' . I18N::translate('You should delete the “%1$s” from “%2$s” and try again.', e($match[2]), e($match[1])));
        }
    }
}
