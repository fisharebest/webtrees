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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ServerRequestInterface;

use function assert;

/**
 * Class GoogleAnalyticsModule - add support for Google analytics.
 */
class GoogleAnalyticsModule extends AbstractModule implements ModuleAnalyticsInterface, ModuleConfigInterface, ModuleExternalUrlInterface, ModuleGlobalInterface
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
        return 'Google™ analytics';
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
        return view('modules/google-analytics/form', $this->analyticsParameters());
    }

    /**
     * Home page for the service.
     *
     * @return string
     */
    public function externalUrl(): string
    {
        return 'https://www.google.com/analytics';
    }

    /**
     * The parameters that need to be embedded in the snippet.
     *
     * @return string[]
     */
    public function analyticsParameters(): array
    {
        return [
            'GOOGLE_WEBMASTER_ID' => $this->getPreference('GOOGLE_WEBMASTER_ID'),
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
        $request = app(ServerRequestInterface::class);

        // Add extra dimensions (i.e. filtering categories)
        $tree = $request->getAttribute('tree');
        $user = $request->getAttribute('user');

        $parameters['dimensions'] = (object) [
            'dimension1' => $tree instanceof Tree ? $tree->name() : '-',
            'dimension2' => $tree instanceof Tree ? Auth::accessLevel($tree, $user) : '-',
        ];

        return view('modules/google-analytics/snippet', $parameters);
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
