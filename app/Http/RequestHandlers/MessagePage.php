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
use Fisharebest\Webtrees\Http\Exceptions\HttpAccessDeniedException;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function route;

/**
 * Compose a message from a logged-in user.
 */
class MessagePage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private UserService $user_service;

    /**
     * MessagePage constructor.
     *
     * @param UserService $user_service
     */
    public function __construct(UserService $user_service)
    {
        $this->user_service = $user_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $user    = $request->getAttribute('user');
        $body    = $request->getQueryParams()['body'] ?? '';
        $subject = $request->getQueryParams()['subject'] ?? '';
        $to      = $request->getQueryParams()['to'] ?? '';
        $url     = $request->getQueryParams()['url'] ?? route(HomePage::class);
        $to_user = $this->user_service->findByUserName($to);

        if ($to_user === null || $to_user->getPreference(UserInterface::PREF_CONTACT_METHOD) === 'none') {
            throw new HttpAccessDeniedException('Invalid contact user id');
        }

        $title = I18N::translate('Send a message');

        return $this->viewResponse('message-page', [
            'body'    => $body,
            'from'    => $user,
            'subject' => $subject,
            'title'   => $title,
            'to'      => $to_user,
            'tree'    => $tree,
            'url'     => $url,
        ]);
    }
}
