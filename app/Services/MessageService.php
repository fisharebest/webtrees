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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Carbon;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\SiteUser;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;

use function array_filter;
use function in_array;
use function view;

/**
 * Send messages between users and from visitors to the site.
 */
class MessageService
{
    private EmailService $email_service;

    private UserService $user_service;

    /**
     * MessageService constructor.
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
     * Contact messages can only be sent to the designated contacts
     *
     * @param Tree $tree
     *
     * @return array<UserInterface>
     */
    public function validContacts(Tree $tree): array
    {
        $contacts = [
            $this->user_service->find((int) $tree->getPreference('CONTACT_USER_ID')),
            $this->user_service->find((int) $tree->getPreference('WEBMASTER_USER_ID')),
        ];

        return array_filter($contacts);
    }

    /**
     * Add a message to a user's inbox, send it to them via email, or both.
     *
     * @param UserInterface $sender
     * @param UserInterface $recipient
     * @param string        $subject
     * @param string        $body
     * @param string        $url
     * @param string        $ip
     *
     * @return bool
     */
    public function deliverMessage(UserInterface $sender, UserInterface $recipient, string $subject, string $body, string $url, string $ip): bool
    {
        $success = true;

        // Temporarily switch to the recipient's language
        $old_language = I18N::languageTag();
        I18N::init($recipient->getPreference(UserInterface::PREF_LANGUAGE));

        $body_text = view('emails/message-user-text', [
            'sender'    => $sender,
            'recipient' => $recipient,
            'message'   => $body,
            'url'       => $url,
        ]);

        $body_html = view('emails/message-user-html', [
            'sender'    => $sender,
            'recipient' => $recipient,
            'message'   => $body,
            'url'       => $url,
        ]);

        // Send via the internal messaging system.
        if ($this->sendInternalMessage($recipient)) {
            DB::table('message')->insert([
                'sender'     => Auth::check() ? Auth::user()->email() : $sender->email(),
                'ip_address' => $ip,
                'user_id'    => $recipient->id(),
                'subject'    => $subject,
                'body'       => $body_text,
            ]);
        }

        // Send via email
        if ($this->sendEmail($recipient)) {
            $success = $this->email_service->send(
                new SiteUser(),
                $recipient,
                $sender,
                I18N::translate('webtrees message') . ' - ' . $subject,
                $body_text,
                $body_html
            );
        }

        I18N::init($old_language);

        return $success;
    }

    /**
     * Should we send messages to this user via internal messaging?
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    public function sendInternalMessage(UserInterface $user): bool
    {
        return in_array($user->getPreference(UserInterface::PREF_CONTACT_METHOD), [
            'messaging',
            'messaging2',
            'mailto',
            'none',
        ], true);
    }

    /**
     * Should we send messages to this user via email?
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    public function sendEmail(UserInterface $user): bool
    {
        return in_array($user->getPreference(UserInterface::PREF_CONTACT_METHOD), [
            'messaging2',
            'messaging3',
            'mailto',
            'none',
        ], true);
    }

    /**
     * Convert a username (or mailing list name) into an array of recipients.
     *
     * @param string $to
     *
     * @return Collection<User>
     */
    public function recipientUsers(string $to): Collection
    {
        switch ($to) {
            default:
            case 'all':
                return $this->user_service->all();
            case 'never_logged':
                return $this->user_service->all()->filter(static function (UserInterface $user): bool {
                    return $user->getPreference(UserInterface::PREF_IS_ACCOUNT_APPROVED) === '1' && $user->getPreference(UserInterface::PREF_TIMESTAMP_REGISTERED) > $user->getPreference(UserInterface::PREF_TIMESTAMP_ACTIVE);
                });
            case 'last_6mo':
                $six_months_ago = Carbon::now()->subMonths(6)->unix();

                return $this->user_service->all()->filter(static function (UserInterface $user) use ($six_months_ago): bool {
                    $session_time = (int) $user->getPreference(UserInterface::PREF_TIMESTAMP_ACTIVE);

                    return $session_time > 0 && $session_time < $six_months_ago;
                });
        }
    }

    /**
     * @param string $to
     *
     * @return string
     */
    public function recipientDescription(string $to): string
    {
        switch ($to) {
            default:
            case 'all':
                return I18N::translate('Send a message to all users');
            case 'never_logged':
                return I18N::translate('Send a message to users who have never signed in');
            case 'last_6mo':
                return I18N::translate('Send a message to users who have not signed in for 6 months');
        }
    }

    /**
     * A list of contact methods (e.g. for an edit control).
     *
     * @return array<string>
     */
    public function contactMethods(): array
    {
        return [
            'messaging'  => I18N::translate('Internal messaging'),
            'messaging2' => I18N::translate('Internal messaging with emails'),
            'messaging3' => I18N::translate('webtrees sends emails with no storage'),
            'mailto'     => I18N::translate('Mailto link'),
            'none'       => I18N::translate('No contact'),
        ];
    }
}
