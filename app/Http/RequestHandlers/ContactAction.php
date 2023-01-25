<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\GuestUser;
use Fisharebest\Webtrees\Http\Exceptions\HttpAccessDeniedException;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\CaptchaService;
use Fisharebest\Webtrees\Services\EmailService;
use Fisharebest\Webtrees\Services\MessageService;
use Fisharebest\Webtrees\Services\RateLimitService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function e;
use function in_array;
use function preg_match;
use function preg_quote;
use function redirect;
use function route;

/**
 * Send a message from a visitor.
 */
class ContactAction implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private CaptchaService $captcha_service;

    private EmailService $email_service;

    private MessageService $message_service;

    private RateLimitService $rate_limit_service;

    private UserService $user_service;

    /**
     * MessagePage constructor.
     *
     * @param CaptchaService   $captcha_service
     * @param EmailService     $email_service
     * @param MessageService   $message_service
     * @param RateLimitService $rate_limit_service
     * @param UserService      $user_service
     */
    public function __construct(
        CaptchaService $captcha_service,
        EmailService $email_service,
        MessageService $message_service,
        RateLimitService $rate_limit_service,
        UserService $user_service
    ) {
        $this->captcha_service    = $captcha_service;
        $this->email_service      = $email_service;
        $this->user_service       = $user_service;
        $this->rate_limit_service = $rate_limit_service;
        $this->message_service    = $message_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree       = Validator::attributes($request)->tree();
        $ip         = Validator::attributes($request)->string('client-ip');
        $base_url   = Validator::attributes($request)->string('base_url');
        $body       = Validator::parsedBody($request)->string('body');
        $from_email = Validator::parsedBody($request)->string('from_email');
        $from_name  = Validator::parsedBody($request)->string('from_name');
        $subject    = Validator::parsedBody($request)->string('subject');
        $to         = Validator::parsedBody($request)->string('to');
        $url        = Validator::parsedBody($request)->isLocalUrl()->string('url', $base_url);
        $to_user    = $this->user_service->findByUserName($to);

        if ($to_user === null) {
            throw new HttpNotFoundException();
        }

        if (!in_array($to_user, $this->message_service->validContacts($tree), false)) {
            throw new HttpAccessDeniedException('Invalid contact user id');
        }

        $errors = $body === '' || $subject === '' || $from_email === '' || $from_name === '';

        if ($this->captcha_service->isRobot($request)) {
            FlashMessages::addMessage(I18N::translate('Please try again.'), 'danger');
            $errors = true;
        }

        if (!$this->email_service->isValidEmail($from_email)) {
            FlashMessages::addMessage(I18N::translate('Please enter a valid email address.'), 'danger');
            $errors = true;
        }

        if (preg_match('/(?!' . preg_quote($base_url, '/') . ')(((?:ftp|http|https):\/\/)[a-zA-Z0-9.-]+)/', $subject . $body, $match)) {
            FlashMessages::addMessage(I18N::translate('You are not allowed to send messages that contain external links.') . ' ' . /* I18N: e.g. ‘You should delete the “https://” from “https://www.example.com” and try again.’ */
                I18N::translate('You should delete the “%1$s” from “%2$s” and try again.', $match[2], $match[1]), 'danger');
            $errors = true;
        }

        if ($errors) {
            return redirect(route(ContactPage::class, [
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

        $this->rate_limit_service->limitRateForUser($to_user, 20, 1200, 'rate-limit-contact');

        if ($this->message_service->deliverMessage($sender, $to_user, $subject, $body, $url, $ip)) {
            FlashMessages::addMessage(I18N::translate('The message was successfully sent to %s.', e($to_user->realName())), 'success');

            return redirect($url);
        }

        FlashMessages::addMessage(I18N::translate('The message was not sent.'), 'danger');

        $redirect_url = route(ContactPage::class, [
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
}
