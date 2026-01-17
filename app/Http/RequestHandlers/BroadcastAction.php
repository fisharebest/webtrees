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

use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\MessageService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function e;
use function redirect;
use function route;

final class BroadcastAction implements RequestHandlerInterface
{
    use ViewResponseTrait;

    public function __construct(
        private readonly MessageService $message_service,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $recipients = $this->message_service->recipientTypes();

        $user    = Validator::attributes($request)->user();
        $to      = Validator::attributes($request)->isInArrayKeys($recipients)->string('to');
        $ip      = Validator::attributes($request)->string('client-ip');
        $body    = Validator::parsedBody($request)->isNotEmpty()->string('body');
        $subject = Validator::parsedBody($request)->isNotEmpty()->string('subject');

        if ($body === '' || $subject === '') {
            return redirect(route(BroadcastPage::class, [
                'body'    => $body,
                'subject' => $subject,
                'to'      => $to,
            ]));
        }

        foreach ($this->message_service->recipientUsers($to) as $to_user) {
            if ($this->message_service->deliverMessage($user, $to_user, $subject, $body, '', $ip)) {
                FlashMessages::addMessage(
                    I18N::translate('The message was successfully sent to %s.', e($to_user->realName())),
                    'success'
                );
            } else {
                FlashMessages::addMessage(
                    I18N::translate('The message was not sent to %s.', e($to_user->realName())),
                    'danger'
                );
            }
        }

        return redirect(route(ControlPanel::class));
    }
}
