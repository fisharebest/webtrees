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

use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;

/**
 * Trait ModuleChartTrait - default implementation of ModuleChartInterface
 */
trait ModuleChartTrait
{
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
     * A menu item for this chart for an individual box in a chart.
     *
     * @param Individual $individual
     *
     * @return Menu|null
     */
    public function chartBoxMenu(Individual $individual): ?Menu
    {
        return null;
    }

    /**
     * A main menu item for this chart.
     *
     * @param Individual $individual
     *
     * @return Menu
     */
    public function chartMenu(Individual $individual): Menu
    {
        return new Menu(
            $this->title(),
            $this->chartUrl($individual),
            $this->chartMenuClass(),
            $this->chartUrlAttributes()
        );
    }

    /**
     * CSS class for the menu.
     *
     * @return string
     */
    public function chartMenuClass(): string
    {
        return '';
    }

    /**
     * The title for a specific instance of this chart.
     *
     * @param Individual $individual
     *
     * @return string
     */
    public function chartTitle(Individual $individual): string
    {
        return $this->title();
    }

    /**
     * The URL for a page showing chart options.
     *
     * @param Individual                                $individual
     * @param array<bool|int|string|array<string>|null> $parameters
     *
     * @return string
     */
    public function chartUrl(Individual $individual, array $parameters = []): string
    {
        return route('module', [
                'module' => $this->name(),
                'action' => 'Chart',
                'xref'   => $individual->xref(),
                'tree'    => $individual->tree()->name(),
        ] + $parameters);
    }

    /**
     * Attributes for the URL.
     *
     * @return array<string>
     */
    public function chartUrlAttributes(): array
    {
        return ['rel' => 'nofollow'];
    }
}
