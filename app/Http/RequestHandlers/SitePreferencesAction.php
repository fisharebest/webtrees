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

use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Site;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function redirect;
use function route;

/**
 * Edit the site preferences.
 */
class SitePreferencesAction implements RequestHandlerInterface
{
    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $params = (array) $request->getParsedBody();

        $INDEX_DIRECTORY = $params['INDEX_DIRECTORY'];

        if (substr($INDEX_DIRECTORY, -1) !== '/') {
            $INDEX_DIRECTORY .= '/';
        }

        if (is_dir($INDEX_DIRECTORY)) {
            Site::setPreference('INDEX_DIRECTORY', $INDEX_DIRECTORY);
        } else {
            FlashMessages::addMessage(I18N::translate('The folder “%s” does not exist.', e($INDEX_DIRECTORY)), 'danger');
        }

        Site::setPreference('ALLOW_CHANGE_GEDCOM', $params['ALLOW_CHANGE_GEDCOM']);
        Site::setPreference('LANGUAGE', $params['LANGUAGE']);
        Site::setPreference('THEME_DIR', $params['THEME_DIR']);
        Site::setPreference('TIMEZONE', $params['TIMEZONE']);

        FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');
        $url = route(ControlPanel::class);

        return redirect($url);
    }
}
