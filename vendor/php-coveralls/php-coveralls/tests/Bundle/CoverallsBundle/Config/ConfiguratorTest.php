<?php

namespace PhpCoveralls\Tests\Bundle\CoverallsBundle\Config;

use PhpCoveralls\Bundle\CoverallsBundle\Config\Configuration;
use PhpCoveralls\Bundle\CoverallsBundle\Config\Configurator;
use PhpCoveralls\Tests\ProjectTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

/**
 * @covers \PhpCoveralls\Bundle\CoverallsBundle\Config\Configurator
 * @covers \PhpCoveralls\Bundle\CoverallsBundle\Config\CoverallsConfiguration
 *
 * @author Kitamura Satoshi <with.no.parachute@gmail.com>
 */
class ConfiguratorTest extends ProjectTestCase
{
    /**
     * @var Configurator
     */
    private $object;

    // load()

    /**
     * @test
     */
    public function shouldLoadNonExistingYml()
    {
        $this->makeProjectDir($this->srcDir, $this->logsDir, $this->cloverXmlPath);

        $path = realpath(__DIR__ . '/yaml/dummy.yml');

        $config = $this->object->load($path, $this->rootDir);

        $this->assertConfiguration($config, [$this->cloverXmlPath], $this->jsonPath);
    }

    // default src_dir not found, it doesn't throw anything now, as src_dir is not required for configuration

    /**
     * @test
     */
    public function loadConfigurationOnLoadEmptyYmlWhenSrcDirNotFound()
    {
        $this->makeProjectDir(null, $this->logsDir, $this->cloverXmlPath);

        $path = realpath(__DIR__ . '/yaml/dummy.yml');

        $config = $this->object->load($path, $this->rootDir);

        $this->assertInstanceOf('PhpCoveralls\Bundle\CoverallsBundle\Config\Configuration', $config);
    }

    // default coverage_clover not found

    /**
     * @test
     */
    public function throwInvalidConfigurationExceptionOnLoadEmptyYmlIfCoverageCloverNotFound()
    {
        $this->expectException(\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException::class);

        $this->makeProjectDir($this->srcDir, $this->logsDir, null);

        $path = realpath(__DIR__ . '/yaml/dummy.yml');

        $this->object->load($path, $this->rootDir);
    }

    // default json_path not writable

    /**
     * @test
     */
    public function throwInvalidConfigurationExceptionOnLoadEmptyYmlIfJsonPathDirNotWritable()
    {
        $this->expectException(\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException::class);

        if ($this->isWindowsOS()) {
            // On Windows read-only attribute on dir applies to files in dir, but not the dir itself.
            $this->markTestSkipped('Unable to run on Windows');
        }

        $this->makeProjectDir($this->srcDir, $this->logsDir, $this->cloverXmlPath, true);

        $path = realpath(__DIR__ . '/yaml/dummy.yml');

        $this->object->load($path, $this->rootDir);
    }

    /**
     * @test
     */
    public function throwInvalidConfigurationExceptionOnLoadEmptyYmlIfJsonPathNotWritable()
    {
        $this->expectException(\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException::class);

        $this->makeProjectDir($this->srcDir, $this->logsDir, $this->cloverXmlPath, false, true);

        $path = realpath(__DIR__ . '/yaml/dummy.yml');

        $this->object->load($path, $this->rootDir);
    }

    // no configuration

    /**
     * @test
     */
    public function shouldLoadEmptyYml()
    {
        $this->makeProjectDir($this->srcDir, $this->logsDir, $this->cloverXmlPath);

        $path = realpath(__DIR__ . '/yaml/empty.yml');

        $config = $this->object->load($path, $this->rootDir);

        $this->assertConfiguration($config, [$this->cloverXmlPath], $this->jsonPath);
    }

    // load default value yml

    /**
     * @test
     */
    public function shouldThrowInvalidConfigurationExceptionUponLoadingSrcDirYml()
    {
        $this->expectException(\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException::class);

        $this->makeProjectDir($this->srcDir, $this->logsDir, $this->cloverXmlPath);

        $path = realpath(__DIR__ . '/yaml/src_dir.yml');

        $this->object->load($path, $this->rootDir);
    }

    /**
     * @test
     */
    public function shouldLoadCoverageCloverYmlContainingDefaultValue()
    {
        $this->makeProjectDir($this->srcDir, $this->logsDir, $this->cloverXmlPath);

        $path = realpath(__DIR__ . '/yaml/coverage_clover.yml');

        $config = $this->object->load($path, $this->rootDir);

        $this->assertConfiguration($config, [$this->cloverXmlPath], $this->jsonPath);
    }

    /**
     * @test
     */
    public function shouldLoadCoverageCloverOverriddenByInput()
    {
        $this->makeProjectDir($this->srcDir, $this->logsDir, [$this->cloverXmlPath1, $this->cloverXmlPath2]);

        $path = realpath(__DIR__ . '/yaml/coverage_clover.yml');

        // Mocking command line options.
        $defs = new InputDefinition(
            [
                new InputOption(
                    'coverage_clover',
                    'x',
                    InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY
                ),
            ]
        );
        $inputArray = [
            '--coverage_clover' => [
                'build/logs/clover-part1.xml',
                'build/logs/clover-part2.xml',
            ],
        ];
        $input = new ArrayInput($inputArray, $defs);
        $config = $this->object->load($path, $this->rootDir, $input);
        $this->assertConfiguration($config, [$this->cloverXmlPath1, $this->cloverXmlPath2], $this->jsonPath);
    }

    /**
     * @test
     */
    public function shouldLoadCoverageCloverYmlContainingGlobValue()
    {
        $this->makeProjectDir($this->srcDir, $this->logsDir, [$this->cloverXmlPath1, $this->cloverXmlPath2]);

        $path = realpath(__DIR__ . '/yaml/coverage_clover_glob.yml');

        $config = $this->object->load($path, $this->rootDir);

        $this->assertConfiguration($config, [$this->cloverXmlPath1, $this->cloverXmlPath2], $this->jsonPath);
    }

    /**
     * @test
     */
    public function shouldLoadCoverageCloverYmlContainingArrayValue()
    {
        $this->makeProjectDir($this->srcDir, $this->logsDir, [$this->cloverXmlPath1, $this->cloverXmlPath2]);

        $path = realpath(__DIR__ . '/yaml/coverage_clover_array.yml');

        $config = $this->object->load($path, $this->rootDir);

        $this->assertConfiguration($config, [$this->cloverXmlPath1, $this->cloverXmlPath2], $this->jsonPath);
    }

    /**
     * @test
     */
    public function shouldLoadJsonPathYmlContainingDefaultValue()
    {
        $this->makeProjectDir($this->srcDir, $this->logsDir, $this->cloverXmlPath);

        $path = realpath(__DIR__ . '/yaml/json_path.yml');

        $config = $this->object->load($path, $this->rootDir);

        $this->assertConfiguration($config, [$this->cloverXmlPath], $this->jsonPath);
    }

    /**
     * @test
     */
    public function shouldLoadJsonPathOverriddenByInput()
    {
        $this->makeProjectDir($this->srcDir, $this->logsDir, $this->cloverXmlPath);

        $path = realpath(__DIR__ . '/yaml/json_path.yml');

        // Mocking command line options.
        $defs = new InputDefinition(
            [
                new InputOption(
                    'json_path',
                    'o',
                    InputOption::VALUE_REQUIRED
                ),
            ]
        );

        $inputArray = [
            '--json_path' => 'build/logs/coveralls-upload-custom.json',
        ];
        $expectedJsonPath = substr($this->jsonPath, 0, strlen($this->jsonPath) - 5) . '-custom.json';

        $input = new ArrayInput($inputArray, $defs);
        $config = $this->object->load($path, $this->rootDir, $input);
        $this->assertConfiguration($config, [$this->cloverXmlPath], $expectedJsonPath);
    }

    /**
     * @test
     */
    public function shouldLoadExcludeNoStmtYmlContainingTrue()
    {
        $this->makeProjectDir($this->srcDir, $this->logsDir, $this->cloverXmlPath);

        $path = realpath(__DIR__ . '/yaml/exclude_no_stmt_true.yml');

        $config = $this->object->load($path, $this->rootDir);

        $this->assertConfiguration($config, [$this->cloverXmlPath], $this->jsonPath, true);
    }

    /**
     * @test
     */
    public function shouldLoadExcludeNoStmtYmlContainingFalse()
    {
        $this->makeProjectDir($this->srcDir, $this->logsDir, $this->cloverXmlPath);

        $path = realpath(__DIR__ . '/yaml/exclude_no_stmt_false.yml');

        $config = $this->object->load($path, $this->rootDir);

        $this->assertConfiguration($config, [$this->cloverXmlPath], $this->jsonPath, false);
    }

    /**
     * @test
     */
    public function shouldLoadEntryPoint()
    {
        $this->makeProjectDir($this->srcDir, $this->logsDir, $this->cloverXmlPath);

        $path = realpath(__DIR__ . '/yaml/entry_point.yml');

        $config = $this->object->load($path, $this->rootDir);

        $this->assertSame('http://foo.bar', $config->getEntryPoint());
    }

    /**
     * @test
     */
    public function shouldLoadUseDefaultEntryPointIfNotSet()
    {
        $this->makeProjectDir($this->srcDir, $this->logsDir, $this->cloverXmlPath);

        $path = realpath(__DIR__ . '/yaml/empty.yml');

        $config = $this->object->load($path, $this->rootDir);

        $this->assertSame('https://coveralls.io', $config->getEntryPoint());
    }

    // configured coverage_clover not found

    /**
     * @test
     */
    public function throwInvalidConfigurationExceptionOnLoadCoverageCloverYmlIfCoverageCloverNotFound()
    {
        $this->expectException(\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException::class);

        $this->makeProjectDir($this->srcDir, $this->logsDir, $this->cloverXmlPath);

        $path = realpath(__DIR__ . '/yaml/coverage_clover_not_found.yml');

        $this->object->load($path, $this->rootDir);
    }

    /**
     * @test
     */
    public function throwInvalidConfigurationExceptionOnLoadCoverageCloverYmlIfCoverageCloverIsNotString()
    {
        $this->expectException(\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException::class);

        $this->makeProjectDir($this->srcDir, $this->logsDir, $this->cloverXmlPath);

        $path = realpath(__DIR__ . '/yaml/coverage_clover_invalid.yml');

        $this->object->load($path, $this->rootDir);
    }

    // configured json_path not found

    /**
     * @test
     */
    public function throwInvalidConfigurationExceptionOnLoadJsonPathYmlIfJsonPathNotFound()
    {
        $this->expectException(\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException::class);

        $this->makeProjectDir($this->srcDir, $this->logsDir, $this->cloverXmlPath);

        $path = realpath(__DIR__ . '/yaml/json_path_not_found.yml');

        $this->object->load($path, $this->rootDir);
    }

    // configured exclude_no_stmt invalid

    /**
     * @test
     */
    public function throwInvalidConfigurationExceptionOnLoadExcludeNoStmtYmlIfInvalid()
    {
        if (PHP_VERSION_ID >= 80000 && !function_exists('get_debug_type')) {
            $this->markTestIncomplete('get_debug_type() is not available yet');
        }

        $this->expectException(\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException::class);

        $this->makeProjectDir($this->srcDir, $this->logsDir, $this->cloverXmlPath);

        $path = realpath(__DIR__ . '/yaml/exclude_no_stmt_invalid.yml');

        $this->object->load($path, $this->rootDir);
    }

    protected function legacySetUp()
    {
        $this->setUpDir(realpath(__DIR__ . '/../../..'));

        $this->srcDir = $this->rootDir . '/src';

        $this->object = new Configurator();
    }

    protected function legacyTearDown()
    {
        $this->rmFile($this->cloverXmlPath);
        $this->rmFile($this->cloverXmlPath1);
        $this->rmFile($this->cloverXmlPath2);
        $this->rmFile($this->jsonPath);
        $this->rmDir($this->srcDir);
        $this->rmDir($this->logsDir);
        $this->rmDir($this->buildDir);
    }

    // custom assertion

    /**
     * @param Configuration $config
     * @param array         $cloverXml
     * @param string        $jsonPath
     * @param bool          $excludeNoStatements
     */
    protected function assertConfiguration(Configuration $config, array $cloverXml, $jsonPath, $excludeNoStatements = false)
    {
        $this->assertSamePaths($cloverXml, $config->getCloverXmlPaths());
        $this->assertSamePath($jsonPath, $config->getJsonPath());
        $this->assertSame($excludeNoStatements, $config->isExcludeNoStatements());
    }

    /**
     * @return bool
     */
    private function isWindowsOS()
    {
        static $isWindows;

        if ($isWindows === null) {
            $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        }

        return $isWindows;
    }
}
