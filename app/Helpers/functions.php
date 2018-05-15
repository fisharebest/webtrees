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
declare(strict_types=1);

use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\View;

/**
 * Generate a CSRF token form field.
 *
 * @return string
 */
function csrf_field() {
	return '<input type="hidden" name="csrf" value="' . e(Filter::getCsrfToken()) . '">';
}

/**
 * Get the CSRF token value.
 *
 * @return string
 */
function csrf_token() {
	return Filter::getCsrfToken();
}

/**
 * Escape a string for inclusion within HTML.
 *
 * @param $text
 *
 * @return string
 */
function e(string $text): string {
	return Html::escape($text);
}

/**
 * Generate a URL for a named route.
 *
 * @param string $route
 * @param array  $parameters
 * @param bool   $absolute
 *
 * @return string
 */
function route(string $route, array $parameters = [], bool $absolute = true): string {
	$parameters = ['route' => $route] + $parameters;

	if ($absolute) {
		return Html::url(WT_BASE_URL . 'index.php', $parameters);
	} else {
		return Html::url('index.php', $parameters);
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
function view(string $name, array $data = []) {
	return View::make($name, $data);
}
