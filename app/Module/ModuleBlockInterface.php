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

/**
 * Interface ModuleBlockInterface - Classes and libraries for module system
 */
interface ModuleBlockInterface {
	/**
	 * Generate the HTML content of this block.
	 *
	 * @param int      $block_id
	 * @param bool     $template
	 * @param string[] $cfg
	 *
	 * @return string
	 */
	public function getBlock($block_id, $template = true, $cfg = array());

	/**
	 * Should this block load asynchronously using AJAX?
	 *
	 * Simple blocks are faster in-line, more comples ones
	 * can be loaded later.
	 *
	 * @return bool
	 */
	public function loadAjax();

	/**
	 * Can this block be shown on the user’s home page?
	 *
	 * @return bool
	 */
	public function isUserBlock();

	/**
	 * Can this block be shown on the tree’s home page?
	 *
	 * @return bool
	 */
	public function isGedcomBlock();

	/**
	 * An HTML form to edit block settings
	 *
	 * @param int $block_id
	 */
	public function configureBlock($block_id);
}
