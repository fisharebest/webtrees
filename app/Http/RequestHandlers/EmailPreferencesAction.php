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
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\EmailService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\SiteUser;
use Fisharebest\Webtrees\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function e;
use function redirect;
use function route;

/**
 * Edit the email preferences.
 */
class EmailPreferencesAction implements RequestHandlerInterface
{
    private EmailService $email_service;

    /**
     * AdminSiteController constructor.
     *
     * @param EmailService $email_service
     */
    public function __construct(EmailService $email_service)
    {
        $this->email_service = $email_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $user = $request->getAttribute('user');
        assert($user instanceof User);

        $params = (array) $request->getParsedBody();

        Site::setPreference('SMTP_ACTIVE', $params['SMTP_ACTIVE']);
        Site::setPreference('SMTP_DISP_NAME', $params['SMTP_DISP_NAME']);
        Site::setPreference('SMTP_FROM_NAME', $params['SMTP_FROM_NAME']);
        Site::setPreference('SMTP_HOST', $params['SMTP_HOST']);
        Site::setPreference('SMTP_PORT', $params['SMTP_PORT']);
        Site::setPreference('SMTP_AUTH', $params['SMTP_AUTH']);
        Site::setPreference('SMTP_AUTH_USER', $params['SMTP_AUTH_USER']);
        Site::setPreference('SMTP_SSL', $params['SMTP_SSL']);
        Site::setPreference('SMTP_HELO', $params['SMTP_HELO']);
        Site::setPreference('DKIM_DOMAIN', $params['DKIM_DOMAIN']);
        Site::setPreference('DKIM_SELECTOR', $params['DKIM_SELECTOR']);
        Site::setPreference('DKIM_KEY', $params['DKIM_KEY']);

        if ($params['SMTP_AUTH_PASS'] !== '') {
            Site::setPreference('SMTP_AUTH_PASS', $params['SMTP_AUTH_PASS']);
        }

        FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');

        $test = $params['test'] ?? '';

        if ($test === 'on') {
            $success = $this->email_service->send(new SiteUser(), $user, $user, 'test', 'test', 'test');

            if ($success) {
                FlashMessages::addMessage(I18N::translate('The message was successfully sent to %s.', e($user->email())), 'success');
            } else {
                FlashMessages::addMessage(I18N::translate('The message was not sent.'), 'danger');
            }

            return redirect(route(EmailPreferencesPage::class));
        }

        return redirect(route(ControlPanel::class));
    }
}
