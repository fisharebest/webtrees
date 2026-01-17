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

use Fisharebest\Webtrees\Module\ModuleDataFixInterface;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function redirect;
use function route;

final class DataFixSelect implements RequestHandlerInterface
{
    public function __construct(
        private readonly ModuleService $module_service,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree       = Validator::attributes($request)->tree();
        $data_fixes = $this->module_service->findByInterface(ModuleDataFixInterface::class);
        $data_fix   = Validator::parsedBody($request)->string('data_fix');
        $module     = $data_fixes->get($data_fix);

        if ($module instanceof ModuleDataFixInterface) {
            return redirect(route(DataFixPage::class, ['tree' => $tree->name(), 'data_fix' => $module->name()]));
        }

        return redirect(route(DataFixPage::class, ['tree' => $tree->name()]));
    }
}
