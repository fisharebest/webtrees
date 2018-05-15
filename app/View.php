<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
	 * @var mixed[] Data to be inserted into all views.
	 */
	private static $shared_data = [];

	/**
	 * @var string Implementation of Blade "stacks".
	 */
	private static $stack;

	/**
	 * @var array[] Implementation of Blade "stacks".
	 */
	private static $stacks = [];

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
	 * Shared data that is available to all views.
	 */
	public static function share($key, $value) {
		self::$shared_data[$key] = $value;
	}

	/**
	 * Implementation of Blade "stacks".
	 *
	 * @see https://laravel.com/docs/5.5/blade#stacks
	 */
	public static function push(string $stack) {
		self::$stack = $stack;
		ob_start();
	}

	/**
	 * Implementation of Blade "stacks".
	 */
	public static function endpush() {
		self::$stacks[self::$stack][] = ob_get_clean();
	}

	/**
	 * Implementation of Blade "stacks".
	 *
	 * @return string
	 */
	public static function stack(string $stack): string {
		$content = implode('', self::$stacks[$stack] ?? []);

		self::$stacks[$stack] = [];

		return $content;
	}

	/**
	 * Render a view.
	 *
	 * @return string
	 */
	public function render() {
		extract($this->data + self::$shared_data);

		ob_start();
		// Do not use require, so we can catch errors for missing files
		include $this->getFilenameForView($this->name);

		return ob_get_clean();
	}

	/**
	 * Allow a theme to override the default views.
	 *
	 * @param string $view_name
	 *
	 * @return string
	 */
	public static function getFilenameForView($view_name) {
		$view_file  = '/resources/views/' . $view_name . '.php';
		$theme_view = WT_ROOT . WT_THEMES_DIR . Theme::theme()->themeId() . $view_file;

		if (is_file($theme_view)) {
			return $theme_view;
		} else {
			return WT_ROOT . $view_file;
		}
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

		DebugBar::addView($name, $data);

		return $view->render();
	}
}
