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

use function date_default_timezone_set;
use function error_reporting;
use Fisharebest\Webtrees\Exceptions\Handler;
use function set_error_handler;
use function set_exception_handler;

/**
 * Definitions for the webtrees application.
 */
class Webtrees
{
    // Location of the file containing the database connection details.
    public const CONFIG_FILE = __DIR__ . '/../data/config.ini.php';

    // Enable debugging on development builds.
    public const DEBUG = self::STABILITY !== '';

    // We want to know about all PHP errors during development, and fewer in production.
    public const ERROR_REPORTING = self::DEBUG ? E_ALL | E_STRICT | E_NOTICE | E_DEPRECATED : E_ALL;

    // The name of the application.
    public const NAME = 'webtrees';

    // Required version of database tables/columns/indexes/etc.
    public const SCHEMA_VERSION = 43;

    // e.g. "dev", "alpha", "beta.3", etc.
    public const STABILITY = 'alpha.5';

    // Project website.
    public const URL = 'https://www.webtrees.net/';

    // Version number
    public const VERSION = '2.0.0' . (self::STABILITY === '' ? '' : '-') . self::STABILITY;

    // Location of our modules and themes. These are used as URLs and folder paths.
    public const MODULES_PATH = 'modules_v4/';

    // Location of themes (core and custom).
    public const THEMES_PATH = 'themes/';

    /**
     * Initialise the application.
     *
     * @return void
     */
    public static function init(): void
    {
        mb_internal_encoding('UTF-8');

        // Show all errors and warnings in development, fewer in production.
        error_reporting(Webtrees::ERROR_REPORTING);

        // PHP requires a time zone to be set. We'll set a better one later on.
        date_default_timezone_set('UTC');

        set_error_handler(Handler::phpErrorHandler());
        set_exception_handler(Handler::phpExceptionHandler());
    }
}
