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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Http\Exceptions\HttpAccessDeniedException;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Validator;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function is_string;
use function method_exists;
use function str_contains;
use function strtolower;

final class ModuleAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly ModuleService $module_service,
    ) {
    }

    /**
     * Perform an HTTP action for one of the modules.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $module_name = $request->getAttribute('module');
        $action      = $request->getAttribute('action');
        $user        = Validator::attributes($request)->user();

        if (!is_string($module_name)) {
            throw new InvalidArgumentException('Invalid module_name');
        }

        if (!is_string($action)) {
            throw new InvalidArgumentException('Invalid action');
        }

        // Check that the module is enabled.
        // The module itself will need to check any tree-level access,
        // which may be different for each component (tab, menu, etc.) of the module.
        $module = $this->module_service->findByName($module_name);

        if ($module === null) {
            throw new HttpNotFoundException('Module ' . e($module_name) . ' does not exist');
        }

        // We'll call a function such as Module::getFooBarAction()
        $verb   = strtolower($request->getMethod());
        $method = $verb . $action . 'Action';

        // Actions with "Admin" in the name are for administrators only.
        if (str_contains($action, 'Admin') && !Auth::isAdmin($user)) {
            throw new HttpAccessDeniedException('Admin only action');
        }

        if (!method_exists($module, $method)) {
            throw new HttpNotFoundException('Method ' . e($method) . '() not found in ' . e($module_name));
        }

        return $module->$method($request);
    }
}
