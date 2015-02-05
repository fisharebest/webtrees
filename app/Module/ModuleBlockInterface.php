<?php
namespace Fisharebest\Webtrees;

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

/**
 * Interface ModuleBlockInterface - Classes and libraries for module system
 */
interface ModuleBlockInterface {
	/**
	 * Generate the HTML content of this block.
	 * 
	 * @param integer $block_id
	 *
	 * @return string
	 */
	public function getBlock($block_id);

	/**
	 * Should this block load asynchronously using AJAX?
	 * Simple blocks are faster in-line, more comples ones
	 * can be loaded later.
	 *
	 * @return boolean
	 */
	public function loadAjax();

	/**
	 * Can this block be shown on the user’s home page?
	 *
	 * @return boolean
	 */
	public function isUserBlock();

	/**
	 * Can this block be shown on the tree’s home page?
	 *
	 * @return boolean
	 */
	public function isGedcomBlock();

	/**
	 * An HTML form to edit block settings
	 *
	 * @param integer $block_id
	 *
	 * @return void
	 */
	public function configureBlock($block_id);
}
