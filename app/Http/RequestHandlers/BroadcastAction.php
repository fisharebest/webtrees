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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\MessageService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function e;
use function redirect;
use function route;

/**
 * Send messages from an administrator.
 */
class BroadcastAction implements RequestHandlerInterface
{
    use ViewResponseTrait;

    /** @var MessageService */
    private $message_service;

    /**
     * MessagePage constructor.
     *
     * @param MessageService $message_service
     */
    public function __construct(MessageService $message_service)
    {
        $this->message_service = $message_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $user    = $request->getAttribute('user');
        $params  = (array) $request->getParsedBody();
        $body    = $params['body'];
        $subject = $params['subject'];
        $to      = $params['to'];

        $ip       = $request->getAttribute('client-ip');
        $to_users = $this->message_service->recipientUsers($to);

        if ($body === '' || $subject === '') {
            return redirect(route(BroadcastPage::class, [
                'body'    => $body,
                'subject' => $subject,
                'to'      => $to,
            ]));
        }

        $errors = false;

        foreach ($to_users as $to_user) {
            if ($this->message_service->deliverMessage($user, $to_user, $subject, $body, '', $ip)) {
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
}
