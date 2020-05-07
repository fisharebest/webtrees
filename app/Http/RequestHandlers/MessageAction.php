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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Exceptions\HttpAccessDeniedException;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\MessageService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function e;
use function redirect;
use function route;

/**
 * Send a message from a logged-in user.
 */
class MessageAction implements RequestHandlerInterface
{
    use ViewResponseTrait;

    /** @var MessageService */
    private $message_service;

    /** @var UserService */
    private $user_service;

    /**
     * MessagePage constructor.
     *
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
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $user    = $request->getAttribute('user');
        $params  = (array) $request->getParsedBody();
        $body    = $params['body'];
        $subject = $params['subject'];
        $to      = $params['to'];
        $url     = $params['url'];
        $to_user = $this->user_service->findByUserName($to);
        $ip      = $request->getAttribute('client-ip');

        if ($to_user === null || $to_user->getPreference(User::PREF_CONTACT_METHOD) === 'none') {
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

            $url = $url ?: route(TreePage::class, ['tree' => $tree->name()]);

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
