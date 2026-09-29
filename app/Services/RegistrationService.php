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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Http\RequestHandlers\VerifyEmail;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\NoReplyUser;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\SiteUser;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\TreeUser;
use Fisharebest\Webtrees\User;
use Illuminate\Support\Str;

use function date;
use function e;
use function preg_match;
use function preg_quote;
use function route;
use function view;

/**
 * Everything a self-registered account needs, regardless of who's asking
 * for it - today that's only the web registration form
 * (RegisterAction), but a module or a future API might want to offer the
 * same "request an account" flow without going through a browser form.
 * Keeping the actual behaviour here, instead of letting every caller
 * reimplement its own copy, means they all create exactly the same kind
 * of account (same preferences, same verification/approval workflow,
 * same notifications) and stay that way automatically if these rules
 * change later - a caller only ever decides *whether* to call
 * register(), never *what registering means*.
 *
 * Deliberately excludes anything specific to how a particular caller
 * receives its input or defends itself against automated abuse (a web
 * form's CAPTCHA, its own CSRF token, a request-scoped rate limiter) -
 * those stay each caller's own responsibility, layered on top of this.
 */
class RegistrationService
{
    private EmailService $email_service;

    private UserService $user_service;

    public function __construct(EmailService $email_service, UserService $user_service)
    {
        $this->email_service = $email_service;
        $this->user_service  = $user_service;
    }

    /**
     * Whether self-registration is turned on for this site at all - every
     * caller needs to check this before offering registration, not just
     * the web form.
     */
    public function registrationAllowed(): bool
    {
        return Site::getPreference('USE_REGISTRATION_MODULE') === '1';
    }

    /**
     * Check whether a set of registration details is acceptable, without
     * creating anything. A caller runs this (after whatever anti-bot
     * check it wants to apply on top) before calling register().
     *
     * @return string|null A translated error message, or null if valid.
     */
    public function checkRegistrationDetails(
        string $username,
        string $email,
        string $realname,
        string $comments,
        string $password,
        string $base_url
    ): ?string {
        if ($username === '' || $email === '' || $realname === '' || $comments === '' || $password === '') {
            return I18N::translate('All fields must be completed.');
        }

        if ($this->user_service->findByUserName($username) !== null) {
            return I18N::translate('Duplicate username. A user with that username already exists. Please choose another username.');
        }

        if ($this->user_service->findByEmail($email) !== null) {
            return I18N::translate('Duplicate email address. A user with that email already exists.');
        }

        if (preg_match('/(?!' . preg_quote($base_url, '/') . ')(((?:http|https):\/\/)[a-zA-Z0-9.-]+)/', $comments, $match) === 1) {
            return I18N::translate('You are not allowed to send messages that contain external links.') . ' ' . I18N::translate('You should delete the “%1$s” from “%2$s” and try again.', e($match[2]), e($match[1]));
        }

        return null;
    }

    /**
     * Create a pending self-registered account and send the verification
     * email to the new user plus a notification to every administrator -
     * the account exists immediately, but (see the preferences this
     * sets) can't log in until both the user has confirmed their email
     * address and an administrator has approved it.
     *
     * The caller is expected to have already checked registrationAllowed()
     * and checkRegistrationDetails() - this doesn't repeat either, so it
     * stays reusable for a caller with its own validation needs (an
     * administrator creating an account on someone else's behalf, say)
     * without those checks getting in the way.
     */
    public function register(
        string $username,
        string $email,
        string $realname,
        string $password,
        string $comments,
        string $base_url,
        ?Tree $tree = null,
        string $ip_address = ''
    ): User {
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

        $reply_to = $tree instanceof Tree ? new TreeUser($tree) : new SiteUser();

        $verify_url = route(VerifyEmail::class, [
            'username' => $user->userName(),
            'token'    => $token,
            'tree'     => $tree instanceof Tree ? $tree->name() : null,
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
                    'ip_address' => $ip_address,
                    'user_id'    => $administrator->id(),
                    'subject'    => $subject,
                    'body'       => $body_text,
                ]);
            }
        }

        return $user;
    }
}
