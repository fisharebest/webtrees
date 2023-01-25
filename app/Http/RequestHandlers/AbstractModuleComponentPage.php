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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\Module\ModuleInterface;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Show a list of modules.
 */
abstract class AbstractModuleComponentPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private ModuleService $module_service;

    private TreeService $tree_service;

    /**
     * @param ModuleService $module_service
     * @param TreeService   $tree_service
     */
    public function __construct(ModuleService $module_service, TreeService $tree_service)
    {
        $this->module_service = $module_service;
        $this->tree_service   = $tree_service;
    }

    /**
     * @param string $interface
     * @param string $title
     * @param string $description
     *
     * @return ResponseInterface
     */
    protected function listComponents(string $interface, string $title, string $description): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        $modules      = $this->module_service->findByInterface($interface, true, true);
        $uses_access  = $this->module_service->componentsWithAccess()->containsStrict($interface);
        $uses_sorting = $this->module_service->componentsWithOrder()->containsStrict($interface);

        $access_summary = $modules
            ->mapWithKeys(function (ModuleInterface $module) use ($interface): array {
                $access_levels = $this->tree_service->all()
                    ->map(static function (Tree $tree) use ($interface, $module): int {
                        return $module->accessLevel($tree, $interface);
                    })
                    ->uniqueStrict()
                    ->values()
                    ->map(static function (int $level): string {
                        return Auth::accessLevelNames()[$level];
                    })
                    ->all();

                return [$module->name() => $access_levels];
            })
            ->all();

        return $this->viewResponse('admin/components', [
            'description'    => $description,
            'interface'      => $interface,
            'modules'        => $modules,
            'title'          => $title,
            'trees'          => $this->tree_service->all(),
            'uses_access'    => $uses_access,
            'uses_sorting'   => $uses_sorting,
            'access_summary' => $access_summary,
        ]);
    }
}
