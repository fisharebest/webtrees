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
declare(strict_types=1);

namespace Fisharebest\Webtrees;

use ErrorException;
use function date_default_timezone_set;
use function error_reporting;
use function set_error_handler;

/**
 * Definitions for the webtrees application.
 */
class Webtrees
{
    // Location of the file containing the database connection details.
    const CONFIG_FILE = __DIR__ . '/../data/config.ini.php';

    // Enable debugging on development builds.
    const DEBUG = self::STABILITY !== '';

    // We want to know about all PHP errors during development, and fewer in production.
    const ERROR_REPORTING = self::DEBUG ? E_ALL | E_STRICT | E_NOTICE | E_DEPRECATED : E_ALL;

    // The name of the application.
    const NAME = 'webtrees';

    // Required version of database tables/columns/indexes/etc.
    const SCHEMA_VERSION = 40;

    // e.g. "dev", "alpha", "beta.3", etc.
    const STABILITY = 'dev';

    // Project website.
    const URL = 'https://www.webtrees.net/';

    // Version number
    const VERSION = '2.0.0' . (self::STABILITY === '' ? '' : '-') . self::STABILITY;

    // Location of our modules and themes. These are used as URLs and folder paths.
    const MODULES_PATH = 'modules_v3/';

    // Location of themes (core and custom).
    const THEMES_PATH = 'themes/';

    // Location of CSS/JS/etc. assets. See also webpack.mix.js.
    const ASSETS_PATH = 'public/assets-2.0.0/';

    // Location of our installation of CK editor.
    const CKEDITOR_PATH = 'public/ckeditor-4.5.2-custom/';

    /**
     * Initialise the application.
     *
     * @return void
     */
    public static function init()
    {
        // Show all errors and warnings in development, fewer in production.
        error_reporting(self::ERROR_REPORTING);

        // PHP requires a time zone to be set. We'll set a better one later on.
        date_default_timezone_set('UTC');

        // Convert PHP warnings/notices into exceptions
        set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline): bool {
            // Ignore errors that are silenced with '@'
            if (error_reporting() & $errno) {
                throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
            }

            return true;
        });
    }
}
