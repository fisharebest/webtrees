<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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
namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Menu;

/**
 * Interface ModuleMenuInterface - Classes and libraries for module system
 */
interface ModuleMenuInterface {
	/**
	 * The user can re-order menus.  Until they do, they are shown in this order.
	 *
	 * @return int
	 */
	public function defaultMenuOrder();

	/**
	 * A menu, to be added to the main application menu.
	 *
	 * @return Menu|null
	 */
	public function getMenu();
}
