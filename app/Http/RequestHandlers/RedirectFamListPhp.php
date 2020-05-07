<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function redirect;

/**
 * Redirect URLs created by webtrees 1.x (and PhpGedView).
 */
class RedirectFamListPhp implements RequestHandlerInterface
{
    /** @var TreeService */
    private $tree_service;

    /**
     * @param TreeService $tree_service
     */
    public function __construct(TreeService $tree_service)
    {
        $this->tree_service = $tree_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $query               = $request->getQueryParams();
        $ged                 = $query['ged'] ?? Site::getPreference('DEFAULT_GEDCOM');
        $alpha               = $query['alpha'] ?? null;
        $falpha              = $query['falpha'] ?? null;
        $show                = $query['show'] ?? null;
        $show_all            = $query['show_all'] ?? null;
        $show_all_firstnames = $query['show_all_firstnames'] ?? null;
        $show_marnm          = $query['show_marnm'] ?? null;
        $surname             = $query['surname'] ?? null;

        $tree = $this->tree_service->all()->get($ged);

        if ($tree instanceof Tree) {
            $url = route('module', [
                'module'              => 'family_list',
                'action'              => 'List',
                'alpha'               => $alpha,
                'falpha'              => $falpha,
                'show'                => $show,
                'show_all'            => $show_all,
                'show_all_firstnames' => $show_all_firstnames,
                'show_marnm'          => $show_marnm,
                'surname'             => $surname,
                'tree'                => $tree->name(),
            ]);

            return redirect($url, StatusCodeInterface::STATUS_MOVED_PERMANENTLY);
        }

        throw new HttpNotFoundException();
    }
}
