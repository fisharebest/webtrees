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

use Fisharebest\Webtrees\Individual;
use Illuminate\Support\Collection;

/**
 * Trait ModuleSidebarTrait - default implementation of ModuleSidebarInterface
 */
trait ModuleSidebarTrait
{
    // The default position for this sidebar.  It can be changed in the control panel.
    protected int $sidebar_order;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    abstract public function title(): string;

    /**
     * The text that appears on the sidebar's title.
     *
     * @param Individual $individual
     *
     * @return string
     */
    public function sidebarTitle(Individual $individual): string
    {
        return $this->title();
    }

    /**
     * Users change change the order of sidebars using the control panel.
     *
     * @param int $sidebar_order
     *
     * @return void
     */
    public function setSidebarOrder(int $sidebar_order): void
    {
        $this->sidebar_order = $sidebar_order;
    }

    /**
     * Users change change the order of sidebars using the control panel.
     *
     * @return int
     */
    public function getSidebarOrder(): int
    {
        return $this->sidebar_order ?? $this->defaultSidebarOrder();
    }


    /**
     * The default position for this sidebar.
     *
     * @return int
     */
    public function defaultSidebarOrder(): int
    {
        return 9999;
    }

    /**
     * This module handles the following facts - so don't show them on the "Facts and events" tab.
     *
     * @return Collection<int,string>
     */
    public function supportedFacts(): Collection
    {
        return new Collection();
    }
}
