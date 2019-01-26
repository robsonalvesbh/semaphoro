<?php

namespace Semaphoro\Storages;


use Predis\Client;
use Semaphoro\Exception\SemaphoroException;

class Redis implements StorageInterface
{
    /**
     * @var \Predis\Client
     */
    private $redisClient;

    /**
     * @var string
     */
    private $prefixKey;

    /**
     * Redis constructor.
     *
     * @param \Predis\Client $redis The redis instance
     * @param string $prefix
     * @throws SemaphoroException
     */
    public function __construct($redis, string $prefix = 'semaphoro')
    {
        if (!($redis instanceof Client)) {
            throw new SemaphoroException('Predis\Client instance required');
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
     * @return string
     */
    public function getValue(string $key): string
    {
        return $this->redisClient->get($this->addPrefix($key));
    }

    /**
     * @param string $key
     * @param int $value
     * @return bool
     */
    public function save(string $key, int $value): bool
    {
        return (bool)$this->redisClient->set(
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
        return (bool)$this->redisClient->del([$this->addPrefix($key)]);
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