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
use Fisharebest\Webtrees\Services\EmailService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Site;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

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

    /** @var EmailService */
    private $email_service;

    /** @var ModuleService */
    private $module_service;

    /**
     * AdminSiteController constructor.
     *
     * @param EmailService  $email_service
     * @param ModuleService $module_service
     */
    public function __construct(EmailService $email_service, ModuleService $module_service)
    {
        $this->email_service  = $email_service;
        $this->module_service = $module_service;
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
