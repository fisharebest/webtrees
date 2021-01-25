<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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
 * Interface ModuleSidebarInterface - Classes and libraries for module system
 */
interface ModuleSidebarInterface extends ModuleInterface
{
    /**
     * The text that appears on the sidebar's title.
     *
     * @return string
     */
    public function sidebarTitle(): string;

    /**
     * Users change change the order of sidebars using the control panel.
     *
     * @param int $sidebar_order
     *
     * @return void
     */
    public function setSidebarOrder(int $sidebar_order): void;

    /**
     * Users change change the order of sidebars using the control panel.
     *
     * @return int
     */
    public function getSidebarOrder(): int;

    /**
     * The default position for this sidebar.  It can be changed in the control panel.
     *
     * @return int
     */
    public function defaultSidebarOrder(): int;

    /**
     * Sidebar content.
     *
     * @param Individual $individual
     *
     * @return string
     */
    public function getSidebarContent(Individual $individual): string;

    /**
     * Does this sidebar have anything to display for this individual?
     *
     * @param Individual $individual
     *
     * @return bool
     */
    public function hasSidebarContent(Individual $individual): bool;
    /**
     * This module handles the following facts - so don't show them on the "Facts and events" tab.
     *
     * @return Collection<string>
     */
    public function supportedFacts(): Collection;
}
