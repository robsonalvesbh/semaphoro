<?php

namespace SemaphoroTests\Unity\Storages;


use M6Web\Component\RedisMock\RedisMockFactory;
use Predis\Client;
use Semaphoro\Exception\SemaphoroException;
use Semaphoro\Process;
use Semaphoro\Storages\Redis;
use Semaphoro\Storages\StorageInterface;
use SemaphoroTests\Unity\BaseTest;

class RedisTest extends BaseTest
{
    /** @var StorageInterface $this ->redisStorage */
    private $redisStorage;

    public function setUp()
    {
        parent::setUp();

        $factory = new RedisMockFactory();
        $myRedisMock = $factory->getAdapter(Client::class, true);

        /** @var Client $myRedisMock */
        $this->redisStorage = new Redis($myRedisMock);
    }

    /**
     * check if throw an exception when the Storage isn't instance of Predis\Client
     *
     * @expectedException \Throwable
     */
    public function testConstruct()
    {
        $processMock = $this->createMock(Process::class);

        $this->redisStorage = new Redis($processMock);
    }

    /**
     * save process key
     */
    public function testSave()
    {
        $status = $this->redisStorage->save(self::PROCESS_KEY, self::STATUS_PROCESSING);

        $this->assertEquals(true, $status);
    }

    /**
     * get all process keys
     */
    public function testGetKeys()
    {
        $allKeys = $this->redisStorage->getKeys(self::PROCESS_PATTEN);

        $rangeKeyWithPrefix = $this->redisStorage->getKeys(self::PROCESS_KEY);

        $this->assertEquals($rangeKeyWithPrefix, $allKeys);
    }

    /**
     * get value from key
     */
    public function testGet()
    {
        $value = $this->redisStorage->getValue(self::PROCESS_KEY);

        $this->assertEquals(self::STATUS_PROCESSING, $value);
    }

    /**
     * remove process key
     */
    public function testRemove()
    {
        $status = $this->redisStorage->remove(self::PROCESS_KEY);

        $this->assertEquals(true, $status);
    }
}