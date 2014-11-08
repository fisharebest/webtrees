<?php
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

/**
 * Interface WT_Module_Sidebar - Classes and libraries for module system
 */
interface WT_Module_Sidebar {
	/**
	 * The user can change the order of sidebars.  Until they do this, they are shown in this order.
	 *
	 * @return integer
	 */
	public function defaultSidebarOrder();

	/**
	 * Load this sidebar synchronously.
	 * @return string
	 */
	public function getSidebarContent();

	/**
	 * Load this sidebar asynchronously.
	 *
	 * @return string
	 */
	public function getSidebarAjaxContent();

	/**
	 * Does this sidebar have anything to display for this individual?
	 *
	 * @return boolean
	 */
	public function hasSidebarContent();
}
