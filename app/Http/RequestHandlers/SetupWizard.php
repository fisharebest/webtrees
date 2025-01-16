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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Exception;
use Fisharebest\Localization\Locale;
use Fisharebest\Localization\Locale\LocaleEnUs;
use Fisharebest\Localization\Locale\LocaleInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Factories\CacheFactory;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\ModuleLanguageInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\MigrationService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\PhpService;
use Fisharebest\Webtrees\Services\ServerCheckService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Validator;
use Fisharebest\Webtrees\Webtrees;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

use function e;
use function file_get_contents;
use function file_put_contents;
use function intdiv;
use function random_bytes;
use function realpath;
use function redirect;
use function touch;
use function unlink;
use function view;

/**
 * Controller for the installation wizard
 */
class SetupWizard implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private const string DEFAULT_DBTYPE = DB::MYSQL;
    private const string DEFAULT_PREFIX = 'wt_';
    private const array DEFAULT_DATA    = [
        'baseurl'  => '',
        'lang'     => '',
        'dbtype'   => self::DEFAULT_DBTYPE,
        'dbhost'   => '',
        'dbport'   => '',
        'dbuser'   => '',
        'dbpass'   => '',
        'dbname'   => '',
        'tblpfx'   => self::DEFAULT_PREFIX,
        'dbkey'    => '',
        'dbcert'   => '',
        'dbca'     => '',
        'dbverify' => '',
        'wtname'   => '',
        'wtuser'   => '',
        'wtpass'   => '',
        'wtemail'  => '',
    ];

    private const array DEFAULT_PORTS = [
        DB::MYSQL      => '3306',
        DB::POSTGRES   => '5432',
        DB::SQLITE     => '',
        DB::SQL_SERVER => '', // Do not use default, as it is valid to have no port number.
    ];

    public function __construct(
        private MigrationService $migration_service,
        private ModuleService $module_service,
        private PhpService $php_service,
        private ServerCheckService $server_check_service,
        private UserService $user_service
    ) {
    }

    /**
     * Installation wizard - check user input and proceed to the next step.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/setup';

        // Some functions need a cache, but we don't have one yet.
        Registry::cache(new CacheFactory());

        // We will need an IP address for the logs.
        $ip_address = Validator::serverParams($request)->string('REMOTE_ADDR', '127.0.0.1');
        $request    = $request->withAttribute('client-ip', $ip_address);

        Registry::container()->set(ServerRequestInterface::class, $request);

        $data = $this->userData($request);

        $step = Validator::parsedBody($request)->integer('step', 1);

        $locales = $this->module_service
            ->setupLanguages()
            ->map(static fn (ModuleLanguageInterface $module): LocaleInterface => $module->locale());

        if ($data['lang'] === '') {
            $default = new LocaleEnUs();

            $locale  = Locale::httpAcceptLanguage($request->getServerParams(), $locales->all(), $default);

            $data['lang'] = $locale->languageTag();
        }

        I18N::init($data['lang'], true);

        $data['cpu_limit']    = $this->php_service->maxExecutionTime();
        $data['locales']      = $locales;
        $data['memory_limit'] = intdiv($this->php_service->maxExecutionTime(), 1048576);

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
     * @return array<string,mixed>
     */
    private function userData(ServerRequestInterface $request): array
    {
        $data = [];

        foreach (self::DEFAULT_DATA as $key => $default) {
            $data[$key] = Validator::parsedBody($request)->string($key, $default);
        }

        return $data;
    }

    /**
     * Check we can write to the data folder.
     */
    private function checkFolderIsWritable(string $data_dir): bool
    {
        $text1 = random_bytes(32);

        try {
            file_put_contents($data_dir . 'test.txt', $text1);
            $text2 = file_get_contents(Webtrees::DATA_DIR . 'test.txt');
            unlink(Webtrees::DATA_DIR . 'test.txt');
        } catch (Exception) {
            return false;
        }

        return $text1 === $text2;
    }

    /**
     * @param array<string,mixed> $data
     */
    private function step1Language(array $data): ResponseInterface
    {
        return $this->viewResponse('setup/step-1-language', $data);
    }

    /**
     * @param array<string,mixed> $data
     */
    private function step2CheckServer(array $data): ResponseInterface
    {
        return $this->viewResponse('setup/step-2-server-checks', $data);
    }

    /**
     * @param array<string,mixed> $data
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
     */
    private function step4DatabaseConnection(array $data): ResponseInterface
    {
        if ($data['errors']->isNotEmpty()) {
            return $this->step3DatabaseType($data);
        }

        $data['mysql_local'] = 'localhost:' . $this->php_service->iniGet(option: 'pdo_mysql.default_socket');

        return $this->viewResponse('setup/step-4-database-' . $data['dbtype'], $data);
    }

    /**
     * @param array<string,mixed> $data
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
            $data['mysql_local'] = 'localhost:' . $this->php_service->iniGet(option: 'pdo_mysql.default_socket');

            return $this->viewResponse('setup/step-4-database-' . $data['dbtype'], $data);
        }

        return $this->viewResponse('setup/step-5-administrator', $data);
    }

    /**
     * @param array<string,mixed> $data
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

    private function checkAdminUser(string $wtname, string $wtuser, string $wtpass, string $wtemail): string
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
        $request = Registry::container()->get(ServerRequestInterface::class)
            ->withAttribute('base_url', $data['baseurl']);

        Session::start($request);
        Auth::login($admin);
        Session::put('language', $data['lang']);
    }

    /**
     * @param array<string,mixed> $data
     */
    private function connectToDatabase(array $data): void
    {
        // Try to create the database, if it does not already exist.
        switch ($data['dbtype']) {
            case DB::SQLITE:
                touch(Webtrees::ROOT_DIR . 'data/' . $data['dbname'] . '.sqlite');
                break;

            case DB::MYSQL:
                DB::connect(
                    driver: $data['dbtype'],
                    host: $data['dbhost'],
                    port: $data['dbport'],
                    database: '',
                    username: $data['dbuser'],
                    password: $data['dbpass'],
                    prefix: $data['tblpfx'],
                    key: $data['dbkey'],
                    certificate: $data['dbcert'],
                    ca: $data['dbca'],
                    verify_certificate: (bool) $data['dbverify'],
                );
                DB::exec('CREATE DATABASE IF NOT EXISTS `' . $data['dbname'] . '` COLLATE utf8mb4_unicode_ci');
                break;
        }

        DB::connect(
            driver: $data['dbtype'],
            host: $data['dbhost'],
            port: $data['dbport'],
            database: $data['dbname'],
            username: $data['dbuser'],
            password: $data['dbpass'],
            prefix: $data['tblpfx'],
            key: $data['dbkey'],
            certificate: $data['dbcert'],
            ca: $data['dbca'],
            verify_certificate: (bool) $data['dbverify'],
        );
    }
}
