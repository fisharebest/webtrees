<?php

/**
 * An example module to modify PHP and database configuration.
 */

declare(strict_types=1);

namespace MyCustomNamespace;

use Fisharebest\Webtrees\Module\AbstractModule;
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleCustomTrait;
use Fisharebest\Webtrees\Services\ServerCheckService;
use Illuminate\Database\Capsule\Manager as DB;

class ExampleServerConfigurationModule extends AbstractModule implements ModuleCustomInterface
{
    use ModuleCustomTrait;

    /** @var ServerCheckService */
    private $server_check_service;

    /**
     *  Constructor.
     *
     * @param ServerCheckService $server_check_service
     */
    public function __construct(ServerCheckService $server_check_service)
    {
        $this->server_check_service = $server_check_service;
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        return 'Server configuration';
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Modify the server configuration';
    }

    /**
     * The person or organisation who created this module.
     *
     * @return string
     */
    public function customModuleAuthorName(): string
    {
        return 'Your name';
    }

    /**
     * If you do not have access to the PHP.INI or MYSQL.CNF files on your server, then
     * you may be able to change them.
     */
    public function boot(): void
    {
        // IMPORTANT - not all servers allow you to change these settings.  Sometimes, even
        // attempting to change them can result in your script being terminated immediately.
        // We attempt to detect whether this will happen, but it is not possible to
        // do so with 100% accuracy.

        if (!$this->server_check_service->isFunctionDisabled('ini_set')) {
            $this->phpIni();
        }

        if (!$this->server_check_service->isFunctionDisabled('set_time_limit')) {
            $this->phpTimeLimit();
        }

        if (!$this->server_check_service->isFunctionDisabled('putenv')) {
            $this->phpEnvironment();
        }

        if (DB::connection()->getDriverName() === 'mysql') {
            $this->mysql();
        }
    }

    /**
     * Modify the PHP time limit.
     */
    private function phpTimeLimit(): void
    {
        // Set the time limit for PHP scripts.
        // Recommended settings are between 15 and 60 seconds.
        //
        // Typical webservers will not wait more than 60 seconds for a PHP response,
        // so it is pointless to allow the server to continue using resources for
        // a request that will be ignored.

        //set_time_limit(45);
    }

    /**
     * Modify the PHP environment variables.
     */
    private function phpEnvironment(): void
    {
        // Some servers block access to the system temporary folder using open_basedir...
        //
        // Create a temporary folder somewhere we have read/write access, and tell PHP to use it.
        //$tmp = __DIR__ . '/../../data/tmp';
        //if (!is_dir($tmp)) {
        //    mkdir($tmp);
        //}
        //putenv('TMPDIR=' . $tmp);
    }

    /**
     * Modify the PHP.INI settings.
     */
    private function phpIni(): void
    {
        // Set the maximum amount of memory that PHP scripts can use.
        // Recommended settings are between 128M and 1024M

        //ini_set('memory_limit', '256M');
    }

    /**
     * Modify the MySQL connection.
     */
    private function mysql(): void
    {
        // If you get the error "The SELECT would examine more than MAX_JOIN_SIZE rows",
        // then setting this option may help.

        //DB::statement('SET SESSION sql_big_selects := 1');
    }
}
