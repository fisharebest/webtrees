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

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\NoReplyUser;
use Fisharebest\Webtrees\Services\EmailService;
use Fisharebest\Webtrees\Services\MessageService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\SiteUser;
use Fisharebest\Webtrees\User;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Acknowledge an email verification code.
 */
class VerifyEmail implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private EmailService $email_service;

    private UserService $user_service;

    /**
     * @param EmailService $email_service
     * @param UserService  $user_service
     */
    public function __construct(EmailService $email_service, UserService $user_service)
    {
        $this->email_service = $email_service;
        $this->user_service  = $user_service;
    }

    /**
     * Respond to a verification link that was emailed to a user.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $token    = $request->getAttribute('token');
        $tree     = Validator::attributes($request)->treeOptional();
        $username = $request->getAttribute('username');

        $title = I18N::translate('User verification');

        $user = $this->user_service->findByUserName($username);

        if ($user instanceof User && $user->getPreference(UserInterface::PREF_VERIFICATION_TOKEN) === $token) {
            $old_language = I18N::languageTag();

            foreach ($this->user_service->administrators() as $administrator) {
                // switch language to administrator settings
                I18N::init($administrator->getPreference(UserInterface::PREF_LANGUAGE, 'en-US'));

                $base_url = Validator::attributes($request)->string('base_url');

                /* I18N: %s is a server name/URL */
                $subject = I18N::translate('New user at %s', $base_url);

                $this->email_service->send(
                    new SiteUser(),
                    $administrator,
                    new NoReplyUser(),
                    $subject,
                    view('emails/verify-notify-text', ['user' => $user]),
                    view('emails/verify-notify-html', ['user' => $user])
                );

                $mail1_method = $administrator->getPreference('CONTACT_METHOD');

                if (
                    $mail1_method !== MessageService::CONTACT_METHOD_EMAIL &&
                    $mail1_method !== MessageService::CONTACT_METHOD_MAILTO &&
                    $mail1_method !== MessageService::CONTACT_METHOD_NONE
                ) {
                    DB::table('message')->insert([
                        'sender'     => $username,
                        'ip_address' => $request->getAttribute('client-ip'),
                        'user_id'    => $administrator->id(),
                        'subject'    => $subject,
                        'body'       => view('emails/verify-notify-text', ['user' => $user]),
                    ]);
                }
            }
            I18N::init($old_language);

            $user->setPreference(UserInterface::PREF_IS_EMAIL_VERIFIED, '1');
            $user->setPreference(UserInterface::PREF_TIMESTAMP_REGISTERED, date('U'));
            $user->setPreference(UserInterface::PREF_VERIFICATION_TOKEN, '');

            Log::addAuthenticationLog('User ' . $username . ' verified their email address');

            return $this->viewResponse('verify-success-page', [
                'title' => $title,
                'tree'  => $tree,
            ]);
        }

        return $this->viewResponse('verify-failure-page', [
            'title' => $title,
            'tree'  => $tree,
        ]);
    }
}
