<?php

namespace SemaphoroTests\Unity\Handlers;


use Semaphoro\Exception\SemaphoroException;
use Semaphoro\Handlers\HandlerInterface;
use Semaphoro\Handlers\RangeHandler;
use Semaphoro\Storages\Redis;
use Semaphoro\Storages\StorageInterface;
use SemaphoroTests\Unity\BaseTest;

class RangeTest extends BaseTest
{
    /** @var HandlerInterface $rangeHandler */
    private $rangeHandler;

    /** @var StorageInterface $this ->storageMock */
    private $storageMock;

    public function setUp()
    {
        parent::setUp();

        $this->storageMock = $this->createMock(Redis::class);

        $this->rangeHandler = new RangeHandler($this->storageMock);
    }

    /**
     * get process when doesn't exist in storage
     */
    public function testGetProcessOpenedWithoutKeys()
    {
        $this->storageMock->method('getKeys')->willReturn([]);

        $process = $this->rangeHandler->getProcessOpened();

        $this->assertEquals(false, $process);
    }

    /**
     * get process when all process has a status processing
     */
    public function testGetProcessOpenedWithoutResult()
    {
        $this->storageMock->method('getKeys')->willReturn([
            self::PROCESS_KEY,
        ]);

        $this->storageMock->method('getValue')->willReturn(self::STATUS_PROCESSING);

        $process = $this->rangeHandler->getProcessOpened();

        $this->assertEquals(false, $process);
    }

    /**
     * check if is throw an exception when the key doesn't exist in the storage
     */
    public function testGetProcessOpenedWithErrorResults()
    {
        $this->storageMock->method('getKeys')->willReturn([
            self::PROCESS_KEY,
        ]);

        $this->storageMock->method('getValue')->will($this->throwException(new SemaphoroException()));

        $process = $this->rangeHandler->getProcessOpened();

        $this->assertEquals(false, $process);
    }

    /**
     * get process when has some process opened
     */
    public function testGetProcessOpened()
    {
        $this->storageMock->method('getKeys')->willReturn([
            self::PROCESS_KEY,
        ]);

        $this->storageMock->method('getValue')->willReturn(self::STATUS_UNPROCESSED);

        $process = $this->rangeHandler->getProcessOpened();

        $this->assertEquals(self::PROCESS_KEY, $process);
    }

    /**
     * check if is throw an exception when doesn't have a last executed number
     */
    public function testCreateProcessKeyWithoutLastId()
    {
        try {
            $this->rangeHandler->createProcessKey();
        } catch (\Throwable $e) {
            $this->assertInstanceOf(SemaphoroException::class, $e);
        }
    }

    /**
     * create process key from last executed number
     */
    public function testCreateProcessKey()
    {
        $this->storageMock->method('getValue')->willReturn(self::PROCESS_START - 1);

        $processKey = $this->rangeHandler->createProcessKey();

        $this->assertEquals(self::PROCESS_KEY, $processKey);
    }

    /**
     * check if storage method save is called at least once
     */
    public function testSetUnprocessedStatus()
    {
        $this->storageMock
            ->expects($this->once())
            ->method('save')
            ->will($this->returnValue(true));

        $this->rangeHandler->setUnprocessedStatus(self::PROCESS_KEY);
    }

    /**
     * check if storage method remove is called at least once
     */
    public function testRemove()
    {
        $this->storageMock
            ->expects($this->once())
            ->method('remove')
            ->will($this->returnValue(true));

        $this->rangeHandler->remove(self::PROCESS_KEY);
    }
}