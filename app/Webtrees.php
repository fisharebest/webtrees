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
use Fisharebest\Webtrees\Factories\CacheFactory;
use Fisharebest\Webtrees\Factories\ElementFactory;
use Fisharebest\Webtrees\Factories\FamilyFactory;
use Fisharebest\Webtrees\Factories\FilesystemFactory;
use Fisharebest\Webtrees\Factories\GedcomRecordFactory;
use Fisharebest\Webtrees\Factories\HeaderFactory;
use Fisharebest\Webtrees\Factories\ImageFactory;
use Fisharebest\Webtrees\Factories\IndividualFactory;
use Fisharebest\Webtrees\Factories\LocationFactory;
use Fisharebest\Webtrees\Factories\MarkdownFactory;
use Fisharebest\Webtrees\Factories\MediaFactory;
use Fisharebest\Webtrees\Factories\NoteFactory;
use Fisharebest\Webtrees\Factories\RepositoryFactory;
use Fisharebest\Webtrees\Factories\SlugFactory;
use Fisharebest\Webtrees\Factories\SourceFactory;
use Fisharebest\Webtrees\Factories\SubmissionFactory;
use Fisharebest\Webtrees\Factories\SubmitterFactory;
use Fisharebest\Webtrees\Factories\XrefFactory;
use Fisharebest\Webtrees\Http\Middleware\BadBotBlocker;
use Fisharebest\Webtrees\Http\Middleware\BootModules;
use Fisharebest\Webtrees\Http\Middleware\CheckForMaintenanceMode;
use Fisharebest\Webtrees\Http\Middleware\ClientIp;
use Fisharebest\Webtrees\Http\Middleware\CompressResponse;
use Fisharebest\Webtrees\Http\Middleware\ContentLength;
use Fisharebest\Webtrees\Http\Middleware\DoHousekeeping;
use Fisharebest\Webtrees\Http\Middleware\EmitResponse;
use Fisharebest\Webtrees\Http\Middleware\HandleExceptions;
use Fisharebest\Webtrees\Http\Middleware\LoadRoutes;
use Fisharebest\Webtrees\Http\Middleware\NoRouteFound;
use Fisharebest\Webtrees\Http\Middleware\ReadConfigIni;
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
use Illuminate\Container\Container;
use Middleland\Dispatcher;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;

use function date_default_timezone_set;
use function error_reporting;
use function is_string;
use function mb_internal_encoding;
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
    public const GEDCOM_PDF = 'https://webtrees.net/downloads/gedcom-5-5-1.pdf';

    private const MIDDLEWARE = [
        EmitResponse::class,
        SecurityHeaders::class,
        ReadConfigIni::class,
        BaseUrl::class,
        HandleExceptions::class,
        ClientIp::class,
        ContentLength::class,
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

        // All modern software uses UTF-8 encoding.
        mb_internal_encoding('UTF-8');

        // Use UTC internally and convert to local time when displaying datetimes.
        date_default_timezone_set('UTC');

        // Factory objects
        Registry::cache(new CacheFactory());
        Registry::familyFactory(new FamilyFactory());
        Registry::filesystem(new FilesystemFactory());
        Registry::elementFactory(new ElementFactory());
        Registry::gedcomRecordFactory(new GedcomRecordFactory());
        Registry::headerFactory(new HeaderFactory());
        Registry::imageFactory(new ImageFactory());
        Registry::individualFactory(new IndividualFactory());
        Registry::locationFactory(new LocationFactory());
        Registry::markdownFactory(new MarkdownFactory());
        Registry::mediaFactory(new MediaFactory());
        Registry::noteFactory(new NoteFactory());
        Registry::repositoryFactory(new RepositoryFactory());
        Registry::slugFactory(new SlugFactory());
        Registry::sourceFactory(new SourceFactory());
        Registry::submissionFactory(new SubmissionFactory());
        Registry::submitterFactory(new SubmitterFactory());
        Registry::xrefFactory(new XrefFactory());
    }

    /**
     * Respond to a CLI request.
     *
     * @return void
     */
    public function cliRequest(): void
    {
        // CLI handler will go here.
    }

    /**
     * Response to an HTTP request.
     *
     * @return ResponseInterface
     */
    public function httpRequest(): ResponseInterface
    {
        // PSR7 messages and PSR17 message-factories
        self::set(ResponseFactoryInterface::class, Psr17Factory::class);
        self::set(ServerRequestFactoryInterface::class, Psr17Factory::class);
        self::set(StreamFactoryInterface::class, Psr17Factory::class);
        self::set(UploadedFileFactoryInterface::class, Psr17Factory::class);
        self::set(UriFactoryInterface::class, Psr17Factory::class);

        $request = $this->captureRequest();

        return self::dispatch($request, self::MIDDLEWARE);
    }

    /**
     * @param ServerRequestInterface            $request
     * @param array<string|MiddlewareInterface> $middleware
     *
     * @return ResponseInterface
     */
    public static function dispatch(ServerRequestInterface $request, array $middleware): ResponseInterface
    {
        $dispatcher = new Dispatcher($middleware, self::container());

        return $dispatcher->dispatch($request);
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
     * Build the request from the PHP super-globals.
     *
     * @return ServerRequestInterface
     */
    private function captureRequest(): ServerRequestInterface
    {
        return self::make(ServerRequestCreator::class)->fromGlobals();
    }

    /**
     * @return ContainerInterface
     */
    public static function container(): ContainerInterface
    {
        return Container::getInstance();
    }

    /**
     * Make an object, using dependency injection.
     *
     * @param string $class
     *
     * @return mixed
     */
    public static function make(string $class)
    {
        return Container::getInstance()->make($class);
    }

    /**
     * Write a value into the container.
     *
     * @param string        $abstract
     * @param string|object $concrete
     */
    public static function set(string $abstract, $concrete): void
    {
        if (is_string($concrete)) {
            Container::getInstance()->bind($abstract, $concrete);
        } else {
            Container::getInstance()->instance($abstract, $concrete);
        }
    }
}
