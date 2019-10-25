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

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Carbon;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\GuestUser;
use Fisharebest\Webtrees\Http\RequestHandlers\ControlPanel;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\MailService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\SiteUser;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Send messages to users and groups of users.
 */
class MessageController extends AbstractBaseController
{
    /**
     * @var MailService
     */
    private $mail_service;

    /**
     * @var UserService
     */
    private $user_service;

    /**
     * MessageController constructor.
     *
     * @param MailService $mail_service
     * @param UserService $user_service
     */
    public function __construct(MailService $mail_service, UserService $user_service)
    {
        $this->mail_service = $mail_service;
        $this->user_service = $user_service;
    }

    /**
     * A form to compose a message from a member.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function broadcastPage(ServerRequestInterface $request): ResponseInterface
    {
        $user    = $request->getAttribute('user');
        $referer = $request->getHeaderLine('referer');
        $params  = $request->getQueryParams();
        $body    = $params['body'] ?? '';
        $subject = $params['subject'] ?? '';
        $to      = $params['to'];
        $url     = $params['url'] ?? $referer;

        $to_names = $this->recipientUsers($to)
            ->map(static function (UserInterface $user): string {
                return $user->realName();
            });

        $title = $this->recipientDescription($to);

        $this->layout = 'layouts/administration';

        return $this->viewResponse('admin/broadcast', [
            'body'     => $body,
            'from'     => $user,
            'subject'  => $subject,
            'title'    => $title,
            'to'       => $to,
            'to_names' => $to_names,
            'url'      => $url,
        ]);
    }

    /**
     * Send a message.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function broadcastAction(ServerRequestInterface $request): ResponseInterface
    {
        $user    = $request->getAttribute('user');
        $params  = $request->getParsedBody();
        $body    = $params['body'];
        $subject = $params['subject'];
        $to      = $params['to'];

        $ip       = $request->getAttribute('client-ip');
        $to_users = $this->recipientUsers($to);

        if ($body === '' || $subject === '') {
            return redirect(route('broadcast', [
                'body'    => $body,
                'subject' => $subject,
                'to'      => $to,
            ]));
        }

        $errors = false;

        foreach ($to_users as $to_user) {
            if ($this->deliverMessage($user, $to_user, $subject, $body, '', $ip)) {
                FlashMessages::addMessage(I18N::translate('The message was successfully sent to %s.', e($to_user->realName())), 'success');
            } else {
                $errors = true;
            }
        }

        if ($errors) {
            FlashMessages::addMessage(I18N::translate('The message was not sent.'), 'danger');
        }

        return redirect(route(ControlPanel::class));
    }

    /**
     * A form to compose a message from a visitor.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function contactPage(ServerRequestInterface $request): ResponseInterface
    {
        $tree       = $request->getAttribute('tree');
        $referer    = $request->getHeaderLine('referer');
        $params     = $request->getQueryParams();
        $body       = $params['body'] ?? '';
        $from_email = $params['from_email'] ?? '';
        $from_name  = $params['from_name'] ?? '';
        $subject    = $params['subject'] ?? '';
        $to         = $params['to'] ?? '';
        $url        = $params['url'] ?? $referer;

        $to_user = $this->user_service->findByUserName($to);

        if (!in_array($to_user, $this->validContacts($tree), false)) {
            throw new AccessDeniedHttpException('Invalid contact user id');
        }

        $to_name = $to_user->realName();

        $title = I18N::translate('Send a message');

        return $this->viewResponse('contact-page', [
            'body'       => $body,
            'from_email' => $from_email,
            'from_name'  => $from_name,
            'subject'    => $subject,
            'title'      => $title,
            'to'         => $to,
            'to_name'    => $to_name,
            'url'        => $url,
        ]);
    }

    /**
     * Send a message.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function contactAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree       = $request->getAttribute('tree');
        $params     = $request->getParsedBody();
        $body       = $params['body'];
        $from_email = $params['from_email'];
        $from_name  = $params['from_name'];
        $subject    = $params['subject'];
        $to         = $params['to'];
        $url        = $params['url'];
        $ip         = $request->getAttribute('client-ip');
        $to_user    = $this->user_service->findByUserName($to);

        if ($to_user === null) {
            throw new NotFoundHttpException();
        }

        if (!in_array($to_user, $this->validContacts($tree), false)) {
            throw new AccessDeniedHttpException('Invalid contact user id');
        }

        $errors = $body === '' || $subject === '' || $from_email === '' || $from_name === '';

        if (!preg_match('/^[^@]+@([^@]+)$/', $from_email, $match) || !checkdnsrr($match[1])) {
            FlashMessages::addMessage(I18N::translate('Please enter a valid email address.'), 'danger');
            $errors = true;
        }

        $base_url = $request->getAttribute('base_url');

        if (preg_match('/(?!' . preg_quote($base_url, '/') . ')(((?:ftp|http|https):\/\/)[a-zA-Z0-9.-]+)/', $subject . $body, $match)) {
            FlashMessages::addMessage(I18N::translate('You are not allowed to send messages that contain external links.') . ' ' . /* I18N: e.g. ‘You should delete the “http://” from “http://www.example.com” and try again.’ */
                I18N::translate('You should delete the “%1$s” from “%2$s” and try again.', $match[2], $match[1]), 'danger');
            $errors = true;
        }

        if ($errors) {
            return redirect(route('contact', [
                'body'       => $body,
                'from_email' => $from_email,
                'from_name'  => $from_name,
                'subject'    => $subject,
                'to'         => $to,
                'tree'       => $tree->name(),
                'url'        => $url,
            ]));
        }

        $sender = new GuestUser($from_email, $from_name);

        if ($this->deliverMessage($sender, $to_user, $subject, $body, $url, $ip)) {
            FlashMessages::addMessage(I18N::translate('The message was successfully sent to %s.', e($to_user->realName())), 'success');

            $url = $url ?: route('tree-page', ['tree' => $tree->name()]);

            return redirect($url);
        }

        FlashMessages::addMessage(I18N::translate('The message was not sent.'), 'danger');

        $redirect_url = route('contact', [
            'body'       => $body,
            'from_email' => $from_email,
            'from_name'  => $from_name,
            'subject'    => $subject,
            'to'         => $to,
            'tree'       => $tree->name(),
            'url'        => $url,
        ]);

        return redirect($redirect_url);
    }

    /**
     * A form to compose a message from a member.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function messagePage(ServerRequestInterface $request): ResponseInterface
    {
        $user    = $request->getAttribute('user');
        $referer = $request->getHeaderLine('referer');
        $params  = $request->getQueryParams();
        $body    = $params['body'] ?? '';
        $subject = $params['subject'] ?? '';
        $to      = $params['to'] ?? '';
        $url     = $params['url'] ?? $referer;
        $to_user = $this->user_service->findByUserName($to);

        if ($to_user === null || $to_user->getPreference('contactmethod') === 'none') {
            throw new AccessDeniedHttpException('Invalid contact user id');
        }

        $title = I18N::translate('Send a message');

        return $this->viewResponse('message-page', [
            'body'    => $body,
            'from'    => $user,
            'subject' => $subject,
            'title'   => $title,
            'to'      => $to_user,
            'url'     => $url,
        ]);
    }

    /**
     * Send a message.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function messageAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree    = $request->getAttribute('tree');
        $user    = $request->getAttribute('user');
        $params  = $request->getParsedBody();
        $body    = $params['body'];
        $subject = $params['subject'];
        $to      = $params['to'];
        $url     = $params['url'];
        $to_user = $this->user_service->findByUserName($to);
        $ip      = $request->getAttribute('client-ip');

        if ($to_user === null || $to_user->getPreference('contactmethod') === 'none') {
            throw new AccessDeniedHttpException('Invalid contact user id');
        }

        if ($body === '' || $subject === '') {
            return redirect(route('message', [
                'body'    => $body,
                'subject' => $subject,
                'to'      => $to,
                'tree'    => $tree,
                'url'     => $url,
            ]));
        }

        if ($this->deliverMessage($user, $to_user, $subject, $body, $url, $ip)) {
            FlashMessages::addMessage(I18N::translate('The message was successfully sent to %s.', e($to_user->realName())), 'success');

            $url = $url ?: route('tree-page', ['tree' => $tree->name()]);

            return redirect($url);
        }

        FlashMessages::addMessage(I18N::translate('The message was not sent.'), 'danger');

        $redirect_url = route('contact', [
            'body'    => $body,
            'subject' => $subject,
            'to'      => $to,
            'tree'    => $tree->name(),
            'url'     => $url,
        ]);

        return redirect($redirect_url);
    }

    /**
     * Contact messages can only be sent to the designated contacts
     *
     * @param Tree $tree
     *
     * @return UserInterface[]
     */
    private function validContacts(Tree $tree): array
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
    private function deliverMessage(UserInterface $sender, UserInterface $recipient, string $subject, string $body, string $url, string $ip): bool
    {
        $success = true;

        // Temporarily switch to the recipient's language
        $old_language = I18N::languageTag();
        I18N::init($recipient->getPreference('language'));

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
            $success = $this->mail_service->send(
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
    private function sendInternalMessage(UserInterface $user): bool
    {
        return in_array($user->getPreference('contactmethod'), [
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
    private function sendEmail(UserInterface $user): bool
    {
        return in_array($user->getPreference('contactmethod'), [
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
     * @return Collection
     */
    private function recipientUsers(string $to): Collection
    {
        switch ($to) {
            default:
            case 'all':
                return $this->user_service->all();
            case 'never_logged':
                return $this->user_service->all()->filter(static function (UserInterface $user): bool {
                    return $user->getPreference('verified_by_admin') && $user->getPreference('reg_timestamp') > $user->getPreference('sessiontime');
                });
            case 'last_6mo':
                $six_months_ago = Carbon::now()->subMonths(6)->unix();

                return $this->user_service->all()->filter(static function (UserInterface $user) use ($six_months_ago): bool {
                    $session_time = (int) $user->getPreference('sessiontime');

                    return $session_time > 0 && $session_time < $six_months_ago;
                });
        }
    }

    /**
     * @param string $to
     *
     * @return string
     */
    private function recipientDescription(string $to): string
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
}
