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
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Http\Controllers\AbstractBaseController;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Mail;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\TreeUser;
use Illuminate\Database\Capsule\Manager as DB;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller for user registration.
 */
class RegisterController extends AbstractBaseController
{
    /**
     * @var UserService
     */
    private $user_service;

    /**
     * RegisterController constructor.
     *
     * @param UserService $user_service
     */
    public function __construct(UserService $user_service)
    {
        $this->user_service = $user_service;
    }

    /**
     * Show a registration page.
     *
     * @param Request $request
     *
     * @return Response
     * @throws NotFoundHttpException
     */
    public function registerPage(Request $request): Response
    {
        $this->checkRegistrationAllowed();

        $comments = $request->get('comments', '');
        $email    = $request->get('email', '');
        $realname = $request->get('realname', '');
        $username = $request->get('username', '');

        $show_caution = Site::getPreference('SHOW_REGISTER_CAUTION') === '1';

        $title = I18N::translate('Request a new user account');

        return $this->viewResponse('register-page', [
            'comments'     => $comments,
            'email'        => $email,
            'realname'     => $realname,
            'show_caution' => $show_caution,
            'title'        => $title,
            'username'     => $username,
        ]);
    }

    /**
     * Perform a registration.
     *
     * @param Request $request
     * @param Tree    $tree
     *
     * @return Response
     */
    public function registerAction(Request $request, Tree $tree): Response
    {
        $this->checkRegistrationAllowed();

        $comments = $request->get('comments', '');
        $email    = $request->get('email', '');
        $password = $request->get('password', '');
        $realname = $request->get('realname', '');
        $username = $request->get('username', '');

        try {
            $this->doValidateRegistration($username, $email, $realname, $comments, $password);
        } catch (Exception $ex) {
            FlashMessages::addMessage($ex->getMessage(), 'danger');

            return new RedirectResponse(route('register', [
                'comments' => $comments,
                'email'    => $email,
                'realname' => $realname,
                'username' => $username,
            ]));
        }

        Log::addAuthenticationLog('User registration requested for: ' . $username);

        $user = $this->user_service->create($username, $realname, $email, $password);
        $user
            ->setPreference('language', WT_LOCALE)
            ->setPreference('verified', '0')
            ->setPreference('verified_by_admin', '0')
            ->setPreference('reg_timestamp', date('U'))
            ->setPreference('reg_hashcode', md5(Uuid::uuid4()->toString()))
            ->setPreference('contactmethod', 'messaging2')
            ->setPreference('comment', $comments)
            ->setPreference('visibleonline', '1')
            ->setPreference('auto_accept', '0')
            ->setPreference('canadmin', '0')
            ->setPreference('sessiontime', '0');

        // Send a verification message to the user.
        /* I18N: %s is a server name/URL */
        Mail::send(
            new TreeUser($tree),
            $user,
            new TreeUser($tree),
            I18N::translate('Your registration at %s', WT_BASE_URL),
            view('emails/register-user-text', ['user' => $user]),
            view('emails/register-user-html', ['user' => $user])
        );

        // Tell the genealogy contact about the registration.
        $webmaster = $this->user_service->find((int) $tree->getPreference('WEBMASTER_USER_ID'));

        if ($webmaster !== null) {
            I18N::init($webmaster->getPreference('language'));

            /* I18N: %s is a server name/URL */
            $subject = I18N::translate('New registration at %s', $tree->title());

            /* I18N: %s is a server name/URL */
            Mail::send(
                new TreeUser($tree),
                $webmaster,
                $user,
                $subject,
                view('emails/register-notify-text', ['user' => $user, 'comments' => $comments]),
                view('emails/register-notify-html', ['user' => $user, 'comments' => $comments])
            );

            $mail1_method = $webmaster->getPreference('contact_method');
            if ($mail1_method !== 'messaging3' && $mail1_method !== 'mailto' && $mail1_method !== 'none') {
                DB::table('message')->insert([
                    'sender'     => $user->email(),
                    'ip_address' => $request->getClientIp(),
                    'user_id'    => $webmaster->id(),
                    'subject'    => $subject,
                    'body'       => view('emails/register-notify-text', ['user' => $user, 'comments' => $comments]),
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
     * Check the registration details.
     *
     * @param string $username
     * @param string $email
     * @param string $realname
     * @param string $comments
     * @param string $password
     *
     * @return void
     * @throws Exception
     */
    private function doValidateRegistration(string $username, string $email, string $realname, string $comments, string $password)
    {
        // All fields are required
        if ($username === '' || $email === '' || $realname === '' || $comments === '' || $password === '') {
            throw new Exception(I18N::translate('All fields must be completed.'));
        }

        // Username already exists
        if ($this->user_service->findByUserName($username) !== null) {
            throw new Exception(I18N::translate('Duplicate username. A user with that username already exists. Please choose another username.' . $username));
        }

        // Email already exists
        if ($this->user_service->findByEmail($email) !== null) {
            throw new Exception(I18N::translate('Duplicate email address. A user with that email already exists.'));
        }

        // No external links
        if (preg_match('/(?!' . preg_quote(WT_BASE_URL, '/') . ')(((?:http|https):\/\/)[a-zA-Z0-9.-]+)/', $comments, $match)) {
            throw new Exception(I18N::translate('You are not allowed to send messages that contain external links.') . ' ' . I18N::translate('You should delete the “%1$s” from “%2$s” and try again.', e($match[2]), e($match[1])));
        }
    }

    /**
     * Show an email verification page.
     *
     * @return Response
     * @throws NotFoundHttpException
     */
    public function verifyPage(): Response
    {
        $this->checkRegistrationAllowed();

        $title = I18N::translate('User verification');

        return $this->viewResponse('register-page', [
            'title' => $title,
        ]);
    }

    /**
     * Perform a registration.
     *
     * @return RedirectResponse
     * @throws NotFoundHttpException
     */
    public function verifyAction(): RedirectResponse
    {
        $this->checkRegistrationAllowed();

        return new RedirectResponse(route('tree-page'));
    }

    /**
     * Check that visitors are allowed to register on this site.
     *
     * @return void
     * @throws NotFoundHttpException
     */
    private function checkRegistrationAllowed()
    {
        if (Site::getPreference('USE_REGISTRATION_MODULE') !== '1') {
            throw new NotFoundHttpException();
        }
    }
}
