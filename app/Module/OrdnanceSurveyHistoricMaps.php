<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

use Fisharebest\Webtrees\I18N;

/**
 * Class OrdnanceSurvey - use maps within webtrees
 */
class OrdnanceSurveyHistoricMaps extends AbstractModule implements ModuleMapProviderInterface
{
    use ModuleMapProviderTrait;

    /**
     * Name of the map provider.
     *
     * @return string
     */
    public function title(): string
    {
        // I18N: Ordnance Survey is the UK government mapping service.
        return I18N::translate('Ordnance Survey historic maps');
    }

    /**
     * Name of the map provider.
     *
     * @return string
     */
    public function description(): string
    {
        $link = '<a href="https://maps.nls.uk/projects/api" dir="ltr">maps.nls.uk/projects/api</a>';

        // I18N: %s is a link/URL
        return I18N::translate('Create maps using %s.', $link);
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
     * Parameters to create a TileLayer in LeafletJs.
     *
     * @return array<object>
     */
    public function leafletJsTileLayers(): array
    {
        return [
            (object) [
                'attribution' => 'Historical Maps Layer, 1919-1947 from the <a href="https://maps.nls.uk/projects/api">NLS Maps API</a>',
                'bounds'      => [[49.852539, -7.793077], [60.894042, 1.790425]],
                'default'     => true,
                'label'       => 'Historic map of Great Britain',
                'maxZoom'     => 17,
                'minZoom'     => 2,
                'subdomains'  => ['0', '1', '2', '3'],
                'url'         => 'https://nls-{s}.tileserver.com/nls/{z}/{x}/{y}.jpg',
            ],
        ];
    }
}
