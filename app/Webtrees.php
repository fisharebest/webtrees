<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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
use Fisharebest\Webtrees\Factories\CalendarDateFactory;
use Fisharebest\Webtrees\Factories\ElementFactory;
use Fisharebest\Webtrees\Factories\EncodingFactory;
use Fisharebest\Webtrees\Factories\FamilyFactory;
use Fisharebest\Webtrees\Factories\FilesystemFactory;
use Fisharebest\Webtrees\Factories\GedcomRecordFactory;
use Fisharebest\Webtrees\Factories\HeaderFactory;
use Fisharebest\Webtrees\Factories\IdFactory;
use Fisharebest\Webtrees\Factories\ImageFactory;
use Fisharebest\Webtrees\Factories\IndividualFactory;
use Fisharebest\Webtrees\Factories\LocationFactory;
use Fisharebest\Webtrees\Factories\MarkdownFactory;
use Fisharebest\Webtrees\Factories\MediaFactory;
use Fisharebest\Webtrees\Factories\NoteFactory;
use Fisharebest\Webtrees\Factories\RepositoryFactory;
use Fisharebest\Webtrees\Factories\ResponseFactory;
use Fisharebest\Webtrees\Factories\RouteFactory;
use Fisharebest\Webtrees\Factories\SharedNoteFactory;
use Fisharebest\Webtrees\Factories\SlugFactory;
use Fisharebest\Webtrees\Factories\SourceFactory;
use Fisharebest\Webtrees\Factories\SubmissionFactory;
use Fisharebest\Webtrees\Factories\SubmitterFactory;
use Fisharebest\Webtrees\Factories\SurnameTraditionFactory;
use Fisharebest\Webtrees\Factories\TimeFactory;
use Fisharebest\Webtrees\Factories\TimestampFactory;
use Fisharebest\Webtrees\Factories\XrefFactory;
use Fisharebest\Webtrees\GedcomFilters\GedcomEncodingFilter;
use Fisharebest\Webtrees\Http\Middleware\BadBotBlocker;
use Fisharebest\Webtrees\Http\Middleware\BaseUrl;
use Fisharebest\Webtrees\Http\Middleware\BootModules;
use Fisharebest\Webtrees\Http\Middleware\CheckForMaintenanceMode;
use Fisharebest\Webtrees\Http\Middleware\CheckForNewVersion;
use Fisharebest\Webtrees\Http\Middleware\ClientIp;
use Fisharebest\Webtrees\Http\Middleware\CompressResponse;
use Fisharebest\Webtrees\Http\Middleware\ContentLength;
use Fisharebest\Webtrees\Http\Middleware\DoHousekeeping;
use Fisharebest\Webtrees\Http\Middleware\EmitResponse;
use Fisharebest\Webtrees\Http\Middleware\HandleExceptions;
use Fisharebest\Webtrees\Http\Middleware\LoadRoutes;
use Fisharebest\Webtrees\Http\Middleware\NoRouteFound;
use Fisharebest\Webtrees\Http\Middleware\ReadConfigIni;
use Fisharebest\Webtrees\Http\Middleware\RegisterGedcomTags;
use Fisharebest\Webtrees\Http\Middleware\Router;
use Fisharebest\Webtrees\Http\Middleware\SecurityHeaders;
use Fisharebest\Webtrees\Http\Middleware\UpdateDatabaseSchema;
use Fisharebest\Webtrees\Http\Middleware\UseDatabase;
use Fisharebest\Webtrees\Http\Middleware\UseLanguage;
use Fisharebest\Webtrees\Http\Middleware\UseSession;
use Fisharebest\Webtrees\Http\Middleware\UseTheme;
use Fisharebest\Webtrees\Http\Middleware\UseTransaction;
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
use function stream_filter_register;

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
    public const DATA_DIR = self::ROOT_DIR . 'data/';

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

    // Page layouts for various page types.
    public const LAYOUT_ADMINISTRATION = 'layouts/administration';
    public const LAYOUT_AJAX           = 'layouts/ajax';
    public const LAYOUT_DEFAULT        = 'layouts/default';
    public const LAYOUT_ERROR          = 'layouts/error';

    // The name of the application.
    public const NAME = 'webtrees';

    // Required version of database tables/columns/indexes/etc.
    public const SCHEMA_VERSION = 45;

    // e.g. "-dev", "-alpha", "-beta", etc.
    public const STABILITY = '-dev';

    // Version number.
    public const VERSION = '2.2.0' . self::STABILITY;

    // Project website.
    public const URL = 'https://webtrees.net/';

    // FAQ links.
    public const URL_FAQ_EMAIL = 'https://webtrees.net/faq/email';

    // GEDCOM specification.
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
        UpdateDatabaseSchema::class,
        UseSession::class,
        UseLanguage::class,
        CheckForMaintenanceMode::class,
        UseTheme::class,
        DoHousekeeping::class,
        UseTransaction::class,
        CheckForNewVersion::class,
        LoadRoutes::class,
        RegisterGedcomTags::class,
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
        Registry::calendarDateFactory(new CalendarDateFactory());
        Registry::elementFactory(new ElementFactory());
        Registry::encodingFactory(new EncodingFactory());
        Registry::familyFactory(new FamilyFactory());
        Registry::filesystem(new FilesystemFactory());
        Registry::gedcomRecordFactory(new GedcomRecordFactory());
        Registry::headerFactory(new HeaderFactory());
        Registry::idFactory(new IdFactory());
        Registry::imageFactory(new ImageFactory());
        Registry::individualFactory(new IndividualFactory());
        Registry::locationFactory(new LocationFactory());
        Registry::markdownFactory(new MarkdownFactory());
        Registry::mediaFactory(new MediaFactory());
        Registry::noteFactory(new NoteFactory());
        Registry::repositoryFactory(new RepositoryFactory());
        Registry::responseFactory(new ResponseFactory(new Psr17Factory(), new Psr17Factory()));
        Registry::routeFactory(new RouteFactory());
        Registry::sharedNoteFactory(new SharedNoteFactory());
        Registry::slugFactory(new SlugFactory());
        Registry::sourceFactory(new SourceFactory());
        Registry::submissionFactory(new SubmissionFactory());
        Registry::submitterFactory(new SubmitterFactory());
        Registry::surnameTraditionFactory(new SurnameTraditionFactory());
        Registry::timeFactory(new TimeFactory());
        Registry::timestampFactory(new TimestampFactory());
        Registry::xrefFactory(new XrefFactory());

        stream_filter_register(GedcomEncodingFilter::class, GedcomEncodingFilter::class);
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
     * Respond to an HTTP request.
     *
     * @return ResponseInterface
     */
    public function httpRequest(): ResponseInterface
    {
        $psr17factory = new Psr17Factory();

        // PSR7 messages and PSR17 message-factories
        self::set(ResponseFactoryInterface::class, $psr17factory);
        self::set(ServerRequestFactoryInterface::class, $psr17factory);
        self::set(StreamFactoryInterface::class, $psr17factory);
        self::set(UploadedFileFactoryInterface::class, $psr17factory);
        self::set(UriFactoryInterface::class, $psr17factory);

        $server_request_creator = new ServerRequestCreator($psr17factory, $psr17factory, $psr17factory, $psr17factory);

        $request = $server_request_creator->fromGlobals();

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
    public static function set(string $abstract, string|object $concrete): void
    {
        if (is_string($concrete)) {
            Container::getInstance()->bind($abstract, $concrete);
        } else {
            Container::getInstance()->instance($abstract, $concrete);
        }
    }
}
