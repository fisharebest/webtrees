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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\I18N;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Trait ModuleAnalyticsTrait - default implementation of ModuleAnalyticsInterface
 */
trait ModuleAnalyticsTrait
{
    /**
     * @param $view_name
     * @param $view_data
     * @param $status
     *
     * @return Response
     */
    abstract protected function viewResponse($view_name, $view_data, $status = Response::HTTP_OK): Response;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    abstract public function title(): string;

    /**
     * Set a module setting.
     *
     * Since module settings are NOT NULL, setting a value to NULL will cause
     * it to be deleted.
     *
     * @param string $setting_name
     * @param string $setting_value
     *
     * @return void
     */
    abstract public function setPreference(string $setting_name, string $setting_value): void;

    /**
     * Should we add this tracker?
     *
     * @return bool
     */
    public function analyticsCanShow(): bool
    {
        foreach ($this->analyticsParameters() as $parameter) {
            if ($parameter === '') {
                return false;
            }
        }

        return true;
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        return I18N::translate('Tracking and analytics');
    }

    /**
     * Form fields to edit the parameters.
     *
     * @return string
     */
    public function analyticsFormFields(): string
    {
        return '';
    }

    /**
     * The parameters that need to be embedded in the snippet.
     *
     * @return string[]
     */
    public function analyticsParameters(): array
    {
        return [];
    }

    /**
     * Embed placeholders in the snippet.
     *
     * @param string[] $parameters
     *
     * @return string
     */
    public function analyticsSnippet(array $parameters): string
    {
        return '';
    }


    /**
     * @return Response
     */
    public function getAdminAction(): Response
    {
        $this->layout = 'layouts/administration';

        return $this->viewResponse('admin/analytics-edit', [
            'form_fields' => $this->analyticsFormFields(),
            'preview'     => $this->analyticsSnippet($this->analyticsParameters()),
            'title'       => $this->title(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function postAdminAction(Request $request): RedirectResponse
    {
        foreach (array_keys($this->analyticsParameters()) as $parameter) {
            $new_value = $request->get($parameter, '');
            $this->setPreference($parameter, $new_value);
        }

        return new RedirectResponse(route('analytics'));
    }
}
