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

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Database\Query\JoinOn;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function redirect;
use function route;

final class ModuleDeleteSettings
{
    public function post(ServerRequestInterface $request): ResponseInterface
    {
        $module_name = Validator::parsedBody($request)->string('module_name');

        DB::query(table: 'block_setting')
            ->whereInSubquery('block_id', DB::query()
                ->select('block.block_id')
                ->from('block')
                ->join('module', static fn (JoinOn $j): JoinOn => $j->on('module.module_name', 'block.module_name'))
                ->whereEqual('module.module_name', $module_name))
            ->delete();

        DB::query(table: 'block')
            ->whereInSubquery('module_name', DB::query()
                ->select('module_name')
                ->from('module')
                ->whereEqual('module_name', $module_name))
            ->delete();

        DB::query(table: 'module_setting')
            ->whereEqual('module_name', $module_name)
            ->delete();

        DB::query(table: 'module_privacy')
            ->whereEqual('module_name', $module_name)
            ->delete();

        DB::query(table: 'module')
            ->whereEqual('module_name', $module_name)
            ->delete();

        FlashMessages::addMessage(I18N::translate('The preferences for the module “%s” have been deleted.', $module_name), 'success');

        return redirect(route(ModulesAll::class));
    }
}
