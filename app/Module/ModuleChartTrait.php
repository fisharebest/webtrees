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

use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;

/**
 * Trait ModuleChartTrait - default implementation of ModuleChartInterface
 */
trait ModuleChartTrait
{
    /**
     * @return string
     */
    abstract function getName(): string;

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
            $this->chartUrlClasss(),
            $this->chartUrlAttributes()
        );
    }

    /**
     * A menu item for this chart for an individual box in a chart.
     *
     * @param Individual $individual
     *
     * @return Menu|null
     */
    public function chartMenuIndividual(Individual $individual): ?Menu
    {
        return null;
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
     * @param Individual $individual
     * @param string[]   $parameters
     *
     * @return string
     */
    public function chartUrl(Individual $individual, array $parameters = []): string
    {
        return route('module', [
            'module' => $this->getName(),
            'action' => 'Chart',
            'xref'   => $individual->xref(),
            'ged'    => $individual->tree()->name(),
        ] + $parameters);
    }

    /**
     * Attributes for the URL.
     *
     * @return string[]
     */
    public function chartUrlAttributes(): array
    {
        return ['rel' => 'nofollow'];
    }

    /**
     * CSS class for the URL.
     *
     * @return string
     */
    public function chartUrlClasss(): string
    {
        return '';
    }
}
