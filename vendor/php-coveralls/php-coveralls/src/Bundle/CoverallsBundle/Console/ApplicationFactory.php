<?php

namespace PhpCoveralls\Bundle\CoverallsBundle\Console;

use PhpCoveralls\Bundle\CoverallsBundle\Command\CoverallsJobsCommand;
use PhpCoveralls\Bundle\CoverallsBundle\Console\Application as CoverallsApplication;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\SingleCommandApplication;

/**
 * In symfony/console 3.1 and lower provides only one way to realize single command application.
 * It is possible by extending the {@see Application}
 * Since symfony/console 6 applied strict typing which makes impossible to extending.
 * Since symfony/console 3.1 added public method setDefaultCommand()
 * Since symfony/console 3.2 method setDefaultCommand() accepts a boolean as second parameter, so we can build
 * single command application without extending.
 * But we can't rely on condition is method setDefaultCommand exists to apply new way without extending
 * Since symfony/console 5.1 introduced new class {@see SingleCommandApplication}. But we can't use that without
 * huge code duplication from the {@see CoverallsJobsCommand}.
 * Nevertheless, when SingleCommandApplication exists we can be sure - setDefaultCommand can be applied.
 *
 * @see https://symfony.com/doc/3.1/components/console/single_command_tool.html
 * @see https://symfony.com/doc/3.2/components/console/single_command_tool.html
 * @see https://symfony.com/doc/5.1/components/console/single_command_tool.html
 *
 * @author Viktor Linkin <adrenalinkin@gmail.com>
 */
class ApplicationFactory
{
    const APP_NAME = 'PHP client library for Coveralls API';
    const APP_VERSION = '@package_version@';

    /**
     * @param string $rootDir
     *
     * @return Application
     */
    public static function create($rootDir)
    {
        $command = new CoverallsJobsCommand();
        $command->setRootDir($rootDir);

        if (\class_exists(SingleCommandApplication::class) === false) {
            $application = new CoverallsApplication(self::APP_NAME, self::APP_VERSION);
            $application->add($command);

            return $application;
        }

        $application = new Application(self::APP_NAME, self::APP_VERSION);
        $application->add($command);
        $application->setDefaultCommand($command->getName(), true);

        return $application;
    }
}
