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

namespace Fisharebest\Webtrees\Http\Controllers;

use Exception;
use Fisharebest\Localization\Locale;
use Fisharebest\Localization\Locale\LocaleEnUs;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\MigrationService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Webtrees;
use PDOException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Controller for the installation wizard
 */
class SetupController extends AbstractBaseController
{
    private const DBTYPES        = ['mysql', 'sqlite', 'pgsql', 'sqlsvr'];
    private const DEFAULT_DBTYPE = 'mysql';

    // We need this information to complete the setup
    private const DEFAULT_DATA = [
        'lang'    => '',
        'dbtype'  => 'mysql',
        'dbhost'  => '',
        'dbport'  => '',
        'dbuser'  => '',
        'dbpass'  => '',
        'dbname'  => '',
        'tblpfx'  => 'wt_',
        'wtname'  => '',
        'wtuser'  => '',
        'wtpass'  => '',
        'wtemail' => '',
    ];

    /** @var MigrationService */
    private $migration_service;

    /** @var UserService */
    private $user_service;

    /** @var string */
    protected $layout = 'layouts/setup';

    /**
     * SetupController constructor.
     *
     * @param MigrationService $migration_service
     * @param UserService      $user_service
     */
    public function __construct(MigrationService $migration_service, UserService $user_service)
    {
        $this->user_service      = $user_service;
        $this->migration_service = $migration_service;
    }

    /**
     * Installation wizard - check user input and proceed to the next step.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function setup(Request $request): Response
    {
        $step = (int) $request->get('step', '1');
        $data = $this->userData($request);

        $data['lang']         = I18N::init($request->get('lang', $data['lang']));
        $data['errors']       = $this->serverErrors();
        $data['warnings']     = $this->serverWarnings();
        $data['cpu_limit']    = $this->maxExecutionTime();
        $data['locales']      = I18N::installedLocales();
        $data['memory_limit'] = $this->memoryLimit();

        define('WT_LOCALE', $data['lang']);

        switch ($step) {
            default:
            case 1:
                return $this->step1Language($data);
            case 2:
                return $this->step2CheckServer($data);
            case 3:
                return $this->step3DatabaseType($data);
            case 4:
                return $this->step4DatabaseConnection($data);
            case 5:
                return $this->step5Administrator($data);
            case 6:
                return $this->step6Install($data);
        }
    }

    /**
     * @param mixed[] $data
     *
     * @return Response
     */
    private function step1Language(array $data): Response
    {
        if ($data['lang'] === '') {
            $data['lang'] = Locale::httpAcceptLanguage($_SERVER, I18N::installedLocales(), new LocaleEnUs())->languageTag();
        }

        return $this->viewResponse('setup/step-1-language', $data);
    }

    /**
     * @param mixed[] $data
     *
     * @return Response
     */
    private function step2CheckServer(array $data): Response
    {
        return $this->viewResponse('setup/step-2-server-checks', $data);
    }

    /**
     * @param mixed[] $data
     *
     * @return Response
     */
    private function step3DatabaseType(array $data): Response
    {
        return $this->viewResponse('setup/step-3-database-type', $data);
    }

    /**
     * @param mixed[] $data
     *
     * @return Response
     */
    private function step4DatabaseConnection(array $data): Response
    {
        if (!in_array($data['dbtype'], self::DBTYPES)) {
            $data['dbtype'] = self::DEFAULT_DBTYPE;

            return $this->step3DatabaseType($data);
        }

        switch ($data['dbtype']) {
            case 'sqlite':
                $data['warnings'][] = I18N::translate('SQLite is only suitable for small sites, testing and evaluation.');
                if ($data['dbname'] === '') {
                    $data['dbname'] ='webtrees';
                }
                break;
            case 'pgsql':
                $data['warnings'][] = I18N::translate('Support for PostgreSQL is experimental.\') . \' \' . I18N::translate(\'Please report any problems to the developers.');
                break;

            case 'sqlsvr':
                $data['warnings'][] = I18N::translate('Support for SQL Server is experimental.') . ' ' . I18N::translate('Please report any problems to the developers.');
                break;

        }

        return $this->viewResponse('setup/step-4-database-' . $data['dbtype'], $data);
    }

    /**
     * @param mixed[] $data
     *
     * @return Response
     */
    private function step5Administrator(array $data): Response
    {
        $error = $this->checkDatabase($data);

        if ($error !== '') {
            $data['errors'][] = $error;

            return $this->step4DatabaseConnection($data);
        }

        return $this->viewResponse('setup/step-5-administrator', $data);
    }

    /**
     * @param mixed[] $data
     *
     * @return Response
     */
    private function step6Install(array $data): Response
    {
        $error = $this->checkAdminUser($data['wtname'], $data['wtuser'], $data['wtpass'], $data['wtemail']);

        if ($error !== '') {
            $data['errors'][] = $error;

            return $this->step5Administrator($data);
        }

        try {
            $this->createConfigFile($data);
        } catch (Throwable $exception) {
            return $this->viewResponse('setup/step-6-failed', ['exception' => $exception]);
        }

        // Done - start using webtrees!
        return new RedirectResponse(route('admin-trees'));
    }

    /**
     * @param string $wtname
     * @param string $wtuser
     * @param string $wtpass
     * @param string $wtemail
     *
     * @return string
     */
    private function checkAdminUser($wtname, $wtuser, $wtpass, $wtemail): string
    {
        if ($wtname === '' || $wtuser === '' || $wtpass === '' || $wtemail === '') {
            return I18N::translate('You must enter all the administrator account fields.');
        }

        if (mb_strlen($wtpass) < 6) {
            return I18N::translate('The password needs to be at least six characters long.');
        }

        return '';
    }

    /**
     * Check we can write to the data folder.
     *
     * @param mixed $data
     *
     * @return string
     */
    private function checkDatabase(array $data): string
    {
        // The character ` is not valid in database or table names (even if escaped).
        // The form should prevent the user from entering them.
        if ($data['dbname'] === '') {
            return 'Invalid database name';
        }

        // Try to create the database, if it does not already exist.
        if ($data['dbtype'] === 'sqlite') {
            touch(WT_ROOT . 'data/' . $data['dbname'] . '.sqlite');
        }

        if ($data['dbtype'] === 'mysql') {
        }

        try {
            Database::connect($data);

            //Database::exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` COLLATE utf8_unicode_ci");
            //Database::exec("USE `{$dbname}`");
        } catch (PDOException $ex) {
            return e($ex->getMessage()) . '<br><br>' . I18N::translate('Check the settings and try again.');
        }

        return '';
    }

    /**
     * Check we can write to the data folder.
     *
     * @param string $data_dir
     *
     * @return bool
     */
    private function checkFolderIsWritable(string $data_dir): bool
    {
        $text1 = uniqid();
        try {
            file_put_contents($data_dir . 'test.txt', $text1);
            $text2 = file_get_contents(WT_DATA_DIR . 'test.txt');
            unlink(WT_DATA_DIR . 'test.txt');
        } catch (Exception $ex) {
            return false;
        }

        return $text1 === $text2;
    }

    /**
     * Check the language parameters are OK.
     *
     * @param Request $request
     *
     * @return void
     */
    private function checkLanguage(Request $request): void
    {
        $language = I18N::init($request->get('lang', ''));
    }

    /**
     * @param string[] $data
     *
     * @return void
     */
    private function createConfigFile(array $data): void
    {
        // Create/update the database tables.
        Database::connect($data);
        $this->migration_service->updateSchema('\Fisharebest\Webtrees\Schema', 'WT_SCHEMA_VERSION', Webtrees::SCHEMA_VERSION);

        // Add some default/necessary configuration data.
        $this->migration_service->seedDatabase();

        // If we are re-installing, then this user may already exist.
        $admin = $this->user_service->findByIdentifier($data['wtemail']);
        if ($admin === null) {
            $admin = $this->user_service->findByIdentifier($data['wtuser']);
        }
        // Create the user
        if ($admin === null) {
            $admin = $this->user_service->create($data['wtuser'], $data['wtname'], $data['wtemail'], $data['wtpass'])
                ->setPreference('language', WT_LOCALE)
                ->setPreference('visibleonline', '1');
        } else {
            $admin->setPassword($_POST['wtpass']);
        }
        // Make the user an administrator
        $admin
            ->setPreference('canadmin', '1')
            ->setPreference('verified', '1')
            ->setPreference('verified_by_admin', '1');

        // Write the config file. We already checked that this would work.
        $config_ini_php =
            '; <' . '?php exit; ?' . '> DO NOT DELETE THIS LINE' . PHP_EOL .
            'dbtype="' . addcslashes($data['dbtype'], '"') . '"' . PHP_EOL .
            'dbhost="' . addcslashes($data['dbhost'], '"') . '"' . PHP_EOL .
            'dbport="' . addcslashes($data['dbport'], '"') . '"' . PHP_EOL .
            'dbuser="' . addcslashes($data['dbuser'], '"') . '"' . PHP_EOL .
            'dbpass="' . addcslashes($data['dbpass'], '"') . '"' . PHP_EOL .
            'dbname="' . addcslashes($data['dbname'], '"') . '"' . PHP_EOL .
            'tblpfx="' . addcslashes($data['tblpfx'], '"') . '"' . PHP_EOL;

        file_put_contents(Webtrees::CONFIG_FILE, $config_ini_php);
    }

    /**
     * @param Request $request
     *
     * @return mixed[]
     */
    private function userData(Request $request): array
    {
        $data = [];

        foreach (self::DEFAULT_DATA as $key => $default) {
            $data[$key] = $request->get($key, $default);
        }

        return $data;
    }

    /**
     * The server's memory limit
     *
     * @return int
     */
    private function maxExecutionTime(): int
    {
        return (int) ini_get('max_execution_time');
    }

    /**
     * The server's memory limit (in MB).
     *
     * @return int
     */
    private function memoryLimit(): int
    {
        $memory_limit = ini_get('memory_limit');

        switch (substr($memory_limit, -1)) {
            case 'k':
            case 'K':
                $memory_limit = substr($memory_limit, 0, -1) / 1024;
                break;
            case 'm':
            case 'M':
                $memory_limit = substr($memory_limit, 0, -1);
                break;
            case 'g':
            case 'G':
                $memory_limit = substr($memory_limit, 0, -1) * 1024;
                break;
            case 't':
            case 'T':
                $memory_limit = substr($memory_limit, 0, -1) * 1024 * 1024;
                break;
            default:
                $memory_limit = $memory_limit / 1024 / 1024;
        }

        return (int) $memory_limit;
    }

    /**
     * A list of major server issues.
     *
     * @return array
     */
    private function serverErrors(): array
    {
        $extensions = [
            'mbstring',
            'iconv',
            'pcre',
            'pdo',
            'pdo_mysql',
            'session',
        ];
        $functions  = ['parse_ini_file'];
        $errors     = [];

        if (!$this->checkFolderIsWritable(WT_DATA_DIR)) {
            $errors[] = '<code>' . e(realpath(WT_DATA_DIR)) . '</code><br>' . I18N::translate('Oops! webtrees was unable to create files in this folder.') . '<br>' . I18N::translate('This usually means that you need to change the folder permissions to 777.') . '<br>' . I18N::translate('You must change this before you can continue.');
        }

        foreach ($extensions as $extension) {
            if (!extension_loaded($extension)) {
                $errors[] = I18N::translate('PHP extension “%s” is disabled. You cannot install webtrees until this is enabled. Please ask your server’s administrator to enable it.', $extension);
            }
        }

        $disable_functions = explode(',', ini_get('disable_functions'));
        $disable_functions = array_map(function (string $func): string {
            return trim($func);
        }, $disable_functions);

        foreach ($functions as $function) {
            if (in_array($function, $disable_functions)) {
                /* I18N: %s is a PHP function/module/setting */
                $errors[] = I18N::translate('%s is disabled on this server. You cannot install webtrees until it is enabled. Please ask your server’s administrator to enable it.', $function . '()');
            }
        }

        return $errors;
    }

    /**
     * A list of minor server issues.
     *
     * @return array
     */
    private function serverWarnings(): array
    {
        $extensions = [
            /* I18N: a program feature */
            'gd'        => I18N::translate('creating thumbnails of images'),
            /* I18N: a program feature */
            'xml'       => I18N::translate('reporting'),
            /* I18N: a program feature */
            'simplexml' => I18N::translate('reporting'),
        ];
        $settings   = [
            /* I18N: a program feature */
            'file_uploads' => I18N::translate('file upload capability'),
        ];
        $warnings   = [];

        foreach ($extensions as $extension => $features) {
            if (!extension_loaded($extension)) {
                $warnings[] = I18N::translate('PHP extension “%1$s” is disabled. Without it, the following features will not work: %2$s. Please ask your server’s administrator to enable it.', $extension, $features);
            }
        }

        foreach ($settings as $setting => $features) {
            if (!ini_get($setting)) {
                $warnings[] = I18N::translate('PHP setting “%1$s” is disabled. Without it, the following features will not work: %2$s. Please ask your server’s administrator to enable it.', $setting, $features);
            }
        }

        return $warnings;
    }
}
