<?php

namespace PhpCoveralls\Tests\Bundle\CoverallsBundle\Collector;

use PhpCoveralls\Bundle\CoverallsBundle\Collector\CiEnvVarsCollector;
use PhpCoveralls\Bundle\CoverallsBundle\Config\Configuration;
use PhpCoveralls\Tests\ProjectTestCase;

/**
 * @covers \PhpCoveralls\Bundle\CoverallsBundle\Collector\CiEnvVarsCollector
 *
 * @author Kitamura Satoshi <with.no.parachute@gmail.com>
 */
class CiEnvVarsCollectorTest extends ProjectTestCase
{
    // collect()

    /**
     * @test
     */
    public function shouldCollectTravisCiEnvVars()
    {
        $serviceName = 'travis-ci';
        $serviceJobId = '1.1';
        $serviceBuildNumber = '123456';

        $env = [];
        $env['TRAVIS'] = true;
        $env['TRAVIS_JOB_ID'] = $serviceJobId;
        $env['TRAVIS_BUILD_NUMBER'] = $serviceBuildNumber;

        $object = $this->createCiEnvVarsCollector();

        $actual = $object->collect($env);

        $this->assertArrayHasKey('CI_NAME', $actual);
        $this->assertSame($serviceName, $actual['CI_NAME']);

        $this->assertArrayHasKey('CI_JOB_ID', $actual);
        $this->assertSame($serviceJobId, $actual['CI_JOB_ID']);

        $this->assertArrayHasKey('CI_BUILD_NUMBER', $actual);
        $this->assertSame($serviceBuildNumber, $actual['CI_BUILD_NUMBER']);

        return $object;
    }

    /**
     * @test
     */
    public function shouldCollectTravisProEnvVars()
    {
        $serviceName = 'travis-pro';
        $serviceJobId = '1.2';
        $serviceBuildNumber = '12345';
        $repoToken = 'your_token';

        $env = [];
        $env['TRAVIS'] = true;
        $env['TRAVIS_JOB_ID'] = $serviceJobId;
        $env['TRAVIS_BUILD_NUMBER'] = $serviceBuildNumber;
        $env['COVERALLS_REPO_TOKEN'] = $repoToken;

        $config = $this->createConfiguration();
        $config->setServiceName($serviceName);

        $object = $this->createCiEnvVarsCollector($config);

        $actual = $object->collect($env);

        $this->assertArrayHasKey('CI_NAME', $actual);
        $this->assertSame($serviceName, $actual['CI_NAME']);

        $this->assertArrayHasKey('CI_JOB_ID', $actual);
        $this->assertSame($serviceJobId, $actual['CI_JOB_ID']);

        $this->assertArrayHasKey('CI_BUILD_NUMBER', $actual);
        $this->assertSame($serviceBuildNumber, $actual['CI_BUILD_NUMBER']);

        $this->assertArrayHasKey('COVERALLS_REPO_TOKEN', $actual);
        $this->assertSame($repoToken, $actual['COVERALLS_REPO_TOKEN']);

        return $object;
    }

    /**
     * @test
     */
    public function shouldCollectCircleCiEnvVars()
    {
        $serviceName = 'circleci';
        $serviceNumber = '123';

        $env = [];
        $env['COVERALLS_REPO_TOKEN'] = 'token';
        $env['CIRCLECI'] = 'true';
        $env['CIRCLE_WORKFLOW_ID'] = $serviceNumber;

        $object = $this->createCiEnvVarsCollector();

        $actual = $object->collect($env);

        $this->assertArrayHasKey('CI_NAME', $actual);
        $this->assertSame($serviceName, $actual['CI_NAME']);

        $this->assertArrayHasKey('CI_BUILD_NUMBER', $actual);
        $this->assertSame($serviceNumber, $actual['CI_BUILD_NUMBER']);

        return $object;
    }

    /**
     * @test
     */
    public function shouldCollectJenkinsEnvVars()
    {
        $serviceName = 'jenkins';
        $serviceNumber = '123';
        $buildUrl = 'http://localhost:8080';

        $env = [];
        $env['COVERALLS_REPO_TOKEN'] = 'token';
        $env['JENKINS_URL'] = $buildUrl;
        $env['BUILD_NUMBER'] = $serviceNumber;

        $object = $this->createCiEnvVarsCollector();

        $actual = $object->collect($env);

        $this->assertArrayHasKey('CI_NAME', $actual);
        $this->assertSame($serviceName, $actual['CI_NAME']);

        $this->assertArrayHasKey('CI_BUILD_NUMBER', $actual);
        $this->assertSame($serviceNumber, $actual['CI_BUILD_NUMBER']);

        $this->assertArrayHasKey('CI_BUILD_URL', $actual);
        $this->assertSame($buildUrl, $actual['CI_BUILD_URL']);

        return $object;
    }

    /**
     * @test
     */
    public function shouldCollectGithubActionsEnvVars()
    {
        $serviceName = 'github';
        $jobId = '275038505';

        $env = [];
        $env['COVERALLS_REPO_TOKEN'] = 'token';
        $env['GITHUB_ACTIONS'] = true;
        $env['GITHUB_EVENT_NAME'] = 'push';
        $env['GITHUB_REF'] = 'refs/heads/master';
        $env['GITHUB_RUN_ID'] = '275038505';

        $object = $this->createCiEnvVarsCollector();

        $actual = $object->collect($env);

        $this->assertArrayHasKey('CI_NAME', $actual);
        $this->assertSame($serviceName, $actual['CI_NAME']);

        $this->assertArrayHasKey('CI_JOB_ID', $actual);
        $this->assertSame($jobId, $actual['CI_JOB_ID']);

        $this->assertArrayHasKey('CI_BRANCH', $actual);
        $this->assertSame('master', $actual['CI_BRANCH']);

        return $object;
    }

    /**
     * @test
     */
    public function shouldCollectGithubActionsEnvVarsForPullRequest()
    {
        $serviceName = 'github';

        $env = [];
        $env['COVERALLS_REPO_TOKEN'] = 'token';
        $env['GITHUB_ACTIONS'] = true;
        $env['GITHUB_EVENT_NAME'] = 'pull_request';
        $env['GITHUB_REF'] = 'refs/pull/1/merge';
        $env['GITHUB_RUN_ID'] = '275038505';

        $object = $this->createCiEnvVarsCollector();

        $actual = $object->collect($env);

        $this->assertArrayHasKey('CI_NAME', $actual);
        $this->assertSame($serviceName, $actual['CI_NAME']);

        $this->assertArrayHasKey('CI_JOB_ID', $actual);
        $this->assertSame('275038505', $actual['CI_JOB_ID']);

        $this->assertArrayHasKey('CI_BRANCH', $actual);
        $this->assertSame('refs/pull/1/merge', $actual['CI_BRANCH']);

        return $object;
    }

    /**
     * @test
     */
    public function shouldCollectGithubActionsEnvVarsForTag()
    {
        $serviceName = 'github';

        $env = [];
        $env['COVERALLS_REPO_TOKEN'] = 'token';
        $env['GITHUB_ACTIONS'] = true;
        $env['GITHUB_EVENT_NAME'] = 'push';
        $env['GITHUB_REF'] = 'refs/tags/v123.456.789';
        $env['GITHUB_RUN_ID'] = '275038505';

        $object = $this->createCiEnvVarsCollector();

        $actual = $object->collect($env);

        $this->assertArrayHasKey('CI_NAME', $actual);
        $this->assertSame($serviceName, $actual['CI_NAME']);

        $this->assertArrayHasKey('CI_JOB_ID', $actual);
        $this->assertSame('275038505', $actual['CI_JOB_ID']);

        $this->assertArrayHasKey('CI_BRANCH', $actual);
        $this->assertSame('v123.456.789', $actual['CI_BRANCH']);

        return $object;
    }

    /**
     * @test
     */
    public function shouldCollectLocalEnvVars()
    {
        $serviceName = 'php-coveralls';
        $serviceEventType = 'manual';

        $env = [];
        $env['COVERALLS_REPO_TOKEN'] = 'token';
        $env['COVERALLS_RUN_LOCALLY'] = '1';

        $object = $this->createCiEnvVarsCollector();

        $actual = $object->collect($env);

        $this->assertArrayHasKey('CI_NAME', $actual);
        $this->assertSame($serviceName, $actual['CI_NAME']);

        $this->assertArrayHasKey('COVERALLS_EVENT_TYPE', $actual);
        $this->assertSame($serviceEventType, $actual['COVERALLS_EVENT_TYPE']);

        $this->assertArrayHasKey('CI_JOB_ID', $actual);
        $this->assertNull($actual['CI_JOB_ID']);

        return $object;
    }

    /**
     * @test
     */
    public function shouldCollectUnsupportedConfig()
    {
        $repoToken = 'token';

        $env = [];

        $config = $this->createConfiguration();
        $config->setRepoToken($repoToken);

        $object = $this->createCiEnvVarsCollector($config);

        $actual = $object->collect($env);

        $this->assertArrayHasKey('COVERALLS_REPO_TOKEN', $actual);
        $this->assertSame($repoToken, $actual['COVERALLS_REPO_TOKEN']);

        return $object;
    }

    /**
     * @test
     */
    public function shouldCollectUnsupportedEnvVars()
    {
        $repoToken = 'token';

        $env = [];
        $env['COVERALLS_REPO_TOKEN'] = $repoToken;

        $object = $this->createCiEnvVarsCollector();

        $actual = $object->collect($env);

        $this->assertArrayHasKey('COVERALLS_REPO_TOKEN', $actual);
        $this->assertSame($repoToken, $actual['COVERALLS_REPO_TOKEN']);

        return $object;
    }

    /**
     * @test
     */
    public function shouldCollectParallel()
    {
        $parallel = true;
        $flagName = 'php-7.4';

        $env = [];
        $env['COVERALLS_PARALLEL'] = $parallel;
        $env['COVERALLS_FLAG_NAME'] = $flagName;

        $object = $this->createCiEnvVarsCollector();

        $actual = $object->collect($env);

        $this->assertArrayHasKey('COVERALLS_PARALLEL', $actual);
        $this->assertSame($parallel, $actual['COVERALLS_PARALLEL']);

        $this->assertArrayHasKey('COVERALLS_FLAG_NAME', $actual);
        $this->assertSame($flagName, $actual['COVERALLS_FLAG_NAME']);

        return $object;
    }

    // getReadEnv()

    /**
     * @test
     */
    public function shouldNotHaveReadEnvOnConstruction()
    {
        $object = $this->createCiEnvVarsCollector();

        $this->assertNull($object->getReadEnv());
    }

    /**
     * @test
     * @depends shouldCollectTravisCiEnvVars
     *
     * @param CiEnvVarsCollector $object
     */
    public function shouldHaveReadEnvAfterCollectTravisCiEnvVars(CiEnvVarsCollector $object)
    {
        $readEnv = $object->getReadEnv();

        $this->assertCount(4, $readEnv);
        $this->assertArrayHasKey('TRAVIS', $readEnv);
        $this->assertArrayHasKey('TRAVIS_JOB_ID', $readEnv);
        $this->assertArrayHasKey('CI_NAME', $readEnv);
        $this->assertArrayHasKey('CI_BUILD_NUMBER', $readEnv);
    }

    /**
     * @test
     * @depends shouldCollectGithubActionsEnvVars
     *
     * @param CiEnvVarsCollector $object
     */
    public function shouldHaveReadEnvAfterCollectGithubActionsEnvVars(CiEnvVarsCollector $object)
    {
        $readEnv = $object->getReadEnv();

        $this->assertCount(6, $readEnv);
        $this->assertArrayHasKey('GITHUB_REF', $readEnv);
        $this->assertArrayHasKey('CI_NAME', $readEnv);
        $this->assertArrayHasKey('CI_JOB_ID', $readEnv);
        $this->assertArrayHasKey('GITHUB_ACTIONS', $readEnv);
        $this->assertArrayHasKey('CI_BRANCH', $readEnv);
        $this->assertArrayHasKey('COVERALLS_REPO_TOKEN', $readEnv);
    }

    /**
     * @test
     * @depends shouldCollectTravisProEnvVars
     *
     * @param CiEnvVarsCollector $object
     */
    public function shouldHaveReadEnvAfterCollectTravisProEnvVars(CiEnvVarsCollector $object)
    {
        $readEnv = $object->getReadEnv();

        $this->assertCount(5, $readEnv);
        $this->assertArrayHasKey('TRAVIS', $readEnv);
        $this->assertArrayHasKey('TRAVIS_JOB_ID', $readEnv);
        $this->assertArrayHasKey('CI_NAME', $readEnv);
        $this->assertArrayHasKey('CI_BUILD_NUMBER', $readEnv);
        $this->assertArrayHasKey('COVERALLS_REPO_TOKEN', $readEnv);
    }

    /**
     * @test
     * @depends shouldCollectCircleCiEnvVars
     *
     * @param CiEnvVarsCollector $object
     */
    public function shouldHaveReadEnvAfterCollectCircleCiEnvVars(CiEnvVarsCollector $object)
    {
        $readEnv = $object->getReadEnv();

        $this->assertCount(4, $readEnv);
        $this->assertArrayHasKey('COVERALLS_REPO_TOKEN', $readEnv);
        $this->assertArrayHasKey('CIRCLECI', $readEnv);
        $this->assertArrayHasKey('CIRCLE_WORKFLOW_ID', $readEnv);
        $this->assertArrayHasKey('CI_NAME', $readEnv);
    }

    /**
     * @test
     * @depends shouldCollectJenkinsEnvVars
     *
     * @param CiEnvVarsCollector $object
     */
    public function shouldHaveReadEnvAfterCollectJenkinsEnvVars(CiEnvVarsCollector $object)
    {
        $readEnv = $object->getReadEnv();

        $this->assertCount(4, $readEnv);
        $this->assertArrayHasKey('COVERALLS_REPO_TOKEN', $readEnv);
        $this->assertArrayHasKey('JENKINS_URL', $readEnv);
        $this->assertArrayHasKey('BUILD_NUMBER', $readEnv);
        $this->assertArrayHasKey('CI_NAME', $readEnv);
    }

    /**
     * @test
     * @depends shouldCollectLocalEnvVars
     *
     * @param CiEnvVarsCollector $object
     */
    public function shouldHaveReadEnvAfterCollectLocalEnvVars(CiEnvVarsCollector $object)
    {
        $readEnv = $object->getReadEnv();

        $this->assertCount(4, $readEnv);
        $this->assertArrayHasKey('COVERALLS_REPO_TOKEN', $readEnv);
        $this->assertArrayHasKey('COVERALLS_RUN_LOCALLY', $readEnv);
        $this->assertArrayHasKey('COVERALLS_EVENT_TYPE', $readEnv);
        $this->assertArrayHasKey('CI_NAME', $readEnv);
    }

    /**
     * @test
     * @depends shouldCollectUnsupportedConfig
     *
     * @param CiEnvVarsCollector $object
     */
    public function shouldHaveReadEnvAfterCollectUnsupportedConfig(CiEnvVarsCollector $object)
    {
        $readEnv = $object->getReadEnv();

        $this->assertCount(1, $readEnv);
        $this->assertArrayHasKey('COVERALLS_REPO_TOKEN', $readEnv);
    }

    /**
     * @test
     * @depends shouldCollectUnsupportedEnvVars
     *
     * @param CiEnvVarsCollector $object
     */
    public function shouldHaveReadEnvAfterCollectUnsupportedEnvVars(CiEnvVarsCollector $object)
    {
        $readEnv = $object->getReadEnv();

        $this->assertCount(1, $readEnv);
        $this->assertArrayHasKey('COVERALLS_REPO_TOKEN', $readEnv);
    }

    protected function legacySetUp()
    {
        $this->setUpDir(realpath(__DIR__ . '/../../..'));
    }

    /**
     * @return Configuration
     */
    protected function createConfiguration()
    {
        $config = new Configuration();

        return $config
        ->addCloverXmlPath($this->cloverXmlPath);
    }

    /**
     * @param null|Configuration $config
     *
     * @return CiEnvVarsCollector
     */
    protected function createCiEnvVarsCollector($config = null)
    {
        if ($config === null) {
            $config = $this->createConfiguration();
        }

        return new CiEnvVarsCollector($config);
    }
}
