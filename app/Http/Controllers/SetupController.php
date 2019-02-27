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
use Fisharebest\Webtrees\Services\ServerCheckService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Webtrees;
use Illuminate\Database\Capsule\Manager;
use SQLite3;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use function ini_get;
use const WT_DATA_DIR;

/**
 * Controller for the installation wizard
 */
class SetupController extends AbstractBaseController
{
    /** @var string */
    protected $layout = 'layouts/setup';

    private const DEFAULT_DBTYPE = 'mysql';
    private const DEFAULT_PREFIX = 'wt_';

    // We need this information to complete the setup
    private const DEFAULT_DATA = [
        'lang'    => '',
        'dbtype'  => self::DEFAULT_DBTYPE,
        'dbhost'  => '',
        'dbport'  => '',
        'dbuser'  => '',
        'dbpass'  => '',
        'dbname'  => '',
        'tblpfx'  => self::DEFAULT_PREFIX,
        'wtname'  => '',
        'wtuser'  => '',
        'wtpass'  => '',
        'wtemail' => '',
    ];

    /** @var MigrationService */
    private $migration_service;

    /** @var ServerCheckService */
    private $server_check_service;

    /** @var UserService */
    private $user_service;

    /**
     * SetupController constructor.
     *
     * @param MigrationService   $migration_service
     * @param ServerCheckService $server_check_service
     * @param UserService        $user_service
     */
    public function __construct(
        MigrationService $migration_service,
        ServerCheckService $server_check_service,
        UserService $user_service
    ) {
        $this->user_service         = $user_service;
        $this->migration_service    = $migration_service;
        $this->server_check_service = $server_check_service;
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
        $data['cpu_limit']    = $this->maxExecutionTime();
        $data['locales']      = I18N::installedLocales();
        $data['memory_limit'] = $this->memoryLimit();

        // Only show database errors after the user has chosen a driver.
        if ($step >= 4) {
            $data['errors']   = $this->server_check_service->serverErrors($data['dbtype']);
            $data['warnings'] = $this->server_check_service->serverWarnings($data['dbtype']);
        } else {
            $data['errors']       = $this->server_check_service->serverErrors();
            $data['warnings']     = $this->server_check_service->serverWarnings();
        }

        if (!$this->checkFolderIsWritable(WT_DATA_DIR)) {
            $data['errors']->push(
                '<code>' . e(realpath(WT_DATA_DIR)) . '</code><br>' .
                I18N::translate('Oops! webtrees was unable to create files in this folder.') . ' ' .
                I18N::translate('This usually means that you need to change the folder permissions to 777.')
            );
        }

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
        if ($data['errors']->isNotEmpty()) {
            return $this->viewResponse('setup/step-2-server-checks', $data);
        }

        return $this->viewResponse('setup/step-3-database-type', $data);
    }

    /**
     * @param mixed[] $data
     *
     * @return Response
     */
    private function step4DatabaseConnection(array $data): Response
    {
        if ($data['errors']->isNotEmpty()) {
            return $this->step3DatabaseType($data);
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
        try {
            $this->checkDatabase($data);
        } catch (Throwable $ex) {
            $data['errors']->push($ex->getMessage());

            // Don't jump to step 4, as the error will make it jump to step 3.
            return $this->viewResponse('setup/step-4-database-' . $data['dbtype'], $data);
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
            $data['errors']->push($error);

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
     * @throws Exception
     */
    private function checkDatabase(array $data): void
    {
        // Try to create the SQLite database, if it does not already exist.
        if ($data['dbtype'] === 'sqlite') {
            touch(WT_ROOT . 'data/' . $data['dbname'] . '.sqlite');
        }

        // Try to create the MySQL database, if it does not already exist.
        if ($data['dbtype'] === 'mysql') {
            $tmp           = $data;
            $tmp['dbname'] = '';
            Database::connect($tmp);
            Manager::connection()->statement('CREATE DATABASE IF NOT EXISTS `' . $data['dbname'] . '` COLLATE utf8_unicode_ci');
        }

        // Try to connect to the database.
        Database::connect($data);
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
}
