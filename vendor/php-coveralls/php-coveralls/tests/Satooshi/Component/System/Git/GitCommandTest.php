<?php

namespace Satooshi\Component\System\Git;

/**
 * @covers \Satooshi\Component\System\Git\GitCommand
 * @covers \Satooshi\Component\System\SystemCommand
 *
 * @author Kitamura Satoshi <with.no.parachute@gmail.com>
 */
class GitCommandTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function shouldReturnBranches()
    {
        $object = new GitCommand();
        $actual = $object->getBranches();

        $this->assertInternalType('array', $actual);
        $this->assertNotEmpty($actual);
    }

    /**
     * @test
     */
    public function shouldReturnHeadCommit()
    {
        $object = new GitCommand();
        $actual = $object->getHeadCommit();

        $this->assertInternalType('array', $actual);
        $this->assertNotEmpty($actual);
        $this->assertCount(6, $actual);
    }

    /**
     * @test
     */
    public function shouldReturnRemotes()
    {
        $object = new GitCommand();
        $actual = $object->getRemotes();

        $this->assertInternalType('array', $actual);
        $this->assertNotEmpty($actual);
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function throwRuntimeExceptionIfExecutedWithoutArgs()
    {
        $object = new GitCommand();
        $object->execute();
    }
}
