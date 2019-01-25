<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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

namespace Fisharebest\Webtrees\Http\Controllers\Admin;

use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Module\ModuleAnalyticsInterface;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for configuring the tracking and analytics modules.
 */
class AnalyticsController extends AbstractAdminController
{
    /**
     * @return Response
     */
    public function list(): Response
    {
        /* I18N: e.g. http://www.google.com/analytics */
        $title = I18N::translate('Tracking and analytics');

        return $this->viewResponse('admin/analytics/index', [
            'modules' => Module::findByInterface(ModuleAnalyticsInterface::class),
            'title'   => $title,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function edit(Request $request): Response
    {
        $module_name = $request->get('module');
        $module      = Module::findByName($module_name);

        if ($module instanceof ModuleAnalyticsInterface) {
            return $this->viewResponse('admin/analytics/edit', [
                'module_name' => $module_name,
                'form_fields' => $module->analyticsFormFields(),
                'preview'     => $module->analyticsSnippet($module->analyticsParameters()),
                'title'       => $module->title(),
            ]);
        }

        return new RedirectResponse(route('analytics'));
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function save(Request $request): RedirectResponse
    {
        $module_name = $request->get('module', '');

        $module = Module::findByName($module_name);

        if ($module instanceof ModuleAnalyticsInterface) {
            foreach (array_keys($module->analyticsParameters()) as $setting_name) {
                $module->setPreference($setting_name, $request->get($setting_name, ''));
            }

            FlashMessages::addMessage(I18N::translate('The preferences for the module “%s” have been updated.', $module->title()), 'success');
        }

        return new RedirectResponse(route('analytics'));
    }
}
