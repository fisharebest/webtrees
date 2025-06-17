<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ServerRequestInterface;

use function view;

/**
 * Class GoogleAnalyticsModule - add support for Google analytics.
 */
class GoogleAnalyticsModule extends AbstractModule implements ModuleAnalyticsInterface, ModuleConfigInterface, ModuleExternalUrlInterface, ModuleGlobalInterface
{
    use ModuleAnalyticsTrait;
    use ModuleConfigTrait;
    use ModuleExternalUrlTrait;
    use ModuleGlobalTrait;

    public function title(): string
    {
        return I18N::translate('Google™ analytics');
    }

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
     * @return array<string>
     */
    public function analyticsParameters(): array
    {
        return [
            'GOOGLE_ANALYTICS_ID' => $this->getPreference('GOOGLE_ANALYTICS_ID'),
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
        $request = Registry::container()->get(ServerRequestInterface::class);

        // Add extra dimensions (i.e. filtering categories)
        $tree = Validator::attributes($request)->treeOptional();
        $user = Validator::attributes($request)->user();

        if (str_starts_with($parameters['GOOGLE_ANALYTICS_ID'], 'UA-')) {
            $parameters['dimensions'] = (object) [
                'dimension1' => $tree instanceof Tree ? $tree->name() : '-',
                'dimension2' => $tree instanceof Tree ? Auth::accessLevel($tree, $user) : '-',
            ];

            return view('modules/google-analytics/snippet', $parameters);
        }

        $parameters['tree_name'] = $tree instanceof Tree ? $tree->name() : '-';
        $parameters['access_level'] = $tree instanceof Tree ? Auth::accessLevel($tree, $user) : '-';

        return view('modules/google-analytics/snippet-v4', $parameters);
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
