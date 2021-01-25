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

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\Services\MessageService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Compose messages from an administrator.
 */
class BroadcastPage implements RequestHandlerInterface
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
        $params  = $request->getQueryParams();
        $body    = $params['body'] ?? '';
        $subject = $params['subject'] ?? '';
        $to      = $params['to'];

        $to_names = $this->message_service->recipientUsers($to)
            ->map(static function (UserInterface $user): string {
                return $user->realName();
            });

        $title = $this->message_service->recipientDescription($to);

        $this->layout = 'layouts/administration';

        return $this->viewResponse('admin/broadcast', [
            'body'     => $body,
            'from'     => $user,
            'subject'  => $subject,
            'title'    => $title,
            'to'       => $to,
            'to_names' => $to_names,
        ]);
    }
}
