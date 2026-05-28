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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\I18N;

use function e;

/**
 * Trait ModuleMapLinkTrait - default implementation of ModuleMapLinkInterface
 */
trait ModuleMapLinkTrait
{
    public function title(): string
    {
        return $this->providerName() . ' — ' . I18N::translate('Map link');
    }

    public function description(): string
    {
        return I18N::translate('Show the location of an event on an external map.');
    }

    public function mapLink(Fact $fact): string
    {
        if ($this->isMapAvailableForLocation($fact)) {
            $icon  = $this->icon();
            $url   = $this->mapUrl($fact);
            $title = I18N::translate('View this location using %s', $this->providerName());

            return '<a href="' . e($url) . '" rel="nofollow" target="_top" title="' . $title . '">' . $icon . '</a>';
        }

        return '';
    }

    /**
     * Name of the map provider.
     */
    protected function providerName(): string
    {
        return 'example.com';
    }

    protected function isMapAvailableForLocation(Fact $fact): bool
    {
        return $fact->latitude() !== null && $fact->longitude() !== null;
    }

    protected function icon(): string
    {
        return 'icon';
    }

    protected function mapUrl(Fact $fact): string
    {
        return 'https://example.com';
    }
}
