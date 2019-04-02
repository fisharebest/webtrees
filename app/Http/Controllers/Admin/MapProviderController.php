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

namespace Fisharebest\Webtrees\Http\Controllers\Admin;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Site;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Controller for maps and geographic data.
 */
class MapProviderController extends AbstractAdminController
{
    /**
     * @return ResponseInterface
     */
    public function mapProviderEdit(): ResponseInterface
    {
        return $this->viewResponse('admin/map-provider', [
            'title'    => I18N::translate('Map provider'),
            'provider' => Site::getPreference('map-provider'),
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function mapProviderSave(ServerRequestInterface $request): ResponseInterface
    {
        $map_provider = $request->getParsedBody()['provider'];

        Site::setPreference('map-provider', $map_provider);

        return redirect(route('admin-control-panel'));
    }
}
