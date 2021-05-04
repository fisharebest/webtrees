<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees;

use Middleland\Dispatcher;
use Nyholm\Psr7Server\ServerRequestCreator;

use function app;
use function is_file;
use function is_string;
use function parse_url;

use const PHP_SAPI;
use const PHP_URL_PATH;

require __DIR__ . '/vendor/autoload.php';

if (PHP_SAPI === 'cli-server') {
    $file = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_string($file) && is_file($file)) {
        return false;
    }
}

// Create the application.
$application = new Webtrees();
$application->bootstrap();

// Select a PSR message factory.
$application->selectMessageFactory();

// The application is defined by a stack of middleware and a PSR-11 container.
$middleware = $application->middleware();
$container  = app();
$dispatcher = new Dispatcher($middleware, $container);

// Build the request from the PHP super-globals.
$request = app(ServerRequestCreator::class)->fromGlobals();

$dispatcher->dispatch($request);
