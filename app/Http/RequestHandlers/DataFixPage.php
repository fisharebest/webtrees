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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\ModuleDataFixInterface;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function e;
use function redirect;
use function route;
use function view;

/**
 * Run a data-fix.
 */
class DataFixPage implements RequestHandlerInterface
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

        $data_fixes = $this->module_service->findByInterface(ModuleDataFixInterface::class, false, true);

        if ($data_fixes->isEmpty()) {
            return redirect(route('control-panel'));
        }

        $data_fix = $request->getAttribute('data_fix') ?? '';
        $module   = $this->module_service->findByName($data_fix);

        $this->layout = 'layouts/administration';

        if ($module instanceof ModuleDataFixInterface) {
            $title       = $module->title() . ' — ' . e($tree->title());
            $page_url    = route(self::class, ['data_fix' => $data_fix, 'tree' => $tree->name()]);
            $pending_url = route(PendingChanges::class, ['tree' => $tree->name(), 'url' => $page_url]);

            return $this->viewResponse('admin/data-fix-page', [
                'data_fix'    => $module,
                'title'       => $title,
                'tree'        => $tree,
                'pending_url' => $pending_url,
            ]);
        }

        $title = view('icons/data-fix') . I18N::translate('Data fixes') . ' — ' . e($tree->title());

        return $this->viewResponse('admin/data-fix-select', [
            'title'      => $title,
            'data_fixes' => $data_fixes,
            'tree'       => $tree,
        ]);
    }
}
