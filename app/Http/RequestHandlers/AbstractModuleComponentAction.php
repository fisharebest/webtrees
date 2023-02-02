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

use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\ModuleInterface;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Validator;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_flip;

/**
 * Update a list of modules.
 */
abstract class AbstractModuleComponentAction implements RequestHandlerInterface
{
    protected ModuleService $module_service;

    protected TreeService $tree_service;

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
     * Update the access levels of the modules.
     *
     * @template T of ModuleInterface
     *
     * @param class-string<T>        $interface
     * @param ServerRequestInterface $request
     *
     * @return void
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
                    $message = I18N::translate('The module “%s” has been enabled.', $module->title());
                } else {
                    $message = I18N::translate('The module “%s” has been disabled.', $module->title());
                }

                FlashMessages::addMessage($message, 'success');
            }
        }
    }

    /**
     * Update the access levels of the modules.
     *
     * @template T of ModuleInterface
     *
     * @param class-string<T>        $interface
     * @param ServerRequestInterface $request
     *
     * @return void
     */
    protected function updateAccessLevel(string $interface, ServerRequestInterface $request): void
    {
        $modules = $this->module_service->findByInterface($interface, true);
        $trees   = $this->tree_service->all();

        foreach ($modules as $module) {
            foreach ($trees as $tree) {
                $key          = 'access-' . $module->name() . '-' . $tree->id();
                $access_level = Validator::parsedBody($request)->integer($key);

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
     * Update the access levels of the modules.
     *
     * @template T of ModuleInterface
     *
     * @param class-string<T>        $interface
     * @param string                 $column
     * @param ServerRequestInterface $request
     *
     * @return void
     */
    protected function updateOrder(string $interface, string $column, ServerRequestInterface $request): void
    {
        $modules = $this->module_service->findByInterface($interface, true);
        $order   = Validator::parsedBody($request)->array('order');
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
