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
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface ModuleTabInterface - Classes and libraries for module system
 */
interface ModuleTabInterface extends ModuleInterface
{
    /**
     * The text that appears on the tab.
     *
     * @return string
     */
    public function tabTitle(): string;

    /**
     * Users change change the order of tabs using the control panel.
     *
     * @param int $tab_order
     *
     * @return void
     */
    public function setTabOrder(int $tab_order): void;

    /**
     * Users change change the order of tabs using the control panel.
     *
     * @return int
     */
    public function getTabOrder(): int;

    /**
     * The default position for this tab.  It can be changed in the control panel.
     *
     * @return int
     */
    public function defaultTabOrder(): int;

    /**
     * Generate the HTML content of this tab.
     *
     * @param Individual $individual
     *
     * @return string
     */
    public function getTabContent(Individual $individual): string;

    /**
     * Is this tab empty? If so, we don't always need to display it.
     *
     * @param Individual $individual
     *
     * @return bool
     */
    public function hasTabContent(Individual $individual): bool;

    /**
     * Can this tab load asynchronously?
     *
     * @return bool
     */
    public function canLoadAjax(): bool;

    /**
     * A greyed out tab has no actual content, but may perhaps have
     * options to create content.
     *
     * @param Individual $individual
     *
     * @return bool
     */
    public function isGrayedOut(Individual $individual): bool;

    /**
     * This module handles the following facts - so don't show them on the "Facts and events" tab.
     *
     * @return Collection<string>
     */
    public function supportedFacts(): Collection;

    /**
     * Generate an AJAX response for a tab.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getTabAction(ServerRequestInterface $request): ResponseInterface;
}
