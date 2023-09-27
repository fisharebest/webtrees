<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface ModuleFooterInterface - Add content to the bottom of every page.
 */
interface ModuleFooterInterface extends ModuleInterface
{
    /**
     * Users change change the order of footers using the control panel.
     *
     * @param int $footer_order
     *
     * @return void
     */
    public function setFooterOrder(int $footer_order): void;

    /**
     * Users change change the order of footers using the control panel.
     *
     * @return int
     */
    public function getFooterOrder(): int;

    /**
     * The default position for this footer.  It can be changed in the control panel.
     *
     * @return int
     */
    public function defaultFooterOrder(): int;

    /**
     * A footer, to be added at the bottom of every page.
     *
     * @param ServerRequestInterface $request
     *
     * @return string
     */
    public function getFooter(ServerRequestInterface $request): string;
}
