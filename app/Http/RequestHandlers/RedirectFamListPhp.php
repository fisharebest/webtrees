<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Http\Exceptions\HttpGoneException;
use Fisharebest\Webtrees\Module\FamilyListModule;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Redirect URLs created by webtrees 1.x (and PhpGedView).
 */
class RedirectFamListPhp implements RequestHandlerInterface
{
    public function __construct(
        private readonly ModuleService $module_service,
        private readonly TreeService $tree_service,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $ged                 = Validator::queryParams($request)->string('ged', Site::getPreference('DEFAULT_GEDCOM'));
        $alpha               = Validator::queryParams($request)->string('alpha', '');
        $falpha              = Validator::queryParams($request)->string('falpha', '');
        $show                = Validator::queryParams($request)->string('show', '');
        $show_all            = Validator::queryParams($request)->string('show_all', '');
        $show_all_firstnames = Validator::queryParams($request)->string('show_all_firstnames', '');
        $show_marnm          = Validator::queryParams($request)->string('show_marnm', '');
        $surname             = Validator::queryParams($request)->string('', '');
        $tree                = $this->tree_service->all()->get($ged);
        $module              = $this->module_service->findByInterface(FamilyListModule::class)->first();

        if ($tree instanceof Tree && $module instanceof FamilyListModule) {
            $url = $module->listUrl($tree, [
                'alpha'               => $alpha,
                'falpha'              => $falpha,
                'show'                => $show,
                'show_all'            => $show_all,
                'show_all_firstnames' => $show_all_firstnames,
                'show_marnm'          => $show_marnm,
                'surname'             => $surname,
            ]);

            return Registry::responseFactory()
                ->redirectUrl($url, StatusCodeInterface::STATUS_MOVED_PERMANENTLY)
                ->withHeader('Link', '<' . $url . '>; rel="canonical"');
        }

        throw new HttpGoneException();
    }
}
