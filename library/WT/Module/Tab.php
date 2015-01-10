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
 * Interface WT_Module_Tab - Classes and libraries for module system
 */
interface WT_Module_Tab {
	/**
	 * The user can re-arrange the tab order, but until they do, this
	 * is the order in which tabs are shown.
	 *
	 * @return integer
	 */
	public function defaultTabOrder();

	/**
	 * Generate the HTML content of this tab.
	 *
	 * @return string
	 */
	public function getTabContent();

	/**
	 * Is this tab empty?  If so, we don't always need to display it.
	 * @return boolean
	 */
	public function hasTabContent();

	/**
	 * Can this tab load asynchronously?
	 *
	 * @return boolean
	 */
	public function canLoadAjax();

	/**
	 * Any content (e.g. Javascript) that needs to be rendered before the tabs.
	 *
	 * This function is probably not needed, as there are better ways to achieve this.
	 *
	 * @return string
	 */
	public function getPreLoadContent();

	/**
	 * A greyed out tab has no actual content, but may perhaps have
	 * options to create content.
	 *
	 * @return boolean
	 */
	public function isGrayedOut();
}
