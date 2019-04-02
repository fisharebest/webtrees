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

use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Http\Controllers\AbstractBaseController;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Mail;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\TreeUser;
use Fisharebest\Webtrees\User;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Controller for requesting password resets.
 */
class ForgotPasswordController extends AbstractBaseController
{
    /**
     * Show a password reset page.
     *
     * @return ResponseInterface
     */
    public function forgotPasswordPage(): ResponseInterface
    {
        $title = I18N::translate('Request a new password');

        return $this->viewResponse('forgot-password-page', [
            'title' => $title,
        ]);
    }

    /**
     * Send a password reset email.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     * @param UserService            $user_service
     *
     * @return ResponseInterface
     */
    public function forgotPasswordAction(ServerRequestInterface $request, Tree $tree, UserService $user_service): ResponseInterface
    {
        $identifier = $request->get('identifier', '');

        $user = $user_service->findByIdentifier($identifier);

        if ($user instanceof User) {
            $password = $this->createNewPassword();
            $user->setPassword($password);

            Log::addAuthenticationLog('Password request was sent to user: ' . $user->userName());

            Mail::send(
                new TreeUser($tree),
                $user,
                new TreeUser($tree),
                I18N::translate('Lost password request'),
                view('emails/password-reset-text', [
                    'user'         => $user,
                    'new_password' => $password,
                ]), view('emails/password-reset-html', [
                    'user'         => $user,
                    'new_password' => $password,
                ])
            );

            FlashMessages::addMessage(I18N::translate('A new password has been created and emailed to %s. You can change this password after you sign in.', e($identifier)), 'success');

            return redirect(route('login', ['username' => $user->userName()]));
        }

        FlashMessages::addMessage(I18N::translate('There is no account with the username or email “%s”.', e($identifier)), 'danger');

        return redirect(route('forgot-password'));
    }

    /**
     * @return string
     */
    private function createNewPassword(): string
    {
        return Str::random(8);
    }
}
