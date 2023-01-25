<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\AdminService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function e;

/**
 * Renumber the XREFs in a family tree.
 */
class RenumberTreePage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private AdminService $admin_service;

    /**
     * @param AdminService $admin_service
     */
    public function __construct(AdminService $admin_service)
    {
        $this->admin_service = $admin_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        $tree  = Validator::attributes($request)->tree();
        $xrefs = $this->admin_service->duplicateXrefs($tree);

        /* I18N: Renumber the records in a family tree */
        $title = I18N::translate('Renumber XREFs') . ' — ' . e($tree->title());

        return $this->viewResponse('admin/trees-renumber', [
            'title' => $title,
            'tree'  => $tree,
            'xrefs' => $xrefs,
        ]);
    }
}
