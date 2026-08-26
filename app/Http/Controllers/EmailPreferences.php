<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\EmailService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\SiteUser;
use Fisharebest\Webtrees\Validator;
use Fisharebest\Webtrees\Webtrees;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function e;
use function filter_var;
use function gethostbyaddr;
use function gethostbyname;
use function gethostname;
use function redirect;
use function route;

use const FILTER_VALIDATE_DOMAIN;

final class EmailPreferences
{
    use ViewResponseTrait;

    public function __construct(
        private UserInterface $user,
        private EmailService $email_service,
    ) {
    }

    public function get(): ResponseInterface
    {
        $mail_ssl_options       = $this->email_service->mailSslOptions();
        $mail_transport_options = $this->email_service->mailTransportOptions();

        $title = I18N::translate('Sending email');

        $SMTP_ACTIVE    = Site::getPreference('SMTP_ACTIVE');
        $SMTP_AUTH      = Site::getPreference('SMTP_AUTH');
        $SMTP_AUTH_USER = Site::getPreference('SMTP_AUTH_USER');
        $SMTP_DISP_NAME = Site::getPreference('SMTP_DISP_NAME');
        $SMTP_FROM_NAME = Site::getPreference('SMTP_FROM_NAME');
        $SMTP_HELO      = Site::getPreference('SMTP_HELO');
        $SMTP_HOST      = Site::getPreference('SMTP_HOST');
        $SMTP_PORT      = Site::getPreference('SMTP_PORT');
        $SMTP_SSL       = Site::getPreference('SMTP_SSL');
        $DKIM_DOMAIN    = Site::getPreference('DKIM_DOMAIN');
        $DKIM_SELECTOR  = Site::getPreference('DKIM_SELECTOR');
        $DKIM_KEY       = Site::getPreference('DKIM_KEY');

        $hostname = gethostname() !== false ? gethostname() : 'localhost';
        $ip       = gethostbyname($hostname);

        if ($ip !== $hostname) {
            $hostname = gethostbyaddr($ip);
        }

        // Defaults
        $SMTP_PORT      = $SMTP_PORT !== '' ? $SMTP_PORT : '25';
        $SMTP_HELO      = $SMTP_HELO !== '' ? $SMTP_HELO : $hostname;
        $SMTP_FROM_NAME = $SMTP_FROM_NAME !== '' ? $SMTP_FROM_NAME : ('no-reply@' . $SMTP_HELO);
        $SMTP_DISP_NAME = $SMTP_DISP_NAME !== '' ? $SMTP_DISP_NAME : Webtrees::NAME;

        $smtp_from_name_valid = $this->email_service->isValidEmail($SMTP_FROM_NAME);
        $smtp_helo_valid      = filter_var($SMTP_HELO, FILTER_VALIDATE_DOMAIN);

        $this->layout = 'layouts/administration';

        return $this->viewResponse('admin/site-mail', [
            'mail_ssl_options'       => $mail_ssl_options,
            'mail_transport_options' => $mail_transport_options,
            'title'                  => $title,
            'smtp_helo_valid'        => $smtp_helo_valid,
            'smtp_from_name_valid'   => $smtp_from_name_valid,
            'SMTP_ACTIVE'            => $SMTP_ACTIVE,
            'SMTP_AUTH'              => $SMTP_AUTH,
            'SMTP_AUTH_USER'         => $SMTP_AUTH_USER,
            'SMTP_DISP_NAME'         => $SMTP_DISP_NAME,
            'SMTP_FROM_NAME'         => $SMTP_FROM_NAME,
            'SMTP_HELO'              => $SMTP_HELO,
            'SMTP_HOST'              => $SMTP_HOST,
            'SMTP_PORT'              => $SMTP_PORT,
            'SMTP_SSL'               => $SMTP_SSL,
            'DKIM_DOMAIN'            => $DKIM_DOMAIN,
            'DKIM_SELECTOR'          => $DKIM_SELECTOR,
            'DKIM_KEY'               => $DKIM_KEY,
        ]);
    }

    public function post(ServerRequestInterface $request): ResponseInterface
    {
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
            $success = $this->email_service->send(new SiteUser(), $this->user, $this->user, 'test', 'test', 'test');

            if ($success) {
                FlashMessages::addMessage(I18N::translate('The message was successfully sent to %s.', e($this->user->email())), 'success');
            } else {
                FlashMessages::addMessage(I18N::translate('The message was not sent.'), 'danger');
            }

            return redirect(route(self::class));
        }

        return redirect(route(ControlPanel::class));
    }
}
