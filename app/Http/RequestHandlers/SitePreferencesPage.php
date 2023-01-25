<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Services\ModuleService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function ini_get;

/**
 * Edit the site preferences.
 */
class SitePreferencesPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private ModuleService $module_service;

    /**
     * AdminSiteController constructor.
     *
     * @param ModuleService $module_service
     */
    public function __construct(ModuleService $module_service)
    {
        $this->module_service = $module_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        $all_themes = $this->module_service
            ->findByInterface(ModuleThemeInterface::class, true, true)
            ->map($this->module_service->titleMapper());

        $max_execution_time = (int) ini_get('max_execution_time');

        $title = I18N::translate('Website preferences');

        return $this->viewResponse('admin/site-preferences', [
            'all_themes'         => $all_themes,
            'max_execution_time' => $max_execution_time,
            'title'              => $title,
        ]);
    }
}
