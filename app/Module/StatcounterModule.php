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

/**
 * Class StatcounterModule - add support for statcounter.
 */
class StatcounterModule extends AbstractModule implements ModuleAnalyticsInterface, ModuleConfigInterface, ModuleExternalUrlInterface, ModuleGlobalInterface
{
    use ModuleAnalyticsTrait;
    use ModuleConfigTrait;
    use ModuleExternalUrlTrait;
    use ModuleGlobalTrait;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        return I18N::translate('Statcounter™');
    }

    /**
     * Should this module be enabled when it is first installed?
     *
     * @return bool
     */
    public function isEnabledByDefault(): bool
    {
        return false;
    }

    /**
     * Form fields to edit the parameters.
     *
     * @return string
     */
    public function analyticsFormFields(): string
    {
        return view('modules/statcounter/form', $this->analyticsParameters());
    }

    /**
     * Home page for the service.
     *
     * @return string
     */
    public function externalUrl(): string
    {
        return 'https://statcounter.com';
    }

    /**
     * The parameters that need to be embedded in the snippet.
     *
     * @return string[]
     */
    public function analyticsParameters(): array
    {
        return [
            'STATCOUNTER_PROJECT_ID' => $this->getPreference('STATCOUNTER_PROJECT_ID'),
            'STATCOUNTER_SECURITY_ID' => $this->getPreference('STATCOUNTER_SECURITY_ID'),
        ];
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
        return view('modules/statcounter/snippet', $parameters);
    }

    /**
     * Raw content, to be added at the end of the <body> element.
     * Typically, this will be <script> elements.
     *
     * @return string
     */
    public function bodyContent(): string
    {
        if ($this->analyticsCanShow()) {
            return $this->analyticsSnippet($this->analyticsParameters());
        }

        return '';
    }
}
