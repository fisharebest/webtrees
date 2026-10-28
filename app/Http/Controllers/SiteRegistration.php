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

use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function redirect;
use function route;

final class SiteRegistration
{
    use ViewResponseTrait;

    public function get(): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        $title = I18N::translate('Sign-in and registration');

        $registration_text_options = [
            0 => I18N::translate('No predefined text'),
            1 => I18N::translate('Predefined text that states all users can request a user account'),
            2 => I18N::translate('Predefined text that states admin will decide on each request for a user account'),
            3 => I18N::translate('Predefined text that states only family members can request a user account'),
            4 => I18N::translate('Choose user defined welcome text typed below'),
        ];

        return $this->viewResponse('admin/site-registration', [
            'language_tag'              => I18N::languageTag(),
            'registration_text_options' => $registration_text_options,
            'title'                     => $title,
        ]);
    }

    public function post(ServerRequestInterface $request): ResponseInterface
    {
        $mode               = Validator::parsedBody($request)->string('WELCOME_TEXT_AUTH_MODE');
        $text               = Validator::parsedBody($request)->string('WELCOME_TEXT_AUTH_MODE_4');
        $allow_registration = Validator::parsedBody($request)->boolean('USE_REGISTRATION_MODULE');
        $show_caution       = Validator::parsedBody($request)->boolean('SHOW_REGISTER_CAUTION');

        Site::setPreference('WELCOME_TEXT_AUTH_MODE', $mode);
        Site::setPreference('WELCOME_TEXT_AUTH_MODE_' . I18N::languageTag(), $text);
        Site::setPreference('USE_REGISTRATION_MODULE', (string) $allow_registration);
        Site::setPreference('SHOW_REGISTER_CAUTION', (string) $show_caution);

        FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');

        return redirect(route(ControlPanel::class));
    }
}
