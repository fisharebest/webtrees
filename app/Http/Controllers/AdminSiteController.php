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

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Localization\Locale\LocaleInterface;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Http\RequestHandlers\ControlPanel;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Services\MailService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Site;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function app;
use function assert;
use function filter_var;

use const FILTER_VALIDATE_DOMAIN;

/**
 * Controller for site administration.
 */
class AdminSiteController extends AbstractBaseController
{
    /** @var string */
    protected $layout = 'layouts/administration';

    /** @var MailService */
    private $mail_service;

    /** @var ModuleService */
    private $module_service;

    /**
     * AdminSiteController constructor.
     *
     * @param MailService   $mail_service
     * @param ModuleService $module_service
     */
    public function __construct(MailService $mail_service, ModuleService $module_service)
    {
        $this->mail_service   = $mail_service;
        $this->module_service = $module_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function mailForm(ServerRequestInterface $request): ResponseInterface
    {
        $mail_ssl_options       = $this->mail_service->mailSslOptions();
        $mail_transport_options = $this->mail_service->mailTransportOptions();

        $title = I18N::translate('Sending email');

        $SMTP_ACTIVE    = Site::getPreference('SMTP_ACTIVE');
        $SMTP_AUTH      = Site::getPreference('SMTP_AUTH');
        $SMTP_AUTH_USER = Site::getPreference('SMTP_AUTH_USER');
        $SMTP_FROM_NAME = $this->mail_service->senderEmail();
        $SMTP_HELO      = $this->mail_service->localDomain();
        $SMTP_HOST      = Site::getPreference('SMTP_HOST');
        $SMTP_PORT      = Site::getPreference('SMTP_PORT');
        $SMTP_SSL       = Site::getPreference('SMTP_SSL');
        $DKIM_DOMAIN    = Site::getPreference('DKIM_DOMAIN');
        $DKIM_SELECTOR  = Site::getPreference('DKIM_SELECTOR');
        $DKIM_KEY       = Site::getPreference('DKIM_KEY');

        $smtp_from_name_valid = $this->mail_service->isValidEmail($SMTP_FROM_NAME);
        $smtp_helo_valid      = filter_var($SMTP_HELO, FILTER_VALIDATE_DOMAIN);

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

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function mailSave(ServerRequestInterface $request): ResponseInterface
    {
        $params = $request->getParsedBody();

        Site::setPreference('SMTP_ACTIVE', $params['SMTP_ACTIVE']);
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
        $url = route(ControlPanel::class);

        return redirect($url);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function preferencesForm(ServerRequestInterface $request): ResponseInterface
    {
        $all_themes = $this->themeOptions();

        $title = I18N::translate('Website preferences');

        return $this->viewResponse('admin/site-preferences', [
            'all_themes'         => $all_themes,
            'max_execution_time' => (int) get_cfg_var('max_execution_time'),
            'title'              => $title,
        ]);
    }

    /**
     * @return Collection
     */
    private function themeOptions(): Collection
    {
        return $this->module_service
            ->findByInterface(ModuleThemeInterface::class)
            ->map($this->module_service->titleMapper());
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function preferencesSave(ServerRequestInterface $request): ResponseInterface
    {
        $params = $request->getParsedBody();

        $INDEX_DIRECTORY = $params['INDEX_DIRECTORY'];
        if (substr($INDEX_DIRECTORY, -1) !== '/') {
            $INDEX_DIRECTORY .= '/';
        }
        if (is_dir($INDEX_DIRECTORY)) {
            Site::setPreference('INDEX_DIRECTORY', $INDEX_DIRECTORY);
        } else {
            FlashMessages::addMessage(I18N::translate('The folder “%s” does not exist.', e($INDEX_DIRECTORY)), 'danger');
        }

        Site::setPreference('THEME_DIR', $params['THEME_DIR']);
        Site::setPreference('ALLOW_CHANGE_GEDCOM', $params['ALLOW_CHANGE_GEDCOM']);
        Site::setPreference('TIMEZONE', $params['TIMEZONE']);

        FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');
        $url = route(ControlPanel::class);

        return redirect($url);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function registrationForm(ServerRequestInterface $request): ResponseInterface
    {
        $locale = $request->getAttribute('locale');
        assert($locale instanceof LocaleInterface);

        $title = I18N::translate('Sign-in and registration');

        $registration_text_options = $this->registrationTextOptions();

        return $this->viewResponse('admin/site-registration', [
            'language_tag'              => $locale->languageTag(),
            'registration_text_options' => $registration_text_options,
            'title'                     => $title,
        ]);
    }

    /**
     * A list of registration rules (e.g. for an edit control).
     *
     * @return string[]
     */
    private function registrationTextOptions(): array
    {
        return [
            0 => I18N::translate('No predefined text'),
            1 => I18N::translate('Predefined text that states all users can request a user account'),
            2 => I18N::translate('Predefined text that states admin will decide on each request for a user account'),
            3 => I18N::translate('Predefined text that states only family members can request a user account'),
            4 => I18N::translate('Choose user defined welcome text typed below'),
        ];
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function registrationSave(ServerRequestInterface $request): ResponseInterface
    {
        $locale = $request->getAttribute('locale');
        assert($locale instanceof LocaleInterface);

        $params = $request->getParsedBody();

        Site::setPreference('WELCOME_TEXT_AUTH_MODE', $params['WELCOME_TEXT_AUTH_MODE']);
        Site::setPreference('WELCOME_TEXT_AUTH_MODE_' . $locale->languageTag(), $params['WELCOME_TEXT_AUTH_MODE_4']);
        Site::setPreference('USE_REGISTRATION_MODULE', $params['USE_REGISTRATION_MODULE']);
        Site::setPreference('SHOW_REGISTER_CAUTION', $params['SHOW_REGISTER_CAUTION']);

        FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');
        $url = route(ControlPanel::class);

        return redirect($url);
    }
}
