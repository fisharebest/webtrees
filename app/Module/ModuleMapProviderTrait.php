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
     *  Check if Module contains the functions for a config page,
     *  If so then an api key is required so check if it is empty
     *
     * @return bool
     * @throws HttpServerErrorException
     */
    public function hasApiKey(): bool
    {
        $error = in_array("getAdminAction", get_class_methods($this)) && $this->getPreference('api_key') === '';
        if ($error && Auth::isAdmin()) {
            $message = I18N::translate('<a href="%s">The %s service requires an API key.', e($this->getConfigLink()), $this->title());

            throw new HttpServerErrorException($message);
        }

        return !$error;
    }
}
