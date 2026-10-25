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
use Fisharebest\Webtrees\Module\FanChartModule;
use Fisharebest\Webtrees\Module\ModuleChartInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class RedirectFanChartPhp
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
                ->first(static fn (ModuleChartInterface $module): bool => $module instanceof FanChartModule);

            if ($module instanceof FanChartModule) {
                $root_id     = Validator::queryParams($request)->string('rootid', '');
                $generations = Validator::queryParams($request)->string('generations', '4');
                $style       = Validator::queryParams($request)->string('style', '4');
                $width       = Validator::queryParams($request)->integer('width', FanChartModule::DEFAULT_WIDTH);
                $individual  = Registry::individualFactory()->make($root_id, $tree) ?? $tree->significantIndividual($this->user);

                $url = $module->chartUrl($individual, [
                    'generations' => $generations,
                    'style'       => $style,
                    'width'       => $width,
                ]);

                return Registry::responseFactory()
                    ->redirectUrl($url, HttpStatusCode::MovedPermanently)
                    ->withHeader('Link', '<' . $url . '>; rel="canonical"');
            }
        }

        throw new HttpGoneException();
    }
}
