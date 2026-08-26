<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Enums\ContactMethod;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Http\Exceptions\HttpForbiddenException;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\MessageService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function e;
use function redirect;
use function route;

final class Message
{
    use ViewResponseTrait;

    public function __construct(
        private UserInterface $user,
        private UserService $user_service,
        private MessageService $message_service
    ) {
    }

    public function get(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $body    = Validator::queryParams($request)->string('body', '');
        $subject = Validator::queryParams($request)->string('subject', '');
        $to      = Validator::queryParams($request)->string('to', '');
        $url     = Validator::queryParams($request)->isLocalUrl()->string('url', route(HomePage::class));
        $to_user = $this->user_service->findByUserName($to);

        if ($to_user === null) {
            throw new HttpForbiddenException();
        }

        if (ContactMethod::fromUser($to_user)->isNotContactable()) {
            throw new HttpForbiddenException();
        }

        $title = I18N::translate('Send a message');

        return $this->viewResponse('message-page', [
            'body'    => $body,
            'from'    => $this->user,
            'subject' => $subject,
            'title'   => $title,
            'to'      => $to_user,
            'tree'    => $tree,
            'url'     => $url,
        ]);
    }

    public function post(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $ip       = Validator::attributes($request)->string('client-ip');
        $base_url = Validator::attributes($request)->string('base_url');
        $body     = Validator::parsedBody($request)->string('body');
        $subject  = Validator::parsedBody($request)->string('subject');
        $to       = Validator::parsedBody($request)->string('to');
        $to_user  = $this->user_service->findByUserName($to);
        $url      = Validator::parsedBody($request)->isLocalUrl()->string('url', $base_url);

        if ($to_user === null) {
            throw new HttpForbiddenException();
        }

        if (ContactMethod::fromUser($to_user)->isNotContactable()) {
            throw new HttpForbiddenException();
        }

        if ($body === '' || $subject === '') {
            return redirect(route(Message::class, [
                'body'    => $body,
                'subject' => $subject,
                'to'      => $to,
                'tree'    => $tree->name(),
                'url'     => $url,
            ]));
        }

        if ($this->message_service->deliverMessage($this->user, $to_user, $subject, $body, $url, $ip)) {
            FlashMessages::addMessage(I18N::translate('The message was successfully sent to %s.', e($to_user->realName())), 'success');

            return redirect($url);
        }

        FlashMessages::addMessage(I18N::translate('The message was not sent.'), 'danger');

        return redirect(route(Message::class, [
            'body'    => $body,
            'subject' => $subject,
            'to'      => $to,
            'tree'    => $tree->name(),
            'url'     => $url,
        ]));
    }
}
