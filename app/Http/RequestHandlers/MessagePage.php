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

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Http\Exceptions\HttpAccessDeniedException;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\MessageService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function route;

final class MessagePage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    public function __construct(
        private readonly UserService $user_service,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree    = Validator::attributes($request)->tree();
        $user    = Validator::attributes($request)->user();
        $body    = Validator::queryParams($request)->string('body', '');
        $subject = Validator::queryParams($request)->string('subject', '');
        $to      = Validator::queryParams($request)->string('to', '');
        $url     = Validator::queryParams($request)->isLocalUrl()->string('url', route(HomePage::class));
        $to_user = $this->user_service->findByUserName($to);

        if ($to_user === null || $to_user->getPreference(UserInterface::PREF_CONTACT_METHOD) === MessageService::CONTACT_METHOD_NONE) {
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
