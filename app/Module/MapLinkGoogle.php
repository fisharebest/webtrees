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

use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\I18N;

use function view;

/**
 * Class MapLinkGoogle - show locations in external maps
 */
class MapLinkGoogle extends AbstractModule implements ModuleMapLinkInterface
{
    use ModuleMapLinkTrait;

    /**
     * Name of the map provider.
     *
     * @return string
     */
    protected function providerName(): string
    {
        return I18N::translate('Google™ maps');
    }

    /**
     * @return string
     */
    protected function icon(): string
    {
        return view('icons/google-maps');
    }

    /**
     * @param Fact $fact
     *
     * @return string
     */
    protected function mapUrl(Fact $fact): string
    {
        // This URL allows us to add a pin at the location.
        // Other URLs allow us to set the zoom.
        // Is there one that does both?
        return 'https://maps.google.com/maps?q=loc:' . $fact->latitude() . '+' . $fact->longitude();
    }
}
