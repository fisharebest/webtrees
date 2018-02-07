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

use Fisharebest\Webtrees\Http\Controllers\SetupController;
use Fisharebest\Webtrees\Theme\WebtreesTheme;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

// This script (uniquely) does not load session.php.
// session.php won’t run until a configuration file exists…
// This next block of code is a minimal version of session.php
error_reporting(E_ALL);

define('WT_CONFIG_FILE', 'config.ini.php');

require 'vendor/autoload.php';

define('WT_WEBTREES', 'webtrees');
define('WT_BASE_URL', '');
define('WT_DATA_DIR', 'data/');
define('WT_REQUIRED_MYSQL_VERSION', '5.0.13');
define('WT_REQUIRED_PHP_VERSION', '5.6');
define('WT_MODULES_DIR', 'modules_v3/');
define('WT_ROOT', __DIR__ . '/');
define('WT_THEMES_DIR', 'themes/');
Theme::theme(new WebtreesTheme);

// PHP requires a time zone to be set. We'll set a better one later on.
date_default_timezone_set('UTC');

define('WT_LOCALE', I18N::init('en-US'));

// The HTTP request.
$request = Request::createFromGlobals();
$method  = $request->getMethod();
$route   = $request->get('route');

switch ($method . ':' . $route) {
	default:
		$url      = Html::url('setup.php', ['route' => 'setup']);
		$response = new RedirectResponse($url);
		break;

	case 'GET:setup':
	case 'POST:setup':
		$response = (new SetupController)->setup($request);
		break;
}

$response->prepare($request)->send();
