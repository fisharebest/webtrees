<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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
namespace Fisharebest\Webtrees;

/**
 * Simple view/template class.
 */
class View {
	/**
	 * @var string The (file) name of the view.
	 */
	private $name;

	/**
	 * @var mixed[] Data to be inserted into the view.
	 */
	private $data;

	/**
	 * Createa view from a template name and optional data.
	 *
	 * @param       $name
	 * @param array $data
	 */
	public function __construct($name, $data = []) {
		$this->name = $name;
		$this->data = $data;
	}

	/**
	 * Render a view.
	 *
	 * @return string
	 */
	public function render() {
		extract($this->data);

		ob_start();
		require WT_ROOT . 'resources/views/' . $this->name . '.php';
		return ob_get_clean();
	}

	/**
	 * Check whether a view exists.
	 *
	 * @param string $view_name
	 *
	 * @return bool
	 */
	public static function exists($view_name) {
		return file_exists(WT_ROOT . 'resources/views/' . $view_name . '.php');
	}

	/**
	 * Cerate and render a view in a single operation.
	 *
	 * @param string  $name
	 * @param mixed[] $data
	 *
	 * @return string
	 */
	public static function make($name, $data = []) {
		$view = new static($name, $data);

		return $view->render();
	}
}
