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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\I18N;

use function strip_tags;
use function view;

/**
 * Class MapLinkBing - show locations in external maps
 */
class MapLinkBing extends AbstractModule implements ModuleMapLinkInterface
{
    use ModuleMapLinkTrait;

    /**
     * Name of the map provider.
     *
     * @return string
     */
    protected function providerName(): string
    {
        return I18N::translate('Bing™ maps');
    }

    /**
     * @return string
     */
    protected function icon(): string
    {
        return view('icons/bing-maps');
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
        $center    = $latitude . '~' . $longitude;
        $label     = strip_tags($fact->record()->fullName()) . ' — ' . $fact->label();
        $pointer   = $latitude . '_' . $longitude . '_' . rawurlencode($label);

        return 'https://www.bing.com/maps/?v=2&cp=' . $center . '&lvl=10&dir=0&sty=o&sp=point.' . $pointer;
    }
}
