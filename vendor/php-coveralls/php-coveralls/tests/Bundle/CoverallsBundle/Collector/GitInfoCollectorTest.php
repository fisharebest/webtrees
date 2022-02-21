<?php

namespace PhpCoveralls\Tests\Bundle\CoverallsBundle\Collector;

use PhpCoveralls\Bundle\CoverallsBundle\Collector\GitInfoCollector;
use PhpCoveralls\Bundle\CoverallsBundle\Entity\Git\Commit;
use PhpCoveralls\Bundle\CoverallsBundle\Entity\Git\Git;
use PhpCoveralls\Bundle\CoverallsBundle\Entity\Git\Remote;
use PhpCoveralls\Component\System\Git\GitCommand;
use PhpCoveralls\Tests\ProjectTestCase;

/**
 * @covers \PhpCoveralls\Bundle\CoverallsBundle\Collector\GitInfoCollector
 *
 * @author Kitamura Satoshi <with.no.parachute@gmail.com>
 */
class GitInfoCollectorTest extends ProjectTestCase
{
    /**
     * @var array
     */
    private $getBranchesValue = [
        '  master',
        '* branch-1',
        '  branch-2',
    ];

    /**
     * @var array
     */
    private $getHeadCommitValue = [
        'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
        'Author Name',
        'author@satooshi.jp',
        'Committer Name',
        'committer@satooshi.jp',
        'commit message',
    ];

    /**
     * @var array
     */
    private $getRemotesValue = [
        "origin\tgit@github.com:php-coveralls/php-coveralls.git (fetch)",
        "origin\tgit@github.com:php-coveralls/php-coveralls.git (push)",
    ];

    // getCommand()

    /**
     * @test
     */
    public function shouldHaveGitCommandOnConstruction()
    {
        $command = new GitCommand();
        $object = new GitInfoCollector($command);

        $this->assertSame($command, $object->getCommand());
    }

    // collect()

    /**
     * @test
     */
    public function shouldCollect()
    {
        $gitCommand = $this->createGitCommandStubWith($this->getBranchesValue, $this->getHeadCommitValue, $this->getRemotesValue);
        $object = new GitInfoCollector($gitCommand);

        $git = $object->collect();

        $this->assertInstanceOf(Git::class, $git);
        $this->assertGit($git);
        $this->assertSame('branch-1', $git->getBranch());
    }

    /**
     * @test
     */
    public function shouldCollectDetachedRef()
    {
        $gitCommand = $this->createGitCommandStubWith(
            ['* (HEAD detached at pull/1/merge)'],
            $this->getHeadCommitValue,
            $this->getRemotesValue
        );
        $object = new GitInfoCollector($gitCommand);

        $git = $object->collect();

        $this->assertInstanceOf(Git::class, $git);
        $this->assertGit($git);
        $this->assertSame('pull/1/merge', $git->getBranch());
    }

    /**
     * @test
     */
    public function shouldCollectNoBranch()
    {
        $gitCommand = $this->createGitCommandStubWith(
            ['* (no branch)'],
            $this->getHeadCommitValue,
            $this->getRemotesValue
        );
        $object = new GitInfoCollector($gitCommand);

        $git = $object->collect();

        $this->assertInstanceOf(Git::class, $git);
        $this->assertGit($git);
        $this->assertSame('(no branch)', $git->getBranch());
    }

    // collectBranch() exception

    /**
     * @test
     */
    public function throwRuntimeExceptionIfCurrentBranchNotFound()
    {
        $getBranchesValue = [
            '  master',
        ];
        $gitCommand = $this->createGitCommandStubCalledBranches($getBranchesValue);

        $object = new GitInfoCollector($gitCommand);

        $this->expectException(\RuntimeException::class);
        $object->collect();
    }

    // collectCommit() exception

    /**
     * @test
     */
    public function throwRuntimeExceptionIfHeadCommitIsInvalid()
    {
        $getHeadCommitValue = [];
        $gitCommand = $this->createGitCommandStubCalledHeadCommit($this->getBranchesValue, $getHeadCommitValue);

        $object = new GitInfoCollector($gitCommand);

        $this->expectException(\RuntimeException::class);
        $object->collect();
    }

    // collectRemotes() exception

    /**
     * @test
     */
    public function throwRuntimeExceptionIfRemoteIsInvalid()
    {
        $getRemotesValue = [];
        $gitCommand = $this->createGitCommandStubWith($this->getBranchesValue, $this->getHeadCommitValue, $getRemotesValue);

        $object = new GitInfoCollector($gitCommand);

        $this->expectException(\RuntimeException::class);
        $object->collect();
    }

    /**
     * @param $getBranchesValue
     * @param $getHeadCommitValue
     * @param $getRemotesValue
     *
     * @return object
     */
    protected function createGitCommandStubWith($getBranchesValue, $getHeadCommitValue, $getRemotesValue)
    {
        $stub = $this->prophesize(GitCommand::class);

        $this->setUpGitCommandStubWithGetBranchesOnce($stub, $getBranchesValue);
        $this->setUpGitCommandStubWithGetHeadCommitOnce($stub, $getHeadCommitValue);
        $this->setUpGitCommandStubWithGetRemotesOnce($stub, $getRemotesValue);

        return $stub->reveal();
    }

    /**
     * @param array $getBranchesValue
     *
     * @return GitCommand
     */
    protected function createGitCommandStubCalledBranches($getBranchesValue)
    {
        $stub = $this->prophesize(GitCommand::class);

        $this->setUpGitCommandStubWithGetBranchesOnce($stub, $getBranchesValue);
        $this->setUpGitCommandStubWithGetHeadCommitNeverCalled($stub);
        $this->setUpGitCommandStubWithGetRemotesNeverCalled($stub);

        return $stub->reveal();
    }

    /**
     * @param array $getBranchesValue
     * @param array $getHeadCommitValue
     *
     * @return GitCommand
     */
    protected function createGitCommandStubCalledHeadCommit($getBranchesValue, $getHeadCommitValue)
    {
        $stub = $this->prophesize(GitCommand::class);

        $this->setUpGitCommandStubWithGetBranchesOnce($stub, $getBranchesValue);
        $this->setUpGitCommandStubWithGetHeadCommitOnce($stub, $getHeadCommitValue);
        $this->setUpGitCommandStubWithGetRemotesNeverCalled($stub);

        return $stub->reveal();
    }

    /**
     * @param GitCommand $stub
     * @param $getBranchesValue
     */
    protected function setUpGitCommandStubWithGetBranchesOnce($stub, $getBranchesValue)
    {
        $stub
            ->getBranches()
            ->willReturn($getBranchesValue)
            ->shouldBeCalled();
    }

    /**
     * @param GitCommand $stub
     * @param array      $getHeadCommitValue
     */
    protected function setUpGitCommandStubWithGetHeadCommitOnce($stub, $getHeadCommitValue)
    {
        $stub
            ->getHeadCommit()
            ->willReturn($getHeadCommitValue)
            ->shouldBeCalled();
    }

    /**
     * @param GitCommand $stub
     */
    protected function setUpGitCommandStubWithGetHeadCommitNeverCalled($stub)
    {
        $stub
            ->getHeadCommit()
            ->shouldNotBeCalled();
    }

    /**
     * @param GitCommand $stub
     * @param array      $getRemotesValue
     */
    protected function setUpGitCommandStubWithGetRemotesOnce($stub, $getRemotesValue)
    {
        $stub
            ->getRemotes()
            ->willReturn($getRemotesValue)
            ->shouldBeCalled();
    }

    /**
     * @param GitCommand $stub
     */
    protected function setUpGitCommandStubWithGetRemotesNeverCalled($stub)
    {
        $stub
            ->getRemotes()
            ->shouldNotBeCalled();
    }

    protected function assertGit(Git $git)
    {
        $commit = $git->getHead();

        $this->assertInstanceOf(Commit::class, $commit);
        $this->assertCommit($commit);

        $remotes = $git->getRemotes();
        $this->assertCount(1, $remotes);

        $this->assertInstanceOf(Remote::class, $remotes[0]);
        $this->assertRemote($remotes[0]);
    }

    protected function assertCommit(Commit $commit)
    {
        $this->assertSame('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', $commit->getId());
        $this->assertSame('Author Name', $commit->getAuthorName());
        $this->assertSame('author@satooshi.jp', $commit->getAuthorEmail());
        $this->assertSame('Committer Name', $commit->getCommitterName());
        $this->assertSame('committer@satooshi.jp', $commit->getCommitterEmail());
        $this->assertSame('commit message', $commit->getMessage());
    }

    protected function assertRemote(Remote $remote)
    {
        $this->assertSame('origin', $remote->getName());
        $this->assertSame('git@github.com:php-coveralls/php-coveralls.git', $remote->getUrl());
    }
}
