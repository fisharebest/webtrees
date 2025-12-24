<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Http\Exceptions\HttpGoneException;
use Fisharebest\Webtrees\Module\AncestorsChartModule;
use Fisharebest\Webtrees\Module\ModuleChartInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RedirectAncestryPhp implements RequestHandlerInterface
{
    private const array CHART_STYLES = [
        0 => AncestorsChartModule::CHART_STYLE_TREE,
        1 => AncestorsChartModule::CHART_STYLE_TREE,
        2 => AncestorsChartModule::CHART_STYLE_INDIVIDUALS,
        3 => AncestorsChartModule::CHART_STYLE_FAMILIES,
    ];

    public function __construct(
        private readonly ModuleService $module_service,
        private readonly TreeService $tree_service,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $ged  = Validator::queryParams($request)->string('ged', Site::getPreference('DEFAULT_GEDCOM'));
        $tree = $this->tree_service->all()->get($ged);

        if ($tree instanceof Tree) {
            $module = $this->module_service
                ->findByComponent(ModuleChartInterface::class, $tree, Auth::user())
                ->first(static fn (ModuleChartInterface $module): bool => $module instanceof AncestorsChartModule);

            if ($module instanceof AncestorsChartModule) {
                $xref        = Validator::queryParams($request)->string('rootid', '');
                $generations = Validator::queryParams($request)->string('PEDIGREE_GENERATIONS', AncestorsChartModule::DEFAULT_GENERATIONS);
                $chart_style = Validator::queryParams($request)->string('chart_style', '');
                $individual  = Registry::individualFactory()->make($xref, $tree) ?? $tree->significantIndividual(Auth::user());

                $url = $module->chartUrl($individual, [
                    'generations' => $generations,
                    'style'       => self::CHART_STYLES[$chart_style] ?? AncestorsChartModule::DEFAULT_STYLE,
                ]);

                return Registry::responseFactory()
                    ->redirectUrl($url, StatusCodeInterface::STATUS_MOVED_PERMANENTLY)
                    ->withHeader('Link', '<' . $url . '>; rel="canonical"');
            }
        }

        throw new HttpGoneException();
    }
}
