<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

use Fisharebest\Webtrees\Cli\Console;
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
use Fisharebest\Webtrees\Http\Dispatcher;
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
use Fisharebest\Webtrees\Http\Middleware\ErrorHandler;
use Fisharebest\Webtrees\Http\Middleware\HandleExceptions;
use Fisharebest\Webtrees\Http\Middleware\LoadRoutes;
use Fisharebest\Webtrees\Http\Middleware\PublicFiles;
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
use Fisharebest\Webtrees\Services\PhpService;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

use function date_default_timezone_set;
use function error_reporting;
use function mb_internal_encoding;
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
    public const string ROOT_DIR = __DIR__ . '/../';

    // This is the location of system data, such as temporary and cache files.
    // The system files are always in this location.
    // It is also the default location of user data, such as media and GEDCOM files.
    // The user files could be anywhere supported by Flysystem.
    public const string DATA_DIR = self::ROOT_DIR . 'data/';

    // Location of the file containing the database connection details.
    public const string CONFIG_FILE = self::DATA_DIR . 'config.ini.php';

    // Location of our modules.
    public const string MODULES_PATH = 'modules_v4/';
    public const string MODULES_DIR  = self::ROOT_DIR . self::MODULES_PATH;

    // Enable debugging on development builds.
    public const bool DEBUG = self::STABILITY !== '';

    // We want to know about all PHP errors during development, and fewer in production.
    public const int ERROR_REPORTING = self::DEBUG ? E_ALL : E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED;

    // Page layouts for various page types.
    public const string LAYOUT_ADMINISTRATION = 'layouts/administration';
    public const string LAYOUT_AJAX    = 'layouts/ajax';
    public const string LAYOUT_DEFAULT = 'layouts/default';
    public const string LAYOUT_ERROR   = 'layouts/error';

    // The name of the application.
    public const string NAME = 'webtrees';

    // Required version of database tables/columns/indexes/etc.
    public const int SCHEMA_VERSION = 45;

    // e.g. "-dev", "-alpha", "-beta", etc.
    public const string STABILITY = '-dev';

    // Version number.
    public const string VERSION = '2.2.2' . self::STABILITY;

    // Project website.
    public const string URL = 'https://webtrees.net/';

    // FAQ links.
    public const string URL_FAQ_EMAIL = 'https://webtrees.net/faq/email';

    // GEDCOM specification.
    public const string GEDCOM_PDF = 'https://webtrees.net/downloads/gedcom-5-5-1.pdf';

    private const array MIDDLEWARE = [
        ErrorHandler::class,
        EmitResponse::class,
        ReadConfigIni::class,
        BaseUrl::class,
        SecurityHeaders::class,
        HandleExceptions::class,
        PublicFiles::class,
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
    ];

    public static function new(): self
    {
        return new self();
    }

    /**
     * Initialise the application.
     */
    public function bootstrap(): self
    {
        // Show all errors and warnings in development, fewer in production.
        error_reporting(self::ERROR_REPORTING);

        // All modern software uses UTF-8 encoding.
        mb_internal_encoding('UTF-8');

        // Use UTC internally and convert to local time when displaying datetimes.
        date_default_timezone_set('UTC');

        // Factory objects
        Registry::cache(new CacheFactory());
        Registry::calendarDateFactory(new CalendarDateFactory());
        Registry::container(new Container());
        Registry::elementFactory(new ElementFactory());
        Registry::encodingFactory(new EncodingFactory());
        Registry::familyFactory(new FamilyFactory());
        Registry::filesystem(new FilesystemFactory());
        Registry::gedcomRecordFactory(new GedcomRecordFactory());
        Registry::headerFactory(new HeaderFactory());
        Registry::idFactory(new IdFactory());
        Registry::imageFactory(new ImageFactory(new PhpService()));
        Registry::individualFactory(new IndividualFactory());
        Registry::locationFactory(new LocationFactory());
        Registry::markdownFactory(new MarkdownFactory());
        Registry::mediaFactory(new MediaFactory());
        Registry::noteFactory(new NoteFactory());
        Registry::repositoryFactory(new RepositoryFactory());
        Registry::responseFactory(new ResponseFactory(new Psr17Factory(), new Psr17Factory()));
        Registry::routeFactory(new RouteFactory());
        Registry::sharedNoteFactory(new SharedNoteFactory());
        Registry::slugFactory(new SlugFactory(new PhpService()));
        Registry::sourceFactory(new SourceFactory());
        Registry::submissionFactory(new SubmissionFactory());
        Registry::submitterFactory(new SubmitterFactory());
        Registry::surnameTraditionFactory(new SurnameTraditionFactory());
        Registry::timeFactory(new TimeFactory());
        Registry::timestampFactory(new TimestampFactory());
        Registry::xrefFactory(new XrefFactory());

        // PSR7 messages and PSR17 message-factories
        Registry::container()
            ->set(ResponseFactoryInterface::class, new Psr17Factory())
            ->set(ServerRequestFactoryInterface::class, new Psr17Factory())
            ->set(StreamFactoryInterface::class, new Psr17Factory())
            ->set(UploadedFileFactoryInterface::class, new Psr17Factory())
            ->set(UriFactoryInterface::class, new Psr17Factory());

        stream_filter_register(GedcomEncodingFilter::class, GedcomEncodingFilter::class);

        return $this;
    }

    /**
     * Run the application.
     */
    public function run(string $php_sapi): int|ResponseInterface
    {
        if ($php_sapi === 'cli') {
            return $this->bootstrap()->cliRequest();
        }

        return $this->bootstrap()->httpRequest();
    }

    /**
     * Respond to a CLI request.
     */
    public function cliRequest(): int
    {
        $console = new Console();

        return $console->loadCommands()->bootstrap()->run();
    }

    /**
     * Respond to an HTTP request.
     */
    public function httpRequest(): ResponseInterface
    {
        $server_request_creator = new ServerRequestCreator(
            Registry::container()->get(ServerRequestFactoryInterface::class),
            Registry::container()->get(UriFactoryInterface::class),
            Registry::container()->get(UploadedFileFactoryInterface::class),
            Registry::container()->get(StreamFactoryInterface::class)
        );

        $request = $server_request_creator->fromGlobals();

        return Dispatcher::dispatch(middleware: self::MIDDLEWARE, request: $request);
    }
}
