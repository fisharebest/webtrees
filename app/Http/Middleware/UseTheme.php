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

namespace Fisharebest\Webtrees\Http\Middleware;

use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Module\WebtreesTheme;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Site;
use Generator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function app;

/**
 * Middleware to select a theme.
 */
class UseTheme implements MiddlewareInterface
{
    private ModuleService $module_service;

    /**
     * @param ModuleService $module_service
     */
    public function __construct(ModuleService $module_service)
    {
        $this->module_service = $module_service;
    }

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        foreach ($this->themes() as $theme) {
            if ($theme instanceof ModuleThemeInterface) {
                app()->instance(ModuleThemeInterface::class, $theme);
                $request = $request->withAttribute('theme', $theme);
                Session::put('theme', $theme->name());
                break;
            }
        }

        return $handler->handle($request);
    }

    /**
     * The theme can be chosen in various ways.
     *
     * @return Generator<ModuleThemeInterface|null>
     */
    private function themes(): Generator
    {
        $themes = $this->module_service->findByInterface(ModuleThemeInterface::class);

        // Last theme used
        yield $themes
            ->first(static fn (ModuleThemeInterface $module): bool => $module->name() === Session::get('theme'));

        // Default for site
        yield $themes
            ->first(static fn (ModuleThemeInterface $module): bool => $module->name() === Site::getPreference('THEME_DIR'));

        // Default for application
        yield new WebtreesTheme();
    }
}
