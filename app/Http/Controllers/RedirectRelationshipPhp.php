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

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Enums\HttpStatusCode;
use Fisharebest\Webtrees\Http\Exceptions\HttpGoneException;
use Fisharebest\Webtrees\Module\ModuleChartInterface;
use Fisharebest\Webtrees\Module\RelationshipsChartModule;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class RedirectRelationshipPhp
{
    public function __construct(
        private UserInterface $user,
        private ModuleService $module_service,
        private TreeService $tree_service,
    ) {
    }

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $ged  = Validator::queryParams($request)->string('ged', Site::getPreference('DEFAULT_GEDCOM'));
        $tree = $this->tree_service->all()->get($ged);

        if ($tree instanceof Tree) {
            $module = $this->module_service
                ->findByComponent(ModuleChartInterface::class, $tree, $this->user)
                ->first(static fn (ModuleChartInterface $module): bool => $module instanceof RelationshipsChartModule);

            if ($module instanceof RelationshipsChartModule) {
                $pid1       = Validator::queryParams($request)->string('pid1', '');
                $individual = Registry::individualFactory()->make($pid1, $tree) ?? $tree->significantIndividual($this->user);

                $url = $module->chartUrl($individual, [
                    'ancestors' => Validator::queryParams($request)->string('ancestors', '0'),
                    'recursion' => Validator::queryParams($request)->string('recursion', '0'),
                    'xref1'     => $pid1,
                    'xref2'     => Validator::queryParams($request)->string('pid2', ''),
                ]);

                return Registry::responseFactory()
                    ->redirectUrl($url, HttpStatusCode::MovedPermanently)
                    ->withHeader('Link', '<' . $url . '>; rel="canonical"');
            }
        }

        throw new HttpGoneException();
    }
}
