<?php

namespace SemaphoroTests\Unity;


use Semaphoro\Handlers\HandlerInterface;
use Semaphoro\Handlers\RangeHandler;
use Semaphoro\Process;
use Semaphoro\ProcessInterface;
use Semaphoro\Semaphoro;

class SemaphoroTest extends BaseTest
{
    /** @var Semaphoro $semaphoro */
    private $semaphoro;

    /** @var HandlerInterface $handlerMock */
    private $handlerMock;

    public function setUp()
    {
        parent::setUp();

        $this->handlerMock = $this->createMock(RangeHandler::class);
        $this->semaphoro = new Semaphoro($this->handlerMock);
    }

    /**
     * get available process when is reprocess
     */
    public function testGetAvailableProcess()
    {
        $this->handlerMock->method('getProcessOpened')->willReturn(self::PROCESS_KEY);

        $process = $this->semaphoro->getAvailableProcess();

        $this->assertInstanceOf(ProcessInterface::class, $process);
        $this->assertEquals(self::PROCESS_START, $process->getStart());
        $this->assertEquals(self::PROCESS_END, $process->getEnd());
        $this->assertEquals(self::IS_REPROCESS, $process->isReprocess());
    }

    /**
     * get available process when isn't reprocess
     */
    public function testGetAvailableProcessWhenIsntReprocess()
    {
        $this->handlerMock->method('getProcessOpened')->willReturn(self::ISNT_REPROCESS);
        $this->handlerMock->method('createProcessKey')->willReturn(self::PROCESS_KEY);

        $process = $this->semaphoro->getAvailableProcess();

        $this->assertInstanceOf(ProcessInterface::class, $process);
        $this->assertEquals(self::PROCESS_START, $process->getStart());
        $this->assertEquals(self::PROCESS_END, $process->getEnd());
        $this->assertEquals(self::ISNT_REPROCESS, $process->isReprocess());
    }

    /**
     * check if storage method save is called at least once
     */
    public function testSetUnprocessedStatus()
    {
        $this->handlerMock
            ->expects($this->once())
            ->method('setUnprocessedStatus')
            ->will($this->returnValue(true));

        /** @var Process $processMock */
        $processMock = $this->createMock(Process::class);
        $this->semaphoro->setUnprocessed($processMock);
    }

    /**
     * check if storage method remove is called at least once
     */
    public function testRemove()
    {
        $this->handlerMock
            ->expects($this->once())
            ->method('remove')
            ->will($this->returnValue(true));

        /** @var Process $processMock */
        $processMock = $this->createMock(Process::class);
        $this->semaphoro->remove($processMock);
    }
}