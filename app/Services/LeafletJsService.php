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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Http\Exceptions\HttpServiceUnavailableException;
use Fisharebest\Webtrees\Http\RequestHandlers\ModulesMapProvidersPage;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\ModuleMapProviderInterface;

/**
 * Generate the configuration data needed to create a LeafletJs map.
 */
class LeafletJsService
{
    private ModuleService $module_service;

    /**
     * LeafletJsService constructor.
     *
     * @param ModuleService $module_service
     */
    public function __construct(ModuleService $module_service)
    {
        $this->module_service = $module_service;
    }

    /**
     * @return object
     */
    public function config(): object
    {
        $default = 'openstreetmap';

        $map_providers = $this->module_service
            ->findByInterface(ModuleMapProviderInterface::class)
            ->map(static function (ModuleMapProviderInterface $map_provider) use ($default): object {
                return (object) [
                    'children'  => $map_provider->leafletJsTileLayers(),
                    'collapsed' => true,
                    'default'   => $map_provider->name() === $default,
                    'label'     => $map_provider->title(),
                ];
            })
            ->values();

        if ($map_providers->isEmpty()) {
            $message = I18N::translate('To display a map, you need to enable a map-provider in the control panel.');

            if (Auth::isAdmin()) {
                $url = route(ModulesMapProvidersPage::class);
                $message .= ' — <a class="alert-link" href="' . e($url) . '">' . I18N::translate('Map providers') . '</a>';
            }

            throw new HttpServiceUnavailableException($message);
        }

        $enter_fullscreen_icon = '<span title="' . I18N::translate('Enter fullscreen') . '">' . view('icons/enter-fullscreen') . '</span>';
        $exit_fullscreen_icon  = '<span title="' . I18N::translate('Exit fullscreen') . '">' . view('icons/exit-fullscreen') . '</span>';

        return (object) [
            'i18n'         => [
                'reset'   => I18N::translate('Reload map'),
                'zoomIn'  => I18N::translate('Zoom in'),
                'zoomOut' => I18N::translate('Zoom out'),
            ],
            'icons'        => [
                'collapse'   => view('icons/collapse'),
                'expand'     => view('icons/expand'),
                'reset'      => view('icons/undo'),
                'fullScreen' => $enter_fullscreen_icon . $exit_fullscreen_icon,
            ],
            'mapProviders' => $map_providers,
        ];
    }
}
