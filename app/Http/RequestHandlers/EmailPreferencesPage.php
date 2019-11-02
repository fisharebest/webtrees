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

use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\EmailService;
use Fisharebest\Webtrees\Site;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function filter_var;

use const FILTER_VALIDATE_DOMAIN;

/**
 * Edit the email preferences.
 */
class EmailPreferencesPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    /** @var EmailService */
    private $email_service;

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
        $mail_ssl_options       = $this->email_service->mailSslOptions();
        $mail_transport_options = $this->email_service->mailTransportOptions();

        $title = I18N::translate('Sending email');

        $SMTP_ACTIVE    = Site::getPreference('SMTP_ACTIVE');
        $SMTP_AUTH      = Site::getPreference('SMTP_AUTH');
        $SMTP_AUTH_USER = Site::getPreference('SMTP_AUTH_USER');
        $SMTP_FROM_NAME = $this->email_service->senderEmail();
        $SMTP_HELO      = $this->email_service->localDomain();
        $SMTP_HOST      = Site::getPreference('SMTP_HOST');
        $SMTP_PORT      = Site::getPreference('SMTP_PORT');
        $SMTP_SSL       = Site::getPreference('SMTP_SSL');
        $DKIM_DOMAIN    = Site::getPreference('DKIM_DOMAIN');
        $DKIM_SELECTOR  = Site::getPreference('DKIM_SELECTOR');
        $DKIM_KEY       = Site::getPreference('DKIM_KEY');

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
}
