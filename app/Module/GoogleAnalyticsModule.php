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
use Fisharebest\Webtrees\User;

/**
 * Class GoogleAnalyticsModule - add support for Google analytics.
 */
class GoogleAnalyticsModule extends AbstractModule implements  ModuleAnalyticsInterface
{
    use ModuleAnalyticsTrait;

    /**
     * How should this module be labelled on tabs, menus, etc.?
     *
     * @return string
     */
    public function title(): string {
        return 'Google™ analytics';
    }

    /**
     * Form fields to edit the parameters.
     *
     * @return string
     */
    public function analyticsFormFields(): string
    {
        return view('admin/analytics/google-analytics-form', $this->analyticsParameters());
    }

    /**
     * Home page for the service.
     *
     * @return string
     */
    public function analyticsHomePageUrl(): string
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
            'GOOGLE_WEBMASTER_ID' => $this->getPreference('GOOGLE_WEBMASTER_ID')
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
        // Add extra dimensions (i.e. filtering categories)
        $tree = app()->make(Tree::class);
        $user = app()->make(User::class);

        $parameters['dimensions'] = (object) [
            'dimension1' => $tree instanceof Tree ? $tree->name() : '-',
            'dimension2' => $tree instanceof Tree ? Auth::accessLevel($tree, $user) : '-',
        ];

        return view('admin/analytics/google-analytics-snippet', $parameters);
    }
}
