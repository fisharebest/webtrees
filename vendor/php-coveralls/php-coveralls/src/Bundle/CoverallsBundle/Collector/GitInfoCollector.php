<?php

namespace PhpCoveralls\Bundle\CoverallsBundle\Collector;

use PhpCoveralls\Bundle\CoverallsBundle\Entity\Git\Commit;
use PhpCoveralls\Bundle\CoverallsBundle\Entity\Git\Git;
use PhpCoveralls\Bundle\CoverallsBundle\Entity\Git\Remote;
use PhpCoveralls\Component\System\Git\GitCommand;

/**
 * Git repository info collector.
 *
 * @author Kitamura Satoshi <with.no.parachute@gmail.com>
 */
class GitInfoCollector
{
    /**
     * Git command.
     *
     * @var GitCommand
     */
    protected $command;

    /**
     * Constructor.
     *
     * @param GitCommand $command Git command
     */
    public function __construct(GitCommand $command)
    {
        $this->command = $command;
    }

    // API

    /**
     * Collect git repository info.
     *
     * @return \PhpCoveralls\Bundle\CoverallsBundle\Entity\Git\Git
     */
    public function collect()
    {
        $branch = $this->collectBranch();
        $commit = $this->collectCommit();
        $remotes = $this->collectRemotes();

        return new Git($branch, $commit, $remotes);
    }

    // accessor

    /**
     * Return git command.
     *
     * @return \PhpCoveralls\Component\System\Git\GitCommand
     */
    public function getCommand()
    {
        return $this->command;
    }

    // internal method

    /**
     * Collect branch name.
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    protected function collectBranch()
    {
        $branchesResult = $this->command->getBranches();

        foreach ($branchesResult as $result) {
            if ($result === '* (no branch)') {
                // Case detected on Travis PUSH hook for tags, can be reporduced by following command:
                // $ git clone --depth=1 --branch=v2.4.0 https://github.com/php-coveralls/php-coveralls.git php-coveralls && cd php-coveralls && git branch
                // * (no branch)
                return '(no branch)';
            }

            if (strpos($result, '* ') === 0) {
                preg_match('/^\* (?:\(HEAD detached at )?([\w\/\-]+)\)?/', $result, $matches);

                return $matches[1];
            }
        }

        throw new \RuntimeException();
    }

    /**
     * Collect commit info.
     *
     * @throws \RuntimeException
     *
     * @return \PhpCoveralls\Bundle\CoverallsBundle\Entity\Git\Commit
     */
    protected function collectCommit()
    {
        $commitResult = $this->command->getHeadCommit();

        if (count($commitResult) !== 6 || array_keys($commitResult) !== range(0, 5)) {
            throw new \RuntimeException();
        }

        $commit = new Commit();

        return $commit
            ->setId($commitResult[0])
            ->setAuthorName($commitResult[1])
            ->setAuthorEmail($commitResult[2])
            ->setCommitterName($commitResult[3])
            ->setCommitterEmail($commitResult[4])
            ->setMessage($commitResult[5]);
    }

    /**
     * Collect remotes info.
     *
     * @throws \RuntimeException
     *
     * @return \PhpCoveralls\Bundle\CoverallsBundle\Entity\Git\Remote[]
     */
    protected function collectRemotes()
    {
        $remotesResult = $this->command->getRemotes();

        if (count($remotesResult) === 0) {
            throw new \RuntimeException();
        }

        // parse command result
        $results = [];

        foreach ($remotesResult as $result) {
            if (strpos($result, ' ') !== false) {
                list($remote) = explode(' ', $result, 2);

                $results[] = $remote;
            }
        }

        // filter
        $results = array_unique($results);

        // create Remote instances
        $remotes = [];

        foreach ($results as $result) {
            if (strpos($result, "\t") !== false) {
                list($name, $url) = explode("\t", $result, 2);

                $remote = new Remote();
                $remotes[] = $remote->setName($name)->setUrl($url);
            }
        }

        return $remotes;
    }
}
