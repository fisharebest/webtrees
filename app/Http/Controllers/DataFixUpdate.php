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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Module\ModuleDataFixInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function assert;
use function response;

final class DataFixUpdate
{
    public function __construct(
        private ModuleService $module_service,
    ) {
    }

    public function post(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $data_fix = Validator::attributes($request)->string('data_fix', '');
        $module   = $this->module_service->findByName($data_fix);
        assert($module instanceof ModuleDataFixInterface);

        $xref   = Validator::queryParams($request)->isXref()->string('xref');
        $params = $request->getQueryParams();

        $record = Registry::gedcomRecordFactory()->make($xref, $tree);
        $record = Auth::checkRecordAccess($record, true);

        if (!$record->isPendingDeletion() && $module->doesRecordNeedUpdate($record, $params)) {
            $module->updateRecord($record, $params);
        }

        return response();
    }
}
