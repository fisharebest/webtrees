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
use Fisharebest\Localization\Locale\LocaleInterface;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Http\Controllers\AbstractBaseController;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\NoReplyUser;
use Fisharebest\Webtrees\Services\EmailService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\SiteUser;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\TreeUser;
use Fisharebest\Webtrees\User;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use function assert;

/**
 * Process a user registration.
 */
class RegisterAction extends AbstractBaseController
{
    /**
     * @var EmailService
     */
    private $email_service;

    /**
     * @var UserService
     */
    private $user_service;

    /**
     * RegisterController constructor.
     *
     * @param EmailService $email_service
     * @param UserService  $user_service
     */
    public function __construct(EmailService $email_service, UserService $user_service)
    {
        $this->email_service = $email_service;
        $this->user_service  = $user_service;
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
        $locale = $request->getAttribute('locale');
        assert($locale instanceof LocaleInterface);

        $this->checkRegistrationAllowed();

        $tree     = $request->getAttribute('tree');
        $comments = $request->getParsedBody()['comments'] ?? '';
        $email    = $request->getParsedBody()['email'] ?? '';
        $password = $request->getParsedBody()['password'] ?? '';
        $realname = $request->getParsedBody()['realname'] ?? '';
        $username = $request->getParsedBody()['username'] ?? '';

        try {
            $this->doValidateRegistration($request, $username, $email, $realname, $comments, $password);
        } catch (Exception $ex) {
            FlashMessages::addMessage($ex->getMessage(), 'danger');

            return redirect(route(RegisterPage::class, [
                'comments' => $comments,
                'email'    => $email,
                'realname' => $realname,
                'username' => $username,
            ]));
        }

        Log::addAuthenticationLog('User registration requested for: ' . $username);

        $user = $this->user_service->create($username, $realname, $email, $password);
        $user->setPreference(User::PREF_LANGUAGE, $locale->languageTag());
        $user->setPreference(User::PREF_IS_EMAIL_VERIFIED, '');
        $user->setPreference(User::PREF_IS_ACCOUNT_APPROVED, '');
        $user->setPreference(User::PREF_TIMESTAMP_REGISTERED, date('U'));
        $user->setPreference(User::PREF_VERIFICATION_TOKEN, md5(Uuid::uuid4()->toString()));
        $user->setPreference(User::PREF_CONTACT_METHOD, 'messaging2');
        $user->setPreference(User::PREF_NEW_ACCOUNT_COMMENT, $comments);
        $user->setPreference(User::PREF_IS_VISIBLE_ONLINE, '1');
        $user->setPreference(User::PREF_AUTO_ACCEPT_EDITS, '');
        $user->setPreference(User::PREF_IS_ADMINISTRATOR, '');
        $user->setPreference(User::PREF_TIMESTAMP_ACTIVE, '0');

        $base_url = $request->getAttribute('base_url');
        $reply_to = $tree instanceof Tree ? new TreeUser($tree) : new SiteUser();

        // Send a verification message to the user.
        /* I18N: %s is a server name/URL */
        $this->email_service->send(
            new Siteuser(),
            $user,
            $reply_to,
            I18N::translate('Your registration at %s', $base_url),
            view('emails/register-user-text', ['user' => $user, 'base_url' => $base_url]),
            view('emails/register-user-html', ['user' => $user, 'base_url' => $base_url])
        );

        // Tell the administrators about the registration.
        foreach ($this->user_service->administrators() as $administrator) {
            I18N::init($administrator->getPreference(User::PREF_LANGUAGE));

            /* I18N: %s is a server name/URL */
            $subject = I18N::translate('New registration at %s', $base_url);

            /* I18N: %s is a server name/URL */
            $this->email_service->send(
                new SiteUser(),
                $administrator,
                new NoReplyUser(),
                $subject,
                view('emails/register-notify-text', ['user' => $user, 'comments' => $comments, 'base_url' => $base_url]),
                view('emails/register-notify-html', ['user' => $user, 'comments' => $comments, 'base_url' => $base_url])
            );

            $mail1_method = $administrator->getPreference(User::PREF_CONTACT_METHOD);
            if ($mail1_method !== 'messaging3' && $mail1_method !== 'mailto' && $mail1_method !== 'none') {
                DB::table('message')->insert([
                    'sender'     => $user->email(),
                    'ip_address' => $request->getAttribute('client-ip'),
                    'user_id'    => $administrator->id(),
                    'subject'    => $subject,
                    'body'       => view('emails/register-notify-text', ['user' => $user, 'comments' => $comments, 'base_url' => $base_url]),
                ]);
            }
        }

        $title = I18N::translate('Request a new user account');

        return $this->viewResponse('register-success-page', [
            'title' => $title,
            'user'  => $user,
        ]);
    }

    /**
     * Check that visitors are allowed to register on this site.
     *
     * @return void
     * @throws NotFoundHttpException
     */
    private function checkRegistrationAllowed(): void
    {
        if (Site::getPreference('USE_REGISTRATION_MODULE') !== '1') {
            throw new NotFoundHttpException();
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
    private function doValidateRegistration(ServerRequestInterface $request, string $username, string $email, string $realname, string $comments, string $password): void
    {
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

        $base_url = $request->getAttribute('base_url');

        // No external links
        if (preg_match('/(?!' . preg_quote($base_url, '/') . ')(((?:http|https):\/\/)[a-zA-Z0-9.-]+)/', $comments, $match)) {
            throw new Exception(I18N::translate('You are not allowed to send messages that contain external links.') . ' ' . I18N::translate('You should delete the “%1$s” from “%2$s” and try again.', e($match[2]), e($match[1])));
        }
    }
}
