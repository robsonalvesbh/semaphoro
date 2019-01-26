<?php

namespace SemaphoroTests\nity;

use Semaphoro\Process;
use Semaphoro\ProcessFactory;
use Semaphoro\ProcessInterface;
use SemaphoroTests\Unity\BaseTest;


class ProcessFactoryTest extends BaseTest
{
    /** @var ProcessFactory $this ->processFactory */
    private $processFactory;

    public function setUp()
    {
        parent::setUp();

        $this->processFactory = new ProcessFactory();
    }

    /**
     * create process from key when isn't reprocess
     */
    public function testCreateFromKey()
    {
        /** @var ProcessInterface $process */
        $process = $this->processFactory->createFromKey(self::PROCESS_KEY);

        $this->assertInstanceOf(ProcessInterface::class, $process);

        $this->assertEquals(self::PROCESS_START, $process->getStart());
        $this->assertEquals(self::PROCESS_END, $process->getEnd());
        $this->assertEquals(!self::IS_REPROCESS, $process->isReprocess());
    }

    /**
     * create process from key when is reprocess
     */
    public function testCreateFromKeyIsReprocess()
    {
        /** @var ProcessInterface $process */
        $process = $this->processFactory->createFromKey(self::PROCESS_KEY, self::IS_REPROCESS);

        $this->assertInstanceOf(ProcessInterface::class, $process);

        $this->assertEquals(self::PROCESS_START, $process->getStart());
        $this->assertEquals(self::PROCESS_END, $process->getEnd());
        $this->assertEquals(self::IS_REPROCESS, $process->isReprocess());
    }

    /**
     * create process key from process
     */
    public function testGetProcessKey()
    {
        /** @var ProcessInterface $processMock */
        $processMock = $this->createMock(Process::class);

        $processMock->method('getStart')->willReturn(self::PROCESS_START);

        $processMock->method('getEnd')->willReturn(self::PROCESS_END);

        $processName = $this->processFactory->getProcessKey($processMock);

        $this->assertEquals(self::PROCESS_KEY, $processName);
    }
}