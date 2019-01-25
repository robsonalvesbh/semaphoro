<?php

namespace Unity;

use PHPUnit\Framework\TestCase;
use Semaphoro\Process;
use Semaphoro\ProcessFactory;
use Semaphoro\ProcessInterface;


class ProcessFactoryTest extends TestCase
{
    /** @var ProcessFactory $this ->processFactory */
    private $processFactory;

    const PROCESS_NAME = '100_200';
    const PROCESS_START = '100';
    const PROCESS_END = '200';
    const IS_REPROCESS = true;

    public function setUp()
    {
        parent::setUp();

        $this->processFactory = new ProcessFactory();
    }

    /**
     * testando criação da key quando não é reprocess
     */
    public function testCreateFromKey()
    {
        /** @var ProcessInterface $process */
        $process = $this->processFactory->createFromKey(self::PROCESS_NAME);

        $this->assertInstanceOf(ProcessInterface::class, $process);

        $this->assertEquals(self::PROCESS_START, $process->getStart());
        $this->assertEquals(self::PROCESS_END, $process->getEnd());
        $this->assertEquals(!self::IS_REPROCESS, $process->isReprocess());
    }

    /**
     * testando criação da key quando é reprocess
     */
    public function testCreateFromKeyIsReprocess()
    {
        /** @var ProcessInterface $process */
        $process = $this->processFactory->createFromKey(self::PROCESS_NAME, self::IS_REPROCESS);

        $this->assertInstanceOf(ProcessInterface::class, $process);

        $this->assertEquals(self::PROCESS_START, $process->getStart());
        $this->assertEquals(self::PROCESS_END, $process->getEnd());
        $this->assertEquals(self::IS_REPROCESS, $process->isReprocess());
    }

    /**
     * testando criação do processName a partir de um processo
     */
    public function testGetProcessKey()
    {
        /** @var ProcessInterface $processMock */
        $processMock = $this->createMock(Process::class);

        $processMock->method('getStart')
            ->willReturn(self::PROCESS_START);

        $processMock->method('getEnd')
            ->willReturn(self::PROCESS_END);

        $processName = $this->processFactory->getProcessKey($processMock);

        $this->assertEquals(self::PROCESS_NAME, $processName);
    }
}