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

use Fisharebest\Webtrees\Application;
use Fisharebest\Webtrees\Webtrees;
use Illuminate\Cache\Repository;

/**
 * Get the IoC container, or fetch something from it.
 *
 * @param string|null $abstract
 *
 * @return Application|Repository|mixed
 */
function app(string $abstract = null)
{
    if ($abstract === null) {
        return Application::getInstance();
    } else {
        return Application::getInstance()->make($abstract);
    }
}

/**
 * Generate a URL to an asset file in the public folder.
 * Add a version parameter for cache-busting.
 *
 * @param string $path
 *
 * @return string
 */
function asset(string $path): string
{
    if (Webtrees::STABILITY === '') {
        $version = Webtrees::VERSION;
    } else {
        $version = filemtime(WT_ROOT . 'public/' . $path);
    }

    return 'public/' . $path . '?v=' . $version;
}

/**
 * Generate a CSRF token form field.
 *
 * @return string
 */
function csrf_field()
{
    return '<input type="hidden" name="csrf" value="' . e(\Fisharebest\Webtrees\Session::getCsrfToken()) . '">';
}

/**
 * Get the CSRF token value.
 *
 * @return string
 */
function csrf_token()
{
    return \Fisharebest\Webtrees\Session::getCsrfToken();
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
function route(string $route, array $parameters = [], bool $absolute = true): string
{
    $parameters = ['route' => $route] + $parameters;

    if ($absolute) {
        return \Fisharebest\Webtrees\Html::url(WT_BASE_URL . 'index.php', $parameters);
    }

    return \Fisharebest\Webtrees\Html::url('index.php', $parameters);
}

/**
 * Cerate and render a view in a single operation.
 *
 * @param string  $name
 * @param mixed[] $data
 *
 * @return string
 */
function view(string $name, array $data = [])
{
    return \Fisharebest\Webtrees\View::make($name, $data);
}
