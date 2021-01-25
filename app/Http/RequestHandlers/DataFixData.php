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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\ModuleDataFixInterface;
use Fisharebest\Webtrees\Services\DataFixService;
use Fisharebest\Webtrees\Services\DatatablesService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use stdClass;

use function assert;
use function e;
use function route;
use function view;

/**
 * Run a data-fix.
 */
class DataFixData implements RequestHandlerInterface
{
    /** @var DataFixService */
    private $data_fix_service;

    /** @var DatatablesService */
    private $datatables_service;

    /** @var ModuleService */
    private $module_service;

    /**
     * DataFix constructor.
     *
     * @param DataFixService    $data_fix_service
     * @param DatatablesService $datatables_service
     * @param ModuleService     $module_service
     */
    public function __construct(
        DataFixService $data_fix_service,
        DatatablesService $datatables_service,
        ModuleService $module_service
    ) {
        $this->data_fix_service   = $data_fix_service;
        $this->module_service     = $module_service;
        $this->datatables_service = $datatables_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $data_fix = $request->getAttribute('data_fix') ?? '';
        $module   = $this->module_service->findByName($data_fix);
        assert($module instanceof ModuleDataFixInterface);

        $params  = $request->getQueryParams();
        $records = $module->recordsToFix($tree, $params);

        $callback = function (stdClass $row) use ($module, $params, $tree): array {
            $record = $this->data_fix_service->getRecordByType($row->xref, $tree, $row->type);
            assert($record instanceof GedcomRecord);

            $col1 = '<a href="' . e($record->url()) . '">' . $record->fullName() . '</a>';

            if ($module->doesRecordNeedUpdate($record, $params)) {
                $preview_url = route(DataFixPreview::class, [
                        'tree'     => $tree->name(),
                        'data_fix' => $module->name(),
                        'action'   => 'update',
                        'xref'     => $row->xref,
                    ] + $params);
                $update_url  = route(DataFixUpdate::class, [
                        'tree'     => $tree->name(),
                        'data_fix' => $module->name(),
                        'action'   => 'update',
                        'xref'     => $row->xref,
                    ] + $params);
                // wt-ajax-modal-title
                $col2 = '<button type="button" class="btn btn-primary" data-toggle="modal" data-backdrop="static" data-target="#wt-ajax-modal" data-href="' . $preview_url . '">' . view('icons/search') . I18N::translate('Preview') . '</button>';
                $col2 .= ' <button type="button" class="btn btn-primary" data-update-url="' . $update_url . '">' . view('icons/data-fix') . I18N::translate('Update') . '</button>';
            } else {
                $col2 = '—';
            }

            return [$col1, $col2];
        };

        return $this->datatables_service->handleCollection($request, $records, [], [], $callback);
    }
}
