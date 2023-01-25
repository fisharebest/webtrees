<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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
use Fisharebest\Webtrees\Validator;
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
        $user          = Validator::attributes($request)->user();
        $active        = Validator::parsedBody($request)->string('SMTP_ACTIVE');
        $disp_name     = Validator::parsedBody($request)->string('SMTP_DISP_NAME');
        $from_name     = Validator::parsedBody($request)->string('SMTP_FROM_NAME');
        $host          = Validator::parsedBody($request)->string('SMTP_HOST');
        $port          = Validator::parsedBody($request)->string('SMTP_PORT');
        $auth          = Validator::parsedBody($request)->string('SMTP_AUTH');
        $auth_user     = Validator::parsedBody($request)->string('SMTP_AUTH_USER');
        $auth_pass     = Validator::parsedBody($request)->string('SMTP_AUTH_PASS');
        $ssl           = Validator::parsedBody($request)->string('SMTP_SSL');
        $helo          = Validator::parsedBody($request)->string('SMTP_HELO');
        $dkim_domain   = Validator::parsedBody($request)->string('DKIM_DOMAIN');
        $dkim_selector = Validator::parsedBody($request)->string('DKIM_SELECTOR');
        $dkim_key      = Validator::parsedBody($request)->string('DKIM_KEY');
        $test          = Validator::parsedBody($request)->boolean('test', false);


        Site::setPreference('SMTP_ACTIVE', $active);
        Site::setPreference('SMTP_DISP_NAME', $disp_name);
        Site::setPreference('SMTP_FROM_NAME', $from_name);
        Site::setPreference('SMTP_HOST', $host);
        Site::setPreference('SMTP_PORT', $port);
        Site::setPreference('SMTP_AUTH', $auth);
        Site::setPreference('SMTP_AUTH_USER', $auth_user);
        Site::setPreference('SMTP_SSL', $ssl);
        Site::setPreference('SMTP_HELO', $helo);
        Site::setPreference('DKIM_DOMAIN', $dkim_domain);
        Site::setPreference('DKIM_SELECTOR', $dkim_selector);
        Site::setPreference('DKIM_KEY', $dkim_key);

        if ($auth_pass !== '') {
            Site::setPreference('SMTP_AUTH_PASS', $auth_pass);
        }

        FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');

        if ($test) {
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
