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

use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\Services\MessageService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Compose messages from an administrator.
 */
class BroadcastPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private MessageService $message_service;

    /**
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
        $recipient_types = $this->message_service->recipientTypes();

        $user = Validator::attributes($request)->user();
        $to   = Validator::attributes($request)->isInArrayKeys($recipient_types)->string('to');

        $title = $recipient_types[$to];

        $this->layout = 'layouts/administration';

        return $this->viewResponse('admin/broadcast', [
            'from'       => $user,
            'title'      => $title,
            'to'         => $to,
            'recipients' => $this->message_service->recipientUsers($to),
        ]);
    }
}
