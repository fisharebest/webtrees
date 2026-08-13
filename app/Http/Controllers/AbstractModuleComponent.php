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

use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Enums\AccessLevel;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\ModuleInterface;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function array_flip;

abstract class AbstractModuleComponent
{
    use ViewResponseTrait;

    public function __construct(
        protected readonly ModuleService $module_service,
        protected readonly TreeService $tree_service,
    ) {
    }

    /**
     * @template T of ModuleInterface
     *
     * @param class-string<T> $interface
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
                    ->map(static fn (Tree $tree): AccessLevel => $module->accessLevel($tree, $interface))
                    ->uniqueStrict()
                    ->values()
                    ->map(static fn (AccessLevel $level): string => $level->label())
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

    /**
     * @template T of ModuleInterface
     *
     * @param class-string<T> $interface
     */
    protected function updateStatus(string $interface, ServerRequestInterface $request): void
    {
        $modules = $this->module_service->findByInterface($interface, true);

        foreach ($modules as $module) {
            $enabled = Validator::parsedBody($request)->boolean('status-' . $module->name(), false);

            if ($enabled !== $module->isEnabled()) {
                DB::table('module')
                    ->where('module_name', '=', $module->name())
                    ->update(['status' => $enabled ? 'enabled' : 'disabled']);

                if ($enabled) {
                    $message = I18N::translate('The module "%s" has been enabled.', $module->title());
                } else {
                    $message = I18N::translate('The module "%s" has been disabled.', $module->title());
                }

                FlashMessages::addMessage($message, 'success');
            }
        }
    }

    /**
     * @template T of ModuleInterface
     *
     * @param class-string<T> $interface
     */
    protected function updateAccessLevel(string $interface, ServerRequestInterface $request): void
    {
        $modules = $this->module_service->findByInterface($interface, true);
        $trees   = $this->tree_service->all();

        foreach ($modules as $module) {
            foreach ($trees as $tree) {
                $key          = 'access-' . $module->name() . '-' . $tree->id();
                $access_level = AccessLevel::from(Validator::parsedBody($request)->integer($key));

                if ($access_level !== $module->accessLevel($tree, $interface)) {
                    DB::table('module_privacy')->updateOrInsert([
                        'module_name' => $module->name(),
                        'gedcom_id'   => $tree->id(),
                        'interface'   => $interface,
                    ], [
                        'access_level' => $access_level,
                    ]);
                }
            }
        }
    }

    /**
     * @template T of ModuleInterface
     *
     * @param class-string<T> $interface
     */
    protected function updateOrder(string $interface, string $column, ServerRequestInterface $request): void
    {
        $modules = $this->module_service->findByInterface($interface, true);
        $order   = Validator::parsedBody($request)->list('order');
        $order   = array_flip($order);

        foreach ($modules as $module) {
            DB::table('module')
                ->where('module_name', '=', $module->name())
                ->update([
                    $column => $order[$module->name()] ?? 0,
                ]);
        }
    }
}
