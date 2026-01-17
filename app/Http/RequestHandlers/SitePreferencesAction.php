<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function e;
use function is_writable;
use function redirect;
use function route;

final class SitePreferencesAction implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $index_directory     = Validator::parsedBody($request)->string('INDEX_DIRECTORY');
        $allow_change_gedcom = Validator::parsedBody($request)->boolean('ALLOW_CHANGE_GEDCOM');
        $language            = Validator::parsedBody($request)->string('LANGUAGE');
        $theme_dir           = Validator::parsedBody($request)->string('THEME_DIR');
        $timezone            = Validator::parsedBody($request)->string('TIMEZONE');

        if (!str_ends_with($index_directory, '/')) {
            $index_directory .= '/';
        }

        if (is_dir($index_directory)) {
            if (is_writable($index_directory)) {
                Site::setPreference('INDEX_DIRECTORY', $index_directory);
            } else {
                FlashMessages::addMessage(I18N::translate('Cannot write to the folder “%s”.', e($index_directory)), 'danger');
            }
        } else {
            FlashMessages::addMessage(I18N::translate('The folder “%s” does not exist.', e($index_directory)), 'danger');
        }

        Site::setPreference('ALLOW_CHANGE_GEDCOM', (string) $allow_change_gedcom);
        Site::setPreference('LANGUAGE', $language);
        Site::setPreference('THEME_DIR', $theme_dir);
        Site::setPreference('TIMEZONE', $timezone);

        FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');
        $url = route(ControlPanel::class);

        return redirect($url);
    }
}
