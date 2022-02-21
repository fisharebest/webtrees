<?php

namespace PhpCoveralls\Bundle\CoverallsBundle\Collector;

use PhpCoveralls\Bundle\CoverallsBundle\Config\Configuration;

/**
 * Environment variables collector for CI envrionment.
 *
 * @author Kitamura Satoshi <with.no.parachute@gmail.com>
 */
class CiEnvVarsCollector
{
    /**
     * Configuration.
     *
     * @var Configuration
     */
    protected $config;

    /**
     * Environment variables.
     *
     * Overwritten through collection process.
     *
     * @var array
     */
    protected $env;

    /**
     * Read environment variables.
     *
     * @var array
     */
    protected $readEnv;

    /**
     * Constructor.
     *
     * @param Configuration $config configuration
     */
    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    // API

    /**
     * Collect environment variables.
     *
     * @param array $env $_SERVER environment
     *
     * @return array
     */
    public function collect(array $env)
    {
        $this->env = $env;
        $this->readEnv = [];

        $this
            ->fillTravisCi()
            ->fillCircleCi()
            ->fillAppVeyor()
            ->fillJenkins()
            ->fillGithubActions()
            ->fillLocal()
            ->fillRepoToken()
            ->fillParallel();

        return $this->env;
    }

    // accessor

    /**
     * Return read environment variables.
     *
     * @return array
     */
    public function getReadEnv()
    {
        return $this->readEnv;
    }

    // internal method

    /**
     * Fill Travis CI environment variables.
     *
     * "TRAVIS", "TRAVIS_BUILD_NUMBER", TRAVIS_JOB_ID" must be set.
     *
     * @return $this
     */
    protected function fillTravisCi()
    {
        if (isset($this->env['TRAVIS']) && $this->env['TRAVIS'] && isset($this->env['TRAVIS_JOB_ID']) && isset($this->env['TRAVIS_BUILD_NUMBER'])) {
            $this->env['CI_JOB_ID'] = $this->env['TRAVIS_JOB_ID'];
            $this->env['CI_BUILD_NUMBER'] = $this->env['TRAVIS_BUILD_NUMBER'];

            if ($this->config->hasServiceName()) {
                $this->env['CI_NAME'] = $this->config->getServiceName();
            } else {
                $this->env['CI_NAME'] = 'travis-ci';
            }

            // backup
            $this->readEnv['TRAVIS'] = $this->env['TRAVIS'];
            $this->readEnv['TRAVIS_JOB_ID'] = $this->env['TRAVIS_JOB_ID'];
            $this->readEnv['CI_NAME'] = $this->env['CI_NAME'];
            $this->readEnv['CI_BUILD_NUMBER'] = $this->env['CI_BUILD_NUMBER'];
        }

        return $this;
    }

    /**
     * Fill Github Actions environment variables.
     *
     * @return $this
     */
    protected function fillGithubActions()
    {
        if (!isset($this->env['GITHUB_ACTIONS'])) {
            return $this;
        }
        $this->env['CI_NAME'] = 'github';

        $githubEventName = $this->env['GITHUB_EVENT_NAME'];
        $githubRef = $this->env['GITHUB_REF'];

        if (strpos($githubRef, 'refs/heads/') !== false) {
            $githubRef = str_replace('refs/heads/', '', $githubRef);
        } elseif ($githubEventName === 'pull_request') {
            $refParts = explode('/', $githubRef);
            $prNumber = $refParts[2];
            $this->env['CI_PULL_REQUEST'] = $prNumber;
            $this->readEnv['CI_PULL_REQUEST'] = $this->env['CI_PULL_REQUEST'];
        } elseif (strpos($githubRef, 'refs/tags/') !== false) {
            $githubRef = str_replace('refs/tags/', '', $githubRef);
        }

        // Same as coverallsapp/github-action
        // @link https://github.com/coverallsapp/github-action/blob/5984097c6e76d873ef1d8e8e1836b0914d307c3c/src/run.ts#L47
        $this->env['CI_JOB_ID'] = $this->env['GITHUB_RUN_ID'];
        $this->env['CI_BRANCH'] = $githubRef;

        $this->readEnv['GITHUB_ACTIONS'] = $this->env['GITHUB_ACTIONS'];
        $this->readEnv['GITHUB_REF'] = $this->env['GITHUB_REF'];
        $this->readEnv['CI_NAME'] = $this->env['CI_NAME'];
        $this->readEnv['CI_JOB_ID'] = $this->env['CI_JOB_ID'];
        $this->readEnv['CI_BRANCH'] = $this->env['CI_BRANCH'];

        return $this;
    }

    /**
     * Fill CircleCI environment variables.
     *
     * "CIRCLECI", "CIRCLE_WORKFLOW_ID" must be set.
     *
     * @return $this
     */
    protected function fillCircleCi()
    {
        if (isset($this->env['CIRCLECI']) && $this->env['CIRCLECI'] && isset($this->env['CIRCLE_WORKFLOW_ID'])) {
            $this->env['CI_BUILD_NUMBER'] = $this->env['CIRCLE_WORKFLOW_ID'];
            $this->env['CI_NAME'] = 'circleci';

            // backup
            $this->readEnv['CIRCLECI'] = $this->env['CIRCLECI'];
            $this->readEnv['CIRCLE_WORKFLOW_ID'] = $this->env['CIRCLE_WORKFLOW_ID'];
            $this->readEnv['CI_NAME'] = $this->env['CI_NAME'];
        }

        return $this;
    }

    /**
     * Fill AppVeyor environment variables.
     *
     * "APPVEYOR", "APPVEYOR_BUILD_NUMBER" must be set.
     *
     * @return $this
     */
    protected function fillAppVeyor()
    {
        if (isset($this->env['APPVEYOR']) && $this->env['APPVEYOR'] && isset($this->env['APPVEYOR_BUILD_NUMBER'])) {
            $this->env['CI_BUILD_NUMBER'] = $this->env['APPVEYOR_BUILD_NUMBER'];
            $this->env['CI_JOB_ID'] = $this->env['APPVEYOR_JOB_NUMBER'];
            $this->env['CI_BRANCH'] = $this->env['APPVEYOR_REPO_BRANCH'];
            $this->env['CI_PULL_REQUEST'] = $this->env['APPVEYOR_PULL_REQUEST_NUMBER'];
            $this->env['CI_NAME'] = 'AppVeyor';

            // backup
            $this->readEnv['APPVEYOR'] = $this->env['APPVEYOR'];
            $this->readEnv['APPVEYOR_BUILD_NUMBER'] = $this->env['APPVEYOR_BUILD_NUMBER'];
            $this->readEnv['APPVEYOR_JOB_NUMBER'] = $this->env['APPVEYOR_JOB_NUMBER'];
            $this->readEnv['APPVEYOR_REPO_BRANCH'] = $this->env['APPVEYOR_REPO_BRANCH'];
            $this->readEnv['APPVEYOR_PULL_REQUEST_NUMBER'] = $this->env['APPVEYOR_PULL_REQUEST_NUMBER'];
            $this->readEnv['CI_NAME'] = $this->env['CI_NAME'];
        }

        return $this;
    }

    /**
     * Fill Jenkins environment variables.
     *
     * "JENKINS_URL", "BUILD_NUMBER" must be set.
     *
     * @return $this
     */
    protected function fillJenkins()
    {
        if (isset($this->env['JENKINS_URL']) && isset($this->env['BUILD_NUMBER'])) {
            $this->env['CI_BUILD_NUMBER'] = $this->env['BUILD_NUMBER'];
            $this->env['CI_BUILD_URL'] = $this->env['JENKINS_URL'];
            $this->env['CI_NAME'] = 'jenkins';

            // backup
            $this->readEnv['BUILD_NUMBER'] = $this->env['BUILD_NUMBER'];
            $this->readEnv['JENKINS_URL'] = $this->env['JENKINS_URL'];
            $this->readEnv['CI_NAME'] = $this->env['CI_NAME'];
        }

        return $this;
    }

    /**
     * Fill local environment variables.
     *
     * "COVERALLS_RUN_LOCALLY" must be set.
     *
     * @return $this
     */
    protected function fillLocal()
    {
        if (isset($this->env['COVERALLS_RUN_LOCALLY']) && $this->env['COVERALLS_RUN_LOCALLY']) {
            $this->env['CI_JOB_ID'] = null;
            $this->env['CI_NAME'] = 'php-coveralls';
            $this->env['COVERALLS_EVENT_TYPE'] = 'manual';

            // backup
            $this->readEnv['COVERALLS_RUN_LOCALLY'] = $this->env['COVERALLS_RUN_LOCALLY'];
            $this->readEnv['COVERALLS_EVENT_TYPE'] = $this->env['COVERALLS_EVENT_TYPE'];
            $this->readEnv['CI_NAME'] = $this->env['CI_NAME'];
        }

        return $this;
    }

    /**
     * Fill repo_token for unsupported CI service.
     *
     * "COVERALLS_REPO_TOKEN" must be set.
     *
     * @return $this
     */
    protected function fillRepoToken()
    {
        if ($this->config->hasRepoToken()) {
            $this->env['COVERALLS_REPO_TOKEN'] = $this->config->getRepoToken();
        }

        // backup
        if (isset($this->env['COVERALLS_REPO_TOKEN'])) {
            $this->readEnv['COVERALLS_REPO_TOKEN'] = $this->env['COVERALLS_REPO_TOKEN'];
        }

        return $this;
    }

    /**
     * Fill parallel for parallel jobs.
     *
     * @return $this
     */
    protected function fillParallel()
    {
        if (isset($this->env['COVERALLS_PARALLEL'])) {
            $this->readEnv['COVERALLS_PARALLEL'] = $this->env['COVERALLS_PARALLEL'];
        }

        if (isset($this->env['COVERALLS_FLAG_NAME'])) {
            $this->readEnv['COVERALLS_FLAG_NAME'] = $this->env['COVERALLS_FLAG_NAME'];
        }

        return $this;
    }
}
