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

use Fisharebest\Webtrees\Http\Controllers\AbstractBaseController;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Mail;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\TreeUser;
use Fisharebest\Webtrees\User;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Controller for email verification.
 */
class VerifyEmailController extends AbstractBaseController
{
    /**
     * Respond to a verification link that was emailed to a user.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     * @param UserService            $user_service
     *
     * @return ResponseInterface
     */
    public function verify(ServerRequestInterface $request, Tree $tree, UserService $user_service): ResponseInterface
    {
        $username = $request->get('username', '');
        $token    = $request->get('token', '');

        $title = I18N::translate('User verification');

        $user = $user_service->findByUserName($username);

        if ($user instanceof User && $user->getPreference('reg_hashcode') === $token) {
            // switch language to webmaster settings
            $webmaster = $user_service->find((int) $tree->getPreference('WEBMASTER_USER_ID'));

            if ($webmaster instanceof User) {
                I18N::init($webmaster->getPreference('language'));

                /* I18N: %s is a server name/URL */
                $subject = I18N::translate('New user at %s', WT_BASE_URL . ' ' . $tree->title());

                Mail::send(
                    new TreeUser($tree),
                    $webmaster,
                    new TreeUser($tree),
                    $subject,
                    view('emails/verify-notify-text', ['user' => $user]),
                    view('emails/verify-notify-html', ['user' => $user])
                );

                $mail1_method = $webmaster->getPreference('CONTACT_METHOD');

                if ($mail1_method !== 'messaging3' && $mail1_method !== 'mailto' && $mail1_method !== 'none') {
                    DB::table('message')->insert([
                        'sender'     => $username,
                        'ip_address' => $request->getClientIp(),
                        'user_id'    => $webmaster->id(),
                        'subject'    => $subject,
                        'body'       => view('emails/verify-notify-text', ['user' => $user]),
                    ]);
                }
                I18N::init(WT_LOCALE);
            }

            $user
                ->setPreference('verified', '1')
                ->setPreference('reg_timestamp', date('U'))
                ->setPreference('reg_hashcode', '');

            Log::addAuthenticationLog('User ' . $username . ' verified their email address');

            return $this->viewResponse('verify-success-page', [
                'title' => $title,
            ]);
        }

        return $this->viewResponse('verify-failure-page', [
            'title' => $title,
        ]);
    }
}
