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
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function redirect;
use function route;

final class ModulesAll
{
    use ViewResponseTrait;

    public function __construct(
        private ModuleService $module_service,
    ) {
    }

    public function get(): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        return $this->viewResponse('admin/modules', [
            'title'           => I18N::translate('All modules'),
            'modules'         => $this->module_service->all(true),
            'deleted_modules' => $this->module_service->deletedModules(),
        ]);
    }

    public function post(ServerRequestInterface $request): ResponseInterface
    {
        $modules = $this->module_service->all(true);

        foreach ($modules as $module) {
            $new_status = Validator::parsedBody($request)->boolean('status-' . $module->name(), false);
            $old_status = $module->isEnabled();

            if ($new_status !== $old_status) {
                DB::table('module')
                    ->where('module_name', '=', $module->name())
                    ->update(['status' => $new_status ? 'enabled' : 'disabled']);

                if ($new_status) {
                    $message = I18N::translate('The module "%s" has been enabled.', $module->title());
                } else {
                    $message = I18N::translate('The module "%s" has been disabled.', $module->title());
                }

                FlashMessages::addMessage($message, 'success');
            }
        }

        return redirect(route(self::class));
    }
}
