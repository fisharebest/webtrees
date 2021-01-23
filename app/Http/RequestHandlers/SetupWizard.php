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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Exception;
use Fisharebest\Localization\Locale;
use Fisharebest\Localization\Locale\LocaleEnUs;
use Fisharebest\Localization\Locale\LocaleInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Cache;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Factories\CacheFactory;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\ModuleLanguageInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\MigrationService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\ServerCheckService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Webtrees;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Throwable;

use function app;
use function e;
use function file_get_contents;
use function file_put_contents;
use function ini_get;
use function random_bytes;
use function realpath;
use function redirect;
use function substr;
use function touch;
use function unlink;
use function view;

/**
 * Controller for the installation wizard
 */
class SetupWizard implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private const DEFAULT_DBTYPE = 'mysql';
    private const DEFAULT_PREFIX = 'wt_';
    private const DEFAULT_DATA   = [
        'baseurl' => '',
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

    private const DEFAULT_PORTS = [
        'mysql'  => '3306',
        'pgsql'  => '5432',
        'sqlite' => '',
        'sqlsvr' => '1433',
    ];

    /** @var MigrationService */
    private $migration_service;

    /** @var ModuleService */
    private $module_service;

    /** @var ServerCheckService */
    private $server_check_service;

    /** @var UserService */
    private $user_service;

    /**
     * SetupWizard constructor.
     *
     * @param MigrationService   $migration_service
     * @param ModuleService      $module_service
     * @param ServerCheckService $server_check_service
     * @param UserService        $user_service
     */
    public function __construct(
        MigrationService $migration_service,
        ModuleService $module_service,
        ServerCheckService $server_check_service,
        UserService $user_service
    ) {
        $this->user_service         = $user_service;
        $this->migration_service    = $migration_service;
        $this->module_service       = $module_service;
        $this->server_check_service = $server_check_service;
    }

    /**
     * Installation wizard - check user input and proceed to the next step.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/setup';

        // Some functions need a cache, but we don't have one yet.
        Registry::cache(new CacheFactory());

        // We will need an IP address for the logs.
        $ip_address  = $request->getServerParams()['REMOTE_ADDR'] ?? '127.0.0.1';
        $request     = $request->withAttribute('client-ip', $ip_address);

        app()->instance(ServerRequestInterface::class, $request);
        app()->instance('cache.array', new Cache(new NullAdapter()));

        $data = $this->userData($request);

        $params = (array) $request->getParsedBody();
        $step   = (int) ($params['step'] ?? '1');

        $locales = $this->module_service
            ->setupLanguages()
            ->map(static function (ModuleLanguageInterface $module): LocaleInterface {
                return $module->locale();
            });

        if ($data['lang'] === '') {
            $default = new LocaleEnUs();

            $locale  = Locale::httpAcceptLanguage($request->getServerParams(), $locales->all(), $default);

            $data['lang'] = $locale->languageTag();
        }

        I18N::init($data['lang'], true);

        $data['cpu_limit']    = $this->maxExecutionTime();
        $data['locales']      = $locales->all();
        $data['memory_limit'] = $this->memoryLimit();

        // Only show database errors after the user has chosen a driver.
        if ($step >= 4) {
            $data['errors']   = $this->server_check_service->serverErrors($data['dbtype']);
            $data['warnings'] = $this->server_check_service->serverWarnings($data['dbtype']);
        } else {
            $data['errors']   = $this->server_check_service->serverErrors();
            $data['warnings'] = $this->server_check_service->serverWarnings();
        }

        if (!$this->checkFolderIsWritable(Webtrees::DATA_DIR)) {
            $data['errors']->push(
                '<code>' . e(realpath(Webtrees::DATA_DIR)) . '</code><br>' .
                I18N::translate('Oops! webtrees was unable to create files in this folder.') . ' ' .
                I18N::translate('This usually means that you need to change the folder permissions to 777.')
            );
        }

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
     * @param ServerRequestInterface $request
     *
     * @return array<string,mixed>
     */
    private function userData(ServerRequestInterface $request): array
    {
        $params = (array) $request->getParsedBody();

        $data = [];

        foreach (self::DEFAULT_DATA as $key => $default) {
            $data[$key] = $params[$key] ?? $default;
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

        $number = (int) $memory_limit;

        switch (substr($memory_limit, -1)) {
            case 'g':
            case 'G':
                return $number * 1024;
            case 'm':
            case 'M':
                return $number;
            case 'k':
            case 'K':
                return (int) ($number / 1024);
            default:
                return (int) ($number / 1048576);
        }
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
        $text1 = random_bytes(32);

        try {
            file_put_contents($data_dir . 'test.txt', $text1);
            $text2 = file_get_contents(Webtrees::DATA_DIR . 'test.txt');
            unlink(Webtrees::DATA_DIR . 'test.txt');
        } catch (Exception $ex) {
            return false;
        }

        return $text1 === $text2;
    }

    /**
     * @param array<string,mixed> $data
     *
     * @return ResponseInterface
     */
    private function step1Language(array $data): ResponseInterface
    {
        return $this->viewResponse('setup/step-1-language', $data);
    }

    /**
     * @param array<string,mixed> $data
     *
     * @return ResponseInterface
     */
    private function step2CheckServer(array $data): ResponseInterface
    {
        return $this->viewResponse('setup/step-2-server-checks', $data);
    }

    /**
     * @param array<string,mixed> $data
     *
     * @return ResponseInterface
     */
    private function step3DatabaseType(array $data): ResponseInterface
    {
        if ($data['errors']->isNotEmpty()) {
            return $this->viewResponse('setup/step-2-server-checks', $data);
        }

        return $this->viewResponse('setup/step-3-database-type', $data);
    }

    /**
     * @param array<string,mixed> $data
     *
     * @return ResponseInterface
     */
    private function step4DatabaseConnection(array $data): ResponseInterface
    {
        if ($data['errors']->isNotEmpty()) {
            return $this->step3DatabaseType($data);
        }

        return $this->viewResponse('setup/step-4-database-' . $data['dbtype'], $data);
    }

    /**
     * @param array<string,mixed> $data
     *
     * @return ResponseInterface
     */
    private function step5Administrator(array $data): ResponseInterface
    {
        // Use default port, if none specified.
        $data['dbport'] = $data['dbport'] ?: self::DEFAULT_PORTS[$data['dbtype']];

        try {
            $this->connectToDatabase($data);
        } catch (Throwable $ex) {
            $data['errors']->push($ex->getMessage());

            // Don't jump to step 4, as the error will make it jump to step 3.
            return $this->viewResponse('setup/step-4-database-' . $data['dbtype'], $data);
        }

        return $this->viewResponse('setup/step-5-administrator', $data);
    }

    /**
     * @param array<string,mixed> $data
     *
     * @return ResponseInterface
     */
    private function step6Install(array $data): ResponseInterface
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
        return redirect($data['baseurl']);
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
     * @param array<string,mixed> $data
     *
     * @return void
     */
    private function createConfigFile(array $data): void
    {
        // Create/update the database tables.
        $this->connectToDatabase($data);
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
            $admin = $this->user_service->create($data['wtuser'], $data['wtname'], $data['wtemail'], $data['wtpass']);
            $admin->setPreference(UserInterface::PREF_LANGUAGE, $data['lang']);
            $admin->setPreference(UserInterface::PREF_IS_VISIBLE_ONLINE, '1');
        } else {
            $admin->setPassword($_POST['wtpass']);
        }
        // Make the user an administrator
        $admin->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        $admin->setPreference(UserInterface::PREF_IS_EMAIL_VERIFIED, '1');
        $admin->setPreference(UserInterface::PREF_IS_ACCOUNT_APPROVED, '1');

        // Write the config file. We already checked that this would work.
        $config_ini_php = view('setup/config.ini', $data);

        file_put_contents(Webtrees::CONFIG_FILE, $config_ini_php);

        // Login as the new user
        $request = app(ServerRequestInterface::class)
            ->withAttribute('base_url', $data['baseurl']);

        Session::start($request);
        Auth::login($admin);
        Session::put('language', $data['lang']);
    }

    /**
     * @param array<string,mixed> $data
     *
     * @return void
     */
    private function connectToDatabase(array $data): void
    {
        $capsule = new DB();

        // Try to create the database, if it does not already exist.
        switch ($data['dbtype']) {
            case 'sqlite':
                $data['dbname'] = Webtrees::ROOT_DIR . 'data/' . $data['dbname'] . '.sqlite';
                touch($data['dbname']);
                break;

            case 'mysql':
                $capsule->addConnection([
                    'driver'                  => $data['dbtype'],
                    'host'                    => $data['dbhost'],
                    'port'                    => $data['dbport'],
                    'database'                => '',
                    'username'                => $data['dbuser'],
                    'password'                => $data['dbpass'],
                ], 'temp');
                $capsule->getConnection('temp')->statement('CREATE DATABASE IF NOT EXISTS `' . $data['dbname'] . '` COLLATE utf8_unicode_ci');
                break;
        }

        // Connect to the database.
        $capsule->addConnection([
            'driver'                  => $data['dbtype'],
            'host'                    => $data['dbhost'],
            'port'                    => $data['dbport'],
            'database'                => $data['dbname'],
            'username'                => $data['dbuser'],
            'password'                => $data['dbpass'],
            'prefix'                  => $data['tblpfx'],
            'prefix_indexes'          => true,
            // For MySQL
            'charset'                 => 'utf8',
            'collation'               => 'utf8_unicode_ci',
            'timezone'                => '+00:00',
            'engine'                  => 'InnoDB',
            'modes'                   => [
                'ANSI',
                'STRICT_TRANS_TABLES',
                'NO_ZERO_IN_DATE',
                'NO_ZERO_DATE',
                'ERROR_FOR_DIVISION_BY_ZERO',
            ],
            // For SQLite
            'foreign_key_constraints' => true,
        ]);

        $capsule->setAsGlobal();
    }
}
