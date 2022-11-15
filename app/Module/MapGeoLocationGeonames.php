<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function redirect;

/**
 * Class MapLocationGeonames - use geonames to find locations
 */
class MapGeoLocationGeonames extends AbstractModule implements ModuleConfigInterface, ModuleMapGeoLocationInterface
{
    use ModuleConfigTrait;
    use ModuleMapGeoLocationTrait;

    /**
     * Name of the map provider.
     *
     * @return string
     */
    public function title(): string
    {
        return /* I18N: https://www.geonames.org */ I18N::translate('GeoNames');
    }

    /**
     * Should this module be enabled when it is first installed?
     *
     * @return bool
     */
    public function isEnabledByDefault(): bool
    {
        return false;
    }

    /**
     * @return ResponseInterface
     */
    public function getAdminAction(): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        // This was a global setting before it became a module setting...
        $default  = Site::getPreference('geonames');
        $username = $this->getPreference('username', $default);

        return $this->viewResponse('modules/geonames/config', [
            'username' => $username,
            'title'    => $this->title(),
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postAdminAction(ServerRequestInterface $request): ResponseInterface
    {
        $api_key = Validator::parsedBody($request)->string('username');

        $this->setPreference('username', $api_key);

        FlashMessages::addMessage(I18N::translate('The preferences for the module “%s” have been updated.', $this->title()), 'success');

        return redirect($this->getConfigLink());
    }
}
