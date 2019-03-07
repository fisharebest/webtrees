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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\I18N;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use SQLite3;
use function array_map;
use function explode;
use function extension_loaded;
use function in_array;
use function ini_get;
use function strtolower;
use function sys_get_temp_dir;
use function trim;
use function version_compare;
use const PATH_SEPARATOR;
use const PHP_MAJOR_VERSION;
use const PHP_MINOR_VERSION;

/**
 * Check if the server meets the minimum requirements for webtrees.
 */
class ServerCheckService
{
    private const PHP_SUPPORT_URL   = 'https://secure.php.net/supported-versions.php';
    private const PHP_MINOR_VERSION = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;
    private const PHP_SUPPORT_DATES = [
        '7.1' => '2019-12-01',
        '7.2' => '2020-11-30',
        '7.3' => '2021-12-06',
    ];

    // As required by illuminate/database 5.8
    private const MINIMUM_SQLITE_VERSION = '3.7.11';

    /**
     * Things that may cause webtrees to break.
     *
     * @param string $driver
     *
     * @return Collection
     * @return string[]
     */
    public function serverErrors($driver = ''): Collection
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
     * @return Collection
     * @return string[]
     */
    public function serverWarnings($driver = ''): Collection
    {
        $warnings = Collection::make([
            $this->databaseDriverWarnings($driver),
            $this->checkPhpExtension('curl'),
            $this->checkPhpExtension('gd'),
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
        $ini_get = (bool) ini_get($varname);

        if ($expected && $ini_get !== $expected) {
            return I18N::translate('The PHP.INI setting “%1$s” is disabled.', $varname);
        }

        if (!$expected && $ini_get !== $expected) {
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
        $disable_functions = explode(',', ini_get('disable_functions'));
        $disable_functions = array_map(function (string $func): string {
            return trim(strtolower($func));
        }, $disable_functions);

        $function = strtolower($function);

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
     * Some servers configure their temporary folder in an unaccessible place.
     */
    private function checkPhpVersion(): string
    {
        $today = date('Y-m-d');

        foreach (self::PHP_SUPPORT_DATES as $version => $end_date) {
            if (version_compare(self::PHP_MINOR_VERSION, $version) <= 0 && $today > $end_date) {
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
     * Some servers configure their temporary folder in an unaccessible place.
     */
    private function checkSystemTemporaryFolder(): string
    {
        $open_basedir  = ini_get('open_basedir');
        $open_basedirs = explode(PATH_SEPARATOR, $open_basedir);
        $sys_temp_dir  = sys_get_temp_dir();

        if ($open_basedir === '' || Str::startsWith($sys_temp_dir, $open_basedirs)) {
            return '';
        }

        $message = I18N::translate('The server’s temporary folder cannot be accessed.');
        $message .= '<br>sys_get_temp_dir() = "' . e($sys_temp_dir) . '"';
        $message .= '<br>ini_get("open_basedir") = "' . e($open_basedir) . '"';

        return $message;
    }

    /**
     * @param string $driver
     *
     * @return Collection
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

            case 'sqlsvr':
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
     * @return Collection
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

            case 'sqlsvr':
                return new Collection([
                    I18N::translate('Support for SQL Server is experimental.'),
                ]);

            default:
                return new Collection();
        }
    }
}
