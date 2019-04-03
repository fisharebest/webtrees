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

namespace Fisharebest\Webtrees\Http\Middleware;

use Closure;
use Fisharebest\Webtrees\Module\ModuleInterface;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Services\ModuleService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use function method_exists;

/**
 * Middleware to bootstrap the modules.
 */
class BootModules implements MiddlewareInterface
{
    /**
     * @param Request $request
     * @param Closure $next
     *
     * @return Response
     * @throws Throwable
     */
    public function handle(Request $request, Closure $next): Response
    {
        $module_service = app(ModuleService::class);
        $theme          = app(ModuleThemeInterface::class);

        $bootable_modules = $module_service->all()->filter(static function (ModuleInterface $module) {
            return method_exists($module, 'boot');
        });

        foreach ($bootable_modules as $module) {
            // Only bootstrap the current theme.
            if ($module instanceof ModuleThemeInterface && $module !== $theme) {
                continue;
            }

            app()->dispatch($module, 'boot');
        }

        return $next($request);
    }
}
