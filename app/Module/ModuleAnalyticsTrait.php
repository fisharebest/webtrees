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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Http\RequestHandlers\ModulesAnalyticsPage;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Trait ModuleAnalyticsTrait - default implementation of ModuleAnalyticsInterface
 */
trait ModuleAnalyticsTrait
{
    use ViewResponseTrait;

    /**
     * A unique internal name for this module (based on the installation folder).
     *
     * @return string
     */
    abstract public function name(): string;

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
        $request = Registry::container()->get(ServerRequestInterface::class);

        // If the browser sets the DNT header, then we won't use analytics.
        if (Validator::serverParams($request)->boolean('HTTP_DNT', false)) {
            return false;
        }

        foreach ($this->analyticsParameters() as $parameter) {
            if ($parameter === '') {
                return false;
            }
        }

        return true;
    }

    /**
     * The parameters that need to be embedded in the snippet.
     *
     * @return array<string>
     */
    public function analyticsParameters(): array
    {
        return [];
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
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getAdminAction(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        return $this->viewResponse('admin/analytics-edit', [
            'action'      => route('module', ['module' => $this->name(), 'action' => 'Admin']),
            'form_fields' => $this->analyticsFormFields(),
            'preview'     => $this->analyticsSnippet($this->analyticsParameters()),
            'title'       => $this->title(),
        ]);
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
     * Embed placeholders in the snippet.
     *
     * @param array<string> $parameters
     *
     * @return string
     */
    public function analyticsSnippet(array $parameters): string
    {
        return '';
    }

    /**
     * Is this a tracker, as opposed to just a site-verification.
     *
     * @return bool
     */
    public function isTracker(): bool
    {
        return true;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postAdminAction(ServerRequestInterface $request): ResponseInterface
    {
        foreach (array_keys($this->analyticsParameters()) as $parameter) {
            $new_value = Validator::parsedBody($request)->string($parameter);

            $this->setPreference($parameter, $new_value);
        }

        return redirect(route(ModulesAnalyticsPage::class));
    }
}
