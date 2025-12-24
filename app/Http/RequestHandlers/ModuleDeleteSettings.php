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

use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function redirect;
use function route;

final class ModuleDeleteSettings implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $module_name = Validator::parsedBody($request)->string('module_name');

        DB::table('block_setting')
            ->join('block', 'block_setting.block_id', '=', 'block.block_id')
            ->join('module', 'block.module_name', '=', 'module.module_name')
            ->where('module.module_name', '=', $module_name)
            ->delete();

        DB::table('block')
            ->join('module', 'block.module_name', '=', 'module.module_name')
            ->where('module.module_name', '=', $module_name)
            ->delete();

        DB::table('module_setting')
            ->where('module_name', '=', $module_name)
            ->delete();

        DB::table('module_privacy')
            ->where('module_name', '=', $module_name)
            ->delete();

        DB::table('module')
            ->where('module_name', '=', $module_name)
            ->delete();

        FlashMessages::addMessage(I18N::translate('The preferences for the module “%s” have been deleted.', $module_name), 'success');

        return redirect(route(ModulesAllPage::class));
    }
}
