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
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Module\InteractiveTreeModule;
use Fisharebest\Webtrees\Module\PedigreeMapModule;
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
class RedirectModulePhp implements RequestHandlerInterface
{
    private ModuleService $module_service;

    private TreeService $tree_service;

    /**
     * @param ModuleService $module_service
     * @param TreeService   $tree_service
     */
    public function __construct(ModuleService $module_service, TreeService $tree_service)
    {
        $this->tree_service   = $tree_service;
        $this->module_service = $module_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $ged        = Validator::queryParams($request)->string('ged', Site::getPreference('DEFAULT_GEDCOM'));
        $mod        = Validator::queryParams($request)->string('mod', '');
        $mod_action = Validator::queryParams($request)->string('mod_action', '');
        $rootid     = Validator::queryParams($request)->string('rootid', '');
        $tree       = $this->tree_service->all()->get($ged);

        if ($tree instanceof Tree) {
            $individual = Registry::individualFactory()->make($rootid, $tree);

            if ($individual instanceof Individual) {
                switch ($mod . '/' . $mod_action) {
                    case 'googlemap/pedigree_map':
                        $module = $this->module_service->findByInterface(PedigreeMapModule::class)->first();

                        if ($module instanceof PedigreeMapModule) {
                            $generations = Validator::queryParams($request)->string('PEDIGREE_GENERATIONS', PedigreeMapModule::DEFAULT_GENERATIONS);
                            $url         = $module->chartUrl($individual, ['generations' => $generations]);

                            return Registry::responseFactory()->redirectUrl($url, StatusCodeInterface::STATUS_MOVED_PERMANENTLY);
                        }

                        break;

                    case 'tree/treeview':
                        $module = $this->module_service->findByInterface(InteractiveTreeModule::class)->first();

                        if ($module instanceof InteractiveTreeModule) {
                            $url = $module->chartUrl($individual, []);

                            return Registry::responseFactory()->redirectUrl($url, StatusCodeInterface::STATUS_MOVED_PERMANENTLY);
                        }

                        break;
                }
            }
        }

        throw new HttpNotFoundException();
    }
}
