<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Http\Exceptions\HttpServerErrorException;
use Fisharebest\Webtrees\I18N;

use function e;

/**
 * Trait ModuleMapProviderTrait - default implementation of ModuleMapProviderInterface
 */
trait ModuleMapProviderTrait
{
    use ModuleConfigTrait;

    /**
     * Parameters to create a TileLayer in LeafletJs.
     *
     * @return array<object>
     */
    public function leafletJsTileLayers(): array
    {
        return [];
    }

    /**
     *  If module requires an api key then return false if not valid
     *
     * @return bool
     * @throws HttpServerErrorException
     */
    public function hasApiKey(): bool
    {
        $api_key = $this->getPreference('api_key', 'not-needed');
        if ($api_key !== 'not-needed' && $api_key === '' && Auth::isAdmin()) {
            $message = I18N::translate('<a href="%s">The %s service requires an API key.', e($this->getConfigLink()), $this->title());
            throw new HttpServerErrorException($message);
        }
        return $api_key !== '';
    }
}
