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

use Fisharebest\Webtrees\Http\Exceptions\HttpAccessDeniedException;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\CaptchaService;
use Fisharebest\Webtrees\Services\MessageService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function in_array;
use function route;

/**
 * Compose a message from a visitor.
 */
class ContactPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private CaptchaService $captcha_service;

    private MessageService $message_service;

    private UserService $user_service;

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
        $tree       = Validator::attributes($request)->tree();
        $body       = Validator::queryParams($request)->string('body', '');
        $from_email = Validator::queryParams($request)->string('from_email', '');
        $from_name  = Validator::queryParams($request)->string('from_name', '');
        $subject    = Validator::queryParams($request)->string('subject', '');
        $to         = Validator::queryParams($request)->string('to', '');
        $url        = Validator::queryParams($request)->isLocalUrl()->string('url', route(HomePage::class));

        $to_user = $this->user_service->findByUserName($to);

        if ($to_user === null || !in_array($to_user, $this->message_service->validContacts($tree), false)) {
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
