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

use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\I18N;

use function view;

/**
 * Class MapLinkOpenStreetMap - show locations in external maps
 */
class MapLinkOpenStreetMap extends AbstractModule implements ModuleMapLinkInterface
{
    use ModuleMapLinkTrait;

    /**
     * Name of the map provider.
     *
     * @return string
     */
    protected function providerName(): string
    {
        return I18N::translate('OpenStreetMap™');
    }

    /**
     * @return string
     */
    protected function icon(): string
    {
        return view('icons/openstreetmap');
    }

    /**
     * @param Fact $fact
     *
     * @return string
     */
    protected function mapUrl(Fact $fact): string
    {
        $latitude  = $fact->latitude();
        $longitude = $fact->longitude();

        // mlat/mlon is the marker postion
        return 'https://www.openstreetmap.org/?mlat=' . $latitude . '&mlon=' . $longitude . '#map=10/' . $latitude . '/' . $longitude;
    }
}
