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

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Http\Exceptions\HttpAccessDeniedException;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\MessageService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function e;
use function redirect;
use function route;

/**
 * Send a message from a logged-in user.
 */
class MessageAction implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private MessageService $message_service;

    private UserService $user_service;

    /**
     * @param MessageService $message_service
     * @param UserService    $user_service
     */
    public function __construct(MessageService $message_service, UserService $user_service)
    {
        $this->user_service    = $user_service;
        $this->message_service = $message_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree     = Validator::attributes($request)->tree();
        $user     = Validator::attributes($request)->user();
        $ip       = Validator::attributes($request)->string('client-ip');
        $base_url = Validator::attributes($request)->string('base_url');
        $body     = Validator::parsedBody($request)->string('body');
        $subject  = Validator::parsedBody($request)->string('subject');
        $to       = Validator::parsedBody($request)->string('to');
        $to_user  = $this->user_service->findByUserName($to);
        $url      = Validator::parsedBody($request)->isLocalUrl()->string('url', $base_url);

        if ($to_user === null || $to_user->getPreference(UserInterface::PREF_CONTACT_METHOD) === MessageService::CONTACT_METHOD_NONE) {
            throw new HttpAccessDeniedException('Invalid contact user id');
        }

        if ($body === '' || $subject === '') {
            return redirect(route(MessagePage::class, [
                'body'    => $body,
                'subject' => $subject,
                'to'      => $to,
                'tree'    => $tree->name(),
                'url'     => $url,
            ]));
        }

        if ($this->message_service->deliverMessage($user, $to_user, $subject, $body, $url, $ip)) {
            FlashMessages::addMessage(I18N::translate('The message was successfully sent to %s.', e($to_user->realName())), 'success');

            return redirect($url);
        }

        FlashMessages::addMessage(I18N::translate('The message was not sent.'), 'danger');

        return redirect(route(MessagePage::class, [
            'body'    => $body,
            'subject' => $subject,
            'to'      => $to,
            'tree'    => $tree->name(),
            'url'     => $url,
        ]));
    }
}
