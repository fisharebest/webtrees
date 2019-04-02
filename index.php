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

use Fisharebest\Webtrees\DebugBar;
use Fisharebest\Webtrees\Http\Middleware\BootModules;
use Fisharebest\Webtrees\Http\Middleware\CheckCsrf;
use Fisharebest\Webtrees\Http\Middleware\CheckForMaintenanceMode;
use Fisharebest\Webtrees\Http\Middleware\DebugBarData;
use Fisharebest\Webtrees\Http\Middleware\ExceptionHandler;
use Fisharebest\Webtrees\Http\Middleware\Housekeeping;
use Fisharebest\Webtrees\Http\Middleware\ModuleMiddleware;
use Fisharebest\Webtrees\Http\Middleware\RequestRouter;
use Fisharebest\Webtrees\Http\Middleware\UpdateDatabaseSchema;
use Fisharebest\Webtrees\Http\Middleware\UseCache;
use Fisharebest\Webtrees\Http\Middleware\UseDatabase;
use Fisharebest\Webtrees\Http\Middleware\UseFilesystem;
use Fisharebest\Webtrees\Http\Middleware\UseLocale;
use Fisharebest\Webtrees\Http\Middleware\UseSession;
use Fisharebest\Webtrees\Http\Middleware\UseTheme;
use Fisharebest\Webtrees\Http\Middleware\UseTransaction;
use Fisharebest\Webtrees\Http\Middleware\UseTree;
use Fisharebest\Webtrees\Http\Request as SymfonyRequest;
use Fisharebest\Webtrees\Webtrees;
use Middleland\Dispatcher;
use Narrowspark\HttpEmitter\SapiEmitter;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

require __DIR__ . '/vendor/autoload.php';

const WT_ROOT = __DIR__ . DIRECTORY_SEPARATOR;

Webtrees::init();

// Use nyholm/psr7 for our PSR7 messages and PSR17 factory.
app()->bind(ResponseFactoryInterface::class, Psr17Factory::class);
app()->bind(ServerRequestFactoryInterface::class, Psr17Factory::class);
app()->bind(StreamFactoryInterface::class, Psr17Factory::class);
app()->bind(UploadedFileFactoryInterface::class, Psr17Factory::class);
app()->bind(UriFactoryInterface::class, Psr17Factory::class);

// Use nyholm/psr7-server to create a request from the PHP environment.
$server_request_creator = new ServerRequestCreator(
    app(ServerRequestFactoryInterface::class),
    app(UriFactoryInterface::class),
    app(UploadedFileFactoryInterface::class),
    app(StreamFactoryInterface::class)
);

// Create a PSR-7 request
$request = $server_request_creator->fromGlobals();

// Initialise the DebugBar for development.
// Use `composer install --dev` on a development build to enable.
// Note that you may need to increase the size of the fcgi buffers on nginx.
// e.g. add these lines to your fastcgi_params file:
// fastcgi_buffers 16 16m;
// fastcgi_buffer_size 32m;
DebugBar::init(class_exists('\\DebugBar\\StandardDebugBar'));

// Until all the code is rewritten to use PSR-7 requests, we still need our hybrid request.
$request = SymfonyRequest::createFromGlobals();

// Calculate the base URL, so we can generate absolute URLs.
// Remove any PHP script name and parameters.
define('WT_BASE_URL', preg_replace('/[^\/]+\.php(\?.*)?$/', '', $request->getUri()));

$middleware = [
    ExceptionHandler::class,
    CheckForMaintenanceMode::class,
    UseDatabase::class,
    DebugBarData::class,
    UpdateDatabaseSchema::class,
    UseCache::class,
    UseFilesystem::class,
    UseSession::class,
    UseTree::class,
    UseLocale::class,
    UseTheme::class,
    Housekeeping::class,
    CheckCsrf::class,
    UseTransaction::class,
    BootModules::class,
    ModuleMiddleware::class,
    RequestRouter::class,
];

// Dispatch the middleware.
$dispatcher = new Dispatcher($middleware, app());
$response   = $dispatcher->dispatch($request);

// Emit the response.
$emitter = new SapiEmitter();
$emitter->emit($response);
