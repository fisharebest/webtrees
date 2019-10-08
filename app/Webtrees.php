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

use Closure;
use ErrorException;
use Fisharebest\Webtrees\Http\Middleware\BootModules;
use Fisharebest\Webtrees\Http\Middleware\CheckCsrf;
use Fisharebest\Webtrees\Http\Middleware\CheckForMaintenanceMode;
use Fisharebest\Webtrees\Http\Middleware\DoHousekeeping;
use Fisharebest\Webtrees\Http\Middleware\EmitResponse;
use Fisharebest\Webtrees\Http\Middleware\HandleExceptions;
use Fisharebest\Webtrees\Http\Middleware\LoadRoutes;
use Fisharebest\Webtrees\Http\Middleware\NoRouteFound;
use Fisharebest\Webtrees\Http\Middleware\PhpEnvironment;
use Fisharebest\Webtrees\Http\Middleware\ReadConfigIni;
use Fisharebest\Webtrees\Http\Middleware\Router;
use Fisharebest\Webtrees\Http\Middleware\UpdateDatabaseSchema;
use Fisharebest\Webtrees\Http\Middleware\UseCache;
use Fisharebest\Webtrees\Http\Middleware\UseDatabase;
use Fisharebest\Webtrees\Http\Middleware\UseDebugbar;
use Fisharebest\Webtrees\Http\Middleware\UseFilesystem;
use Fisharebest\Webtrees\Http\Middleware\UseLocale;
use Fisharebest\Webtrees\Http\Middleware\UseSession;
use Fisharebest\Webtrees\Http\Middleware\UseTheme;
use Fisharebest\Webtrees\Http\Middleware\UseTransaction;
use Fisharebest\Webtrees\Http\Middleware\UseTree;
use Fisharebest\Webtrees\Http\Middleware\WebEnvironment;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Throwable;

use function app;
use function dirname;
use function error_reporting;
use function ob_end_clean;
use function ob_get_level;
use function set_error_handler;
use function set_exception_handler;
use function str_replace;

use const PHP_EOL;

/**
 * Definitions for the webtrees application.
 */
class Webtrees
{
    // The root folder of this installation
    public const ROOT_DIR = __DIR__ . '/../';

    // Location of the file containing the database connection details.
    public const CONFIG_FILE = self::ROOT_DIR . 'data/config.ini.php';

    // Location of the file that triggers maintenance mode.
    public const OFFLINE_FILE = self::ROOT_DIR . 'data/offline.txt';

    // Location of our modules.
    public const MODULES_PATH = 'modules_v4/';
    public const MODULES_DIR  = self::ROOT_DIR . self::MODULES_PATH;

    // Enable debugging on development builds.
    public const DEBUG = self::STABILITY !== '';

    // We want to know about all PHP errors during development, and fewer in production.
    public const ERROR_REPORTING = self::DEBUG ? E_ALL | E_STRICT | E_NOTICE | E_DEPRECATED : E_ALL;

    // The name of the application.
    public const NAME = 'webtrees';

    // Required version of database tables/columns/indexes/etc.
    public const SCHEMA_VERSION = 43;

    // e.g. "dev", "alpha", "beta", etc.
    public const STABILITY = 'beta.4';

    // Version number
    public const VERSION = '2.0.0' . (self::STABILITY === '' ? '' : '-') . self::STABILITY;

    // Project website.
    public const URL = 'https://www.webtrees.net/';

    private const MIDDLEWARE = [
        PhpEnvironment::class,
        EmitResponse::class,
        HandleExceptions::class,
        ReadConfigIni::class,
        WebEnvironment::class,
        UseDatabase::class,
        UseDebugbar::class,
        UpdateDatabaseSchema::class,
        UseCache::class,
        UseFilesystem::class,
        UseSession::class,
        UseTree::class,
        UseLocale::class,
        CheckForMaintenanceMode::class,
        UseTheme::class,
        DoHousekeeping::class,
        CheckCsrf::class,
        UseTransaction::class,
        LoadRoutes::class,
        BootModules::class,
        Router::class,
        NoRouteFound::class,
    ];

    /**
     * Initialise the application.
     *
     * @return void
     */
    public function bootstrap(): void
    {
        // Show all errors and warnings in development, fewer in production.
        error_reporting(self::ERROR_REPORTING);

        set_error_handler($this->phpErrorHandler());
        set_exception_handler($this->phpExceptionHandler());
    }

    /**
     * An error handler that can be passed to set_error_handler().
     *
     * @return Closure
     */
    private function phpErrorHandler(): Closure
    {
        return static function (int $errno, string $errstr, string $errfile, int $errline): bool {
            // Ignore errors that are silenced with '@'
            if (error_reporting() & $errno) {
                throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
            }

            return true;
        };
    }

    /**
     * An exception handler that can be passed to set_exception_handler().
     * Display any exception that are not caught by the middleware exception handler.
     *
     * @return Closure
     */
    private function phpExceptionHandler(): Closure
    {
        return static function (Throwable $ex): void {
            $base_path = dirname(__DIR__);
            $trace     = $ex->getMessage() . ' ' . $ex->getFile() . ':' . $ex->getLine() . PHP_EOL . $ex->getTraceAsString();
            $trace     = str_replace($base_path, '…', $trace);

            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            echo '<html lang="en"><head><title>Error</title><meta charset="UTF-8"></head><body><pre>' . $trace . '</pre></body></html>';
        };
    }

    /**
     * We can use any PSR-7 / PSR-17 compatible message factory.
     *
     * @return void
     */
    public function selectMessageFactory(): void
    {
        app()->bind(ResponseFactoryInterface::class, Psr17Factory::class);
        app()->bind(ServerRequestFactoryInterface::class, Psr17Factory::class);
        app()->bind(StreamFactoryInterface::class, Psr17Factory::class);
        app()->bind(UploadedFileFactoryInterface::class, Psr17Factory::class);
        app()->bind(UriFactoryInterface::class, Psr17Factory::class);
    }

    /**
     * The webtrees application is built from middleware.
     *
     * @return string[]
     */
    public function middleware(): array
    {
        return self::MIDDLEWARE;
    }
}
