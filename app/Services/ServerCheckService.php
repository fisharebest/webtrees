<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\I18N;
use Illuminate\Support\Collection;
use SQLite3;

use function array_map;
use function class_exists;
use function date;
use function e;
use function explode;
use function extension_loaded;
use function function_exists;
use function in_array;
use function str_ends_with;
use function str_starts_with;
use function strtolower;
use function sys_get_temp_dir;
use function trim;
use function version_compare;

use const PATH_SEPARATOR;
use const PHP_MAJOR_VERSION;
use const PHP_MINOR_VERSION;
use const PHP_VERSION;

/**
 * Check if the server meets the minimum requirements for webtrees.
 */
class ServerCheckService
{
    private const PHP_SUPPORT_URL   = 'https://www.php.net/supported-versions.php';
    private const PHP_MINOR_VERSION = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;
    private const PHP_SUPPORT_DATES = [
        '8.1' => '2024-11-25',
        '8.2' => '2025-12-08',
    ];

    // As required by illuminate/database 8.x
    private const MINIMUM_SQLITE_VERSION = '3.8.8';

    /**
     * Things that may cause webtrees to break.
     *
     * @param string $driver
     *
     * @return Collection<int,string>
     */
    public function serverErrors(string $driver = ''): Collection
    {
        $errors = Collection::make([
            $this->databaseDriverErrors($driver),
            $this->checkPhpExtension('mbstring'),
            $this->checkPhpExtension('iconv'),
            $this->checkPhpExtension('pcre'),
            $this->checkPhpExtension('session'),
            $this->checkPhpExtension('xml'),
            $this->checkPhpFunction('parse_ini_file'),
        ]);

        return $errors
            ->flatten()
            ->filter();
    }

    /**
     * Things that should be fixed, but which won't stop completely webtrees from running.
     *
     * @param string $driver
     *
     * @return Collection<int,string>
     */
    public function serverWarnings(string $driver = ''): Collection
    {
        $warnings = Collection::make([
            $this->databaseDriverWarnings($driver),
            $this->checkPhpExtension('curl'),
            $this->checkPhpExtension('exif'),
            $this->checkPhpExtension('fileinfo'),
            $this->checkPhpExtension('gd'),
            $this->checkPhpExtension('intl'),
            $this->checkPhpExtension('zip'),
            $this->checkPhpExtension('simplexml'),
            $this->checkPhpIni('file_uploads', true),
            $this->checkSystemTemporaryFolder(),
            $this->checkPhpVersion(),
        ]);

        return $warnings
            ->flatten()
            ->filter();
    }

    /**
     * Check if a PHP extension is loaded.
     *
     * @param string $extension
     *
     * @return string
     */
    private function checkPhpExtension(string $extension): string
    {
        if (!extension_loaded($extension)) {
            return I18N::translate('The PHP extension “%s” is not installed.', $extension);
        }

        return '';
    }

    /**
     * Check if a PHP setting is correct.
     *
     * @param string $varname
     * @param bool   $expected
     *
     * @return string
     */
    private function checkPhpIni(string $varname, bool $expected): string
    {
        $actual = (bool) ini_get($varname);

        if ($expected && !$actual) {
            return I18N::translate('The PHP.INI setting “%1$s” is disabled.', $varname);
        }

        if (!$expected && $actual) {
            return I18N::translate('The PHP.INI setting “%1$s” is enabled.', $varname);
        }

        return '';
    }

    /**
     * Check if a PHP function is in the list of disabled functions.
     *
     * @param string $function
     *
     * @return bool
     */
    public function isFunctionDisabled(string $function): bool
    {
        $function = strtolower($function);

        $disable_functions = explode(',', (string) ini_get('disable_functions'));
        $disable_functions = array_map(static fn (string $func): string => strtolower(trim($func)), $disable_functions);

        return in_array($function, $disable_functions, true) || !function_exists($function);
    }

    /**
     * Create a warning message for a disabled function.
     *
     * @param string $function
     *
     * @return string
     */
    private function checkPhpFunction(string $function): string
    {
        if ($this->isFunctionDisabled($function)) {
            return I18N::translate('The PHP function “%1$s” is disabled.', $function . '()');
        }

        return '';
    }

    /**
     * Some servers configure their temporary folder in an inaccessible place.
     */
    private function checkPhpVersion(): string
    {
        $today = date('Y-m-d');

        foreach (self::PHP_SUPPORT_DATES as $version => $end_date) {
            if ($today > $end_date && version_compare(self::PHP_MINOR_VERSION, $version) <= 0) {
                return I18N::translate('Your web server is using PHP version %s, which is no longer receiving security updates. You should upgrade to a later version as soon as possible.', PHP_VERSION) . ' <a href="' . e(self::PHP_SUPPORT_URL) . '">' . e(self::PHP_SUPPORT_URL) . '</a>';
            }
        }

        return '';
    }

    /**
     * Check the
     *
     * @return string
     */
    private function checkSqliteVersion(): string
    {
        if (class_exists(SQLite3::class)) {
            $sqlite_version = SQLite3::version()['versionString'];

            if (version_compare($sqlite_version, self::MINIMUM_SQLITE_VERSION) < 0) {
                return I18N::translate('SQLite version %s is installed. SQLite version %s or later is required.', $sqlite_version, self::MINIMUM_SQLITE_VERSION);
            }
        }

        return '';
    }

    /**
     * Some servers configure their temporary folder in an inaccessible place.
     */
    private function checkSystemTemporaryFolder(): string
    {
        $open_basedir = ini_get('open_basedir');

        if ($open_basedir === '') {
            // open_basedir not used.
            return '';
        }

        $open_basedirs = explode(PATH_SEPARATOR, $open_basedir);

        $sys_temp_dir = sys_get_temp_dir();
        $sys_temp_dir = $this->normalizeFolder($sys_temp_dir);

        foreach ($open_basedirs as $dir) {
            $dir = $this->normalizeFolder($dir);

            if (str_starts_with($sys_temp_dir, $dir)) {
                return '';
            }
        }

        $message = I18N::translate('The server’s temporary folder cannot be accessed.');
        $message .= '<br>sys_get_temp_dir() = "' . e($sys_temp_dir) . '"';
        $message .= '<br>ini_get("open_basedir") = "' . e($open_basedir) . '"';

        return $message;
    }

    /**
     * Convert a folder name to a canonical form:
     * - forward slashes.
     * - trailing slash.
     * We can't use realpath() as this can trigger open_basedir restrictions,
     * and we are using this code to find out whether open_basedir will affect us.
     *
     * @param string $path
     *
     * @return string
     */
    private function normalizeFolder(string $path): string
    {
        $path = strtr($path, ['\\' => '/']);

        if (str_ends_with($path, '/')) {
            return $path;
        }

        return $path . '/';
    }

    /**
     * @param string $driver
     *
     * @return Collection<int,string>
     */
    private function databaseDriverErrors(string $driver): Collection
    {
        switch ($driver) {
            case 'mysql':
                return Collection::make([
                    $this->checkPhpExtension('pdo'),
                    $this->checkPhpExtension('pdo_mysql'),
                ]);

            case 'sqlite':
                return Collection::make([
                    $this->checkPhpExtension('pdo'),
                    $this->checkPhpExtension('sqlite3'),
                    $this->checkPhpExtension('pdo_sqlite'),
                    $this->checkSqliteVersion(),
                ]);

            case 'pgsql':
                return Collection::make([
                    $this->checkPhpExtension('pdo'),
                    $this->checkPhpExtension('pdo_pgsql'),
                ]);

            case 'sqlsrv':
                return Collection::make([
                    $this->checkPhpExtension('pdo'),
                    $this->checkPhpExtension('pdo_odbc'),
                ]);

            default:
                return new Collection();
        }
    }

    /**
     * @param string $driver
     *
     * @return Collection<int,string>
     */
    private function databaseDriverWarnings(string $driver): Collection
    {
        switch ($driver) {
            case 'sqlite':
                return new Collection([
                    I18N::translate('SQLite is only suitable for small sites, testing and evaluation.'),
                ]);

            case 'pgsql':
                return new Collection([
                    I18N::translate('Support for PostgreSQL is experimental.'),
                ]);

            case 'sqlsrv':
                return new Collection([
                    I18N::translate('Support for SQL Server is experimental.'),
                ]);

            default:
                return new Collection();
        }
    }
}
