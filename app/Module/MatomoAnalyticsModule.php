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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\I18N;

use function str_ends_with;

/**
 * Class MatomoAnalyticsModule - add support for Matomo analytics.
 */
class MatomoAnalyticsModule extends AbstractModule implements ModuleAnalyticsInterface, ModuleConfigInterface, ModuleExternalUrlInterface, ModuleGlobalInterface
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
        return I18N::translate('Matomo™ / Piwik™ analytics');
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
        return view('modules/matomo-analytics/form', $this->analyticsParameters());
    }

    /**
     * Home page for the service.
     *
     * @return string
     */
    public function externalUrl(): string
    {
        return 'https://matomo.org';
    }

    /**
     * The parameters that need to be embedded in the snippet.
     *
     * @return array<string>
     */
    public function analyticsParameters(): array
    {
        $matomo_site_id = $this->getPreference('MATOMO_SITE_ID');
        $matomo_url     = $this->getPreference('MATOMO_URL');

        // Reports on the webtrees forum say that a trailing slash is required.
        if (!str_ends_with($matomo_url, '/')) {
            $matomo_url .= '/';
        }

        return [
            'MATOMO_SITE_ID' => $matomo_site_id,
            'MATOMO_URL'     => $matomo_url,
        ];
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
        $parameters['user'] = Auth::user();

        return view('modules/matomo-analytics/snippet', $parameters);
    }

    /**
     * Raw content, to be added at the end of the <head> element.
     * Typically, this will be <link> and <meta> elements.
     *
     * @return string
     */
    public function headContent(): string
    {
        if ($this->analyticsCanShow()) {
            return $this->analyticsSnippet($this->analyticsParameters());
        }

        return '';
    }
}
