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

namespace Fisharebest\Webtrees;

use Middleland\Dispatcher;

require __DIR__ . '/vendor/autoload.php';

// Create the application.
$application = new Webtrees();

$application->bootstrap();

// Select a PSR message factory.
$application->selectMessageFactory();

// Convert the GET, POST, COOKIE variables into a request.
$request = $application->createServerRequest();

// Calculate the base URL, so we can generate absolute URLs.
// Remove any PHP script name and parameters.
define('WT_BASE_URL', preg_replace('/[^\/]+\.php(\?.*)?$/', '', (string) $request->getUri()));

// The application is defined by a stack of middleware and a PSR-11 container.
$middleware = $application->middleware();
$container  = app();
$dispatcher = new Dispatcher($middleware, $container);
$dispatcher->dispatch($request);
