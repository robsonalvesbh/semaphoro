<?php

namespace Semaphoro\Storages\Redis;


use Predis\Client;
use Semaphoro\Storages\StorageInterface;

class Redis implements StorageInterface
{
    /**
     * @var \Predis\Client|\Redis
     */
    private $redisClient;

    /**
     * @var string
     */
    private $prefixKey;

    /**
     * Redis constructor.
     *
     * @param \Predis\Client|\Redis $redis The redis instance
     * @param string $prefix
     */
    public function __construct($redis, string $prefix = 'semaphoro')
    {
        if (!($redis instanceof Client)) {
            throw new \InvalidArgumentException('Predis\Client instance required');
        }

        $this->redisClient = $redis;
        $this->prefixKey = $prefix;
    }

    /**
     * @param string $key
     * @return array
     */
    public function getKeys(string $key): array
    {
        $keys = $this->redisClient->keys($this->addPrefix($key));

        return array_filter($keys, function ($key) {
            return $this->removePrefix($key);
        });
    }

    /**
     * @param string $key
     * @return bool|string
     */
    public function getValue(string $key)
    {
        return $this->redisClient->get($this->addPrefix($key));
    }

    /**
     * @param string $key
     * @param int $value
     * @return bool|mixed
     */
    public function save(string $key, int $value): bool
    {
        return $this->redisClient->set(
            $this->addPrefix($key),
            $value
        );
    }

    /**
     * @param string $key
     * @return bool
     */
    public function remove(string $key): bool
    {
        return $this->redisClient->del([$this->addPrefix($key)]);
    }


    /**
     * @param string $key
     * @return string
     */
    private function removePrefix(string $key): string
    {
        return str_replace(
            sprintf('%s:', $this->prefixKey),
            '',
            $key
        );
    }

    /**
     * @param string $key
     * @return string
     */
    private function addPrefix(string $key): string
    {
        return sprintf('%s:%s', $this->prefixKey, $key);
    }
}