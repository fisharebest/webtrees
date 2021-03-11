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

use Closure;
use ErrorException;
use Fisharebest\Webtrees\Http\Middleware\BadBotBlocker;
use Fisharebest\Webtrees\Http\Middleware\BootModules;
use Fisharebest\Webtrees\Http\Middleware\CheckForMaintenanceMode;
use Fisharebest\Webtrees\Http\Middleware\ClientIp;
use Fisharebest\Webtrees\Http\Middleware\CompressResponse;
use Fisharebest\Webtrees\Http\Middleware\DoHousekeeping;
use Fisharebest\Webtrees\Http\Middleware\EmitResponse;
use Fisharebest\Webtrees\Http\Middleware\HandleExceptions;
use Fisharebest\Webtrees\Http\Middleware\LoadRoutes;
use Fisharebest\Webtrees\Http\Middleware\NoRouteFound;
use Fisharebest\Webtrees\Http\Middleware\PhpEnvironment;
use Fisharebest\Webtrees\Http\Middleware\ReadConfigIni;
use Fisharebest\Webtrees\Http\Middleware\RegisterFactories;
use Fisharebest\Webtrees\Http\Middleware\Router;
use Fisharebest\Webtrees\Http\Middleware\SecurityHeaders;
use Fisharebest\Webtrees\Http\Middleware\UpdateDatabaseSchema;
use Fisharebest\Webtrees\Http\Middleware\UseDatabase;
use Fisharebest\Webtrees\Http\Middleware\UseDebugbar;
use Fisharebest\Webtrees\Http\Middleware\UseLanguage;
use Fisharebest\Webtrees\Http\Middleware\UseSession;
use Fisharebest\Webtrees\Http\Middleware\UseTheme;
use Fisharebest\Webtrees\Http\Middleware\UseTransaction;
use Fisharebest\Webtrees\Http\Middleware\BaseUrl;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

use function app;
use function error_reporting;
use function set_error_handler;

use const E_ALL;
use const E_DEPRECATED;
use const E_USER_DEPRECATED;

/**
 * Definitions for the webtrees application.
 */
class Webtrees
{
    // The root folder of this installation
    public const ROOT_DIR = __DIR__ . '/../';

    // This is the location of system data, such as temporary and cache files.
    // The system files are always in this location.
    // It is also the default location of user data, such as media and GEDCOM files.
    // The user files could be anywhere supported by Flysystem.
    public const DATA_DIR  = self::ROOT_DIR . 'data/';

    // Location of the file containing the database connection details.
    public const CONFIG_FILE = self::DATA_DIR . 'config.ini.php';

    // Location of the file that triggers maintenance mode.
    public const OFFLINE_FILE = self::DATA_DIR . 'offline.txt';

    // Location of our modules.
    public const MODULES_PATH = 'modules_v4/';
    public const MODULES_DIR  = self::ROOT_DIR . self::MODULES_PATH;

    // Enable debugging on development builds.
    public const DEBUG = self::STABILITY !== '';

    // We want to know about all PHP errors during development, and fewer in production.
    public const ERROR_REPORTING = self::DEBUG ? E_ALL : E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED;

    // The name of the application.
    public const NAME = 'webtrees';

    // Required version of database tables/columns/indexes/etc.
    public const SCHEMA_VERSION = 45;

    // e.g. "-dev", "-alpha", "-beta", etc.
    public const STABILITY = '-dev';

    // Version number
    public const VERSION = '2.1.0' . self::STABILITY;

    // Project website.
    public const URL = 'https://webtrees.net/';

    // FAQ links
    public const URL_FAQ_EMAIL = 'https://webtrees.net/faq/email';

    // Project website.
    public const GEDCOM_PDF = 'https://webtrees.net/downloads/gedcom-551.pdf';

    private const MIDDLEWARE = [
        PhpEnvironment::class,
        EmitResponse::class,
        SecurityHeaders::class,
        ReadConfigIni::class,
        BaseUrl::class,
        HandleExceptions::class,
        ClientIp::class,
        RegisterFactories::class,
        CompressResponse::class,
        BadBotBlocker::class,
        UseDatabase::class,
        UseDebugbar::class,
        UpdateDatabaseSchema::class,
        UseSession::class,
        UseLanguage::class,
        CheckForMaintenanceMode::class,
        UseTheme::class,
        DoHousekeeping::class,
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
     * @return array<string>
     */
    public function middleware(): array
    {
        return self::MIDDLEWARE;
    }
}
