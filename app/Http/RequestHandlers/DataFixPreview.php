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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\ModuleDataFixInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;

/**
 * Run a data-fix.
 */
class DataFixPreview implements RequestHandlerInterface
{
    use ViewResponseTrait;

    /** @var ModuleService */
    private $module_service;

    /**
     * DataFix constructor.
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
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $data_fix = $request->getAttribute('data_fix', '');
        $module   = $this->module_service->findByName($data_fix);
        assert($module instanceof ModuleDataFixInterface);

        $params = $request->getQueryParams();
        $xref   = $params['xref'] ?? '';
        $record = Registry::gedcomRecordFactory()->make($xref, $tree);
        $record = Auth::checkRecordAccess($record);

        $this->layout = 'layouts/ajax';

        if (!$record->isPendingDeletion() && $module->doesRecordNeedUpdate($record, $params)) {
            $preview = $module->previewUpdate($record, $params);
        } else {
            $preview = '';
        }

        return $this->viewResponse('modals/data-fix-preview', [
            'preview' => $preview,
            'title'   => I18N::translate('Preview') . ' — ' . $record->fullName(),
        ]);
    }
}
