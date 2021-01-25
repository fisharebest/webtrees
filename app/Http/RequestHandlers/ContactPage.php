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

use Fisharebest\Webtrees\Exceptions\HttpAccessDeniedException;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\CaptchaService;
use Fisharebest\Webtrees\Services\MessageService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function in_array;
use function route;

/**
 * Compose a message from a visitor.
 */
class ContactPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    /** @var CaptchaService */
    private $captcha_service;

    /** @var MessageService */
    private $message_service;

    /** @var UserService */
    private $user_service;

    /**
     * MessagePage constructor.
     *
     * @param CaptchaService $captcha_service
     * @param MessageService $message_service
     * @param UserService    $user_service
     */
    public function __construct(
        CaptchaService $captcha_service,
        MessageService $message_service,
        UserService $user_service
    ) {
        $this->captcha_service = $captcha_service;
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

        $params     = $request->getQueryParams();
        $body       = $params['body'] ?? '';
        $from_email = $params['from_email'] ?? '';
        $from_name  = $params['from_name'] ?? '';
        $subject    = $params['subject'] ?? '';
        $to         = $params['to'] ?? '';
        $url        = $params['url'] ?? route(HomePage::class);

        $to_user = $this->user_service->findByUserName($to);

        if (!in_array($to_user, $this->message_service->validContacts($tree), false)) {
            throw new HttpAccessDeniedException('Invalid contact user id');
        }

        $to_name = $to_user->realName();

        $title = I18N::translate('Send a message');

        return $this->viewResponse('contact-page', [
            'body'       => $body,
            'captcha'    => $this->captcha_service->createCaptcha(),
            'from_email' => $from_email,
            'from_name'  => $from_name,
            'subject'    => $subject,
            'title'      => $title,
            'to'         => $to,
            'to_name'    => $to_name,
            'tree'       => $tree,
            'url'        => $url,
        ]);
    }
}
