<?php

namespace Satooshi\Bundle\CoverallsV1Bundle\Collector;

use Satooshi\Bundle\CoverallsV1Bundle\Entity\Git\Commit;
use Satooshi\Bundle\CoverallsV1Bundle\Entity\Git\Git;
use Satooshi\Bundle\CoverallsV1Bundle\Entity\Git\Remote;
use Satooshi\Component\System\Git\GitCommand;

/**
 * @covers \Satooshi\Bundle\CoverallsV1Bundle\Collector\GitInfoCollector
 *
 * @author Kitamura Satoshi <with.no.parachute@gmail.com>
 */
class GitInfoCollectorTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->getBranchesValue = array(
            '  master',
            '* branch1',
            '  branch2',
        );
        $this->getHeadCommitValue = array(
            'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
            'Author Name',
            'author@satooshi.jp',
            'Committer Name',
            'committer@satooshi.jp',
            'commit message',
        );
        $this->getRemotesValue = array(
            "origin\tgit@github.com:php-coveralls/php-coveralls.git (fetch)",
            "origin\tgit@github.com:php-coveralls/php-coveralls.git (push)",
        );
    }

    protected function createGitCommandStubWith($getBranchesValue, $getHeadCommitValue, $getRemotesValue)
    {
        $stub = $this->prophesize('Satooshi\Component\System\Git\GitCommand');

        $this->setUpGitCommandStubWithGetBranchesOnce($stub, $getBranchesValue);
        $this->setUpGitCommandStubWithGetHeadCommitOnce($stub, $getHeadCommitValue);
        $this->setUpGitCommandStubWithGetRemotesOnce($stub, $getRemotesValue);

        return $stub->reveal();
    }

    protected function createGitCommandStubCalledBranches($getBranchesValue)
    {
        $stub = $this->prophesize('Satooshi\Component\System\Git\GitCommand');

        $this->setUpGitCommandStubWithGetBranchesOnce($stub, $getBranchesValue);
        $this->setUpGitCommandStubWithGetHeadCommitNeverCalled($stub);
        $this->setUpGitCommandStubWithGetRemotesNeverCalled($stub);

        return $stub->reveal();
    }

    protected function createGitCommandStubCalledHeadCommit($getBranchesValue, $getHeadCommitValue, $getRemotesValue)
    {
        $stub = $this->prophesize('Satooshi\Component\System\Git\GitCommand');

        $this->setUpGitCommandStubWithGetBranchesOnce($stub, $getBranchesValue);
        $this->setUpGitCommandStubWithGetHeadCommitOnce($stub, $getHeadCommitValue);
        $this->setUpGitCommandStubWithGetRemotesNeverCalled($stub);

        return $stub->reveal();
    }

    protected function setUpGitCommandStubWithGetBranchesOnce($stub, $getBranchesValue)
    {
        $stub
            ->getBranches()
            ->willReturn($getBranchesValue)
            ->shouldBeCalled();
    }

    protected function setUpGitCommandStubWithGetHeadCommitOnce($stub, $getHeadCommitValue)
    {
        $stub
            ->getHeadCommit()
            ->willReturn($getHeadCommitValue)
            ->shouldBeCalled();
    }

    protected function setUpGitCommandStubWithGetHeadCommitNeverCalled($stub)
    {
        $stub
            ->getHeadCommit()
            ->shouldNotBeCalled();
    }

    protected function setUpGitCommandStubWithGetRemotesOnce($stub, $getRemotesValue)
    {
        $stub
            ->getRemotes()
            ->willReturn($getRemotesValue)
            ->shouldBeCalled();
    }

    protected function setUpGitCommandStubWithGetRemotesNeverCalled($stub)
    {
        $stub
            ->getRemotes()
            ->shouldNotBeCalled();
    }

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

        $this->assertInstanceOf('Satooshi\Bundle\CoverallsV1Bundle\Entity\Git\Git', $git);
        $this->assertGit($git);
    }

    protected function assertGit(Git $git)
    {
        $this->assertSame('branch1', $git->getBranch());

        $commit = $git->getHead();

        $this->assertInstanceOf('Satooshi\Bundle\CoverallsV1Bundle\Entity\Git\Commit', $commit);
        $this->assertCommit($commit);

        $remotes = $git->getRemotes();
        $this->assertCount(1, $remotes);

        $this->assertInstanceOf('Satooshi\Bundle\CoverallsV1Bundle\Entity\Git\Remote', $remotes[0]);
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

    // collectBranch() exception

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function throwRuntimeExceptionIfCurrentBranchNotFound()
    {
        $getBranchesValue = array(
            '  master',
        );
        $gitCommand = $this->createGitCommandStubCalledBranches($getBranchesValue);

        $object = new GitInfoCollector($gitCommand);

        $object->collect();
    }

    // collectCommit() exception

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function throwRuntimeExceptionIfHeadCommitIsInvalid()
    {
        $getHeadCommitValue = array();
        $gitCommand = $this->createGitCommandStubCalledHeadCommit($this->getBranchesValue, $getHeadCommitValue, $this->getRemotesValue);

        $object = new GitInfoCollector($gitCommand);

        $object->collect();
    }

    // collectRemotes() exception

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function throwRuntimeExceptionIfRemoteIsInvalid()
    {
        $getRemotesValue = array();
        $gitCommand = $this->createGitCommandStubWith($this->getBranchesValue, $this->getHeadCommitValue, $getRemotesValue);

        $object = new GitInfoCollector($gitCommand);

        $object->collect();
    }
}
