<?php

namespace Semaphoro\Handlers;


use Semaphoro\Storages\StorageInterface;

class RangeHandler implements HandlerInterface
{
    /**
     * Last number executed
     */
    const LAST_EXECUTED_NUMBER_KEY = 'last_executed_number';

    /**
     * Range's status
     *
     * 0 = Unprocessed
     * 1 = Processing
     */
    const STATUS_UNPROCESSED = 0;
    const STATUS_PROCESSING = 1;

    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var int
     */
    private $rangeLength;

    public function __construct(StorageInterface $storage, int $rangeLength = 50)
    {
        $this->storage = $storage;
        $this->rangeLength = $rangeLength;
    }

    /**
     * @return null|string
     */
    public function getProcessOpened(): ?string
    {
        $rangeKeys = $this->storage->getKeys('[0-9]*');

        foreach ($rangeKeys as $rangeKey) {
            try {
                $rangeKeyStatus = $this->storage->getValue($rangeKey);
            } catch (\Throwable $e) {
                continue;
            }

            if ($rangeKeyStatus === self::STATUS_UNPROCESSED) {
                $this->setStatusInProcess($rangeKey);
                return $rangeKey;
            }
        }

        return false;
    }

    /**
     * @param string $rangeKey
     */
    private function setStatusInProcess(string $rangeKey): void
    {
        $this->storage->save($rangeKey, self::STATUS_PROCESSING);
    }

    /**
     * @return string
     */
    public function createProcessKey() :string
    {
        $lastExecutedNumber = $this->getLastExecutedNumber();

        $this->setLastExecutedNumber($this->calculateLastRangeNumber($lastExecutedNumber));

        return $this->createRangeKey($lastExecutedNumber);
    }

    /**
     * @throws \RuntimeException if the last executed number is null
     *
     * @return int
     */
    private function getLastExecutedNumber(): int
    {
        $lastExecutedNumber = $this->storage->getValue(self::LAST_EXECUTED_NUMBER_KEY);

        if (null === $lastExecutedNumber) {
            throw new \RuntimeException('Last executed number is required');
        }

        return $lastExecutedNumber;
    }

    /**
     * @param int $number
     * @return bool|mixed
     */
    private function setLastExecutedNumber(int $number)
    {
        return $this->storage->save(self::LAST_EXECUTED_NUMBER_KEY, $number);
    }

    /**
     * @param int $number
     * @return int
     */
    private function calculateFirstRangeNumber(int $number): int
    {
        return ++$number;
    }

    /**
     * @param int $number
     * @return int
     */
    private function calculateLastRangeNumber(int $number)
    {
        return $number + $this->rangeLength;
    }

    /**
     * @param int $lastNfseExecutedNumber
     * @return string
     */
    private function createRangeKey(int $lastNfseExecutedNumber)
    {
        return sprintf(
            '%s_%s',
            $this->calculateFirstRangeNumber($lastNfseExecutedNumber),
            $this->calculateLastRangeNumber($lastNfseExecutedNumber)
        );
    }

    /**
     * @param string $rangeKey
     * @return bool
     */
    public function setUnprocessedStatus(string $rangeKey): bool
    {
        return $this->storage->save($rangeKey, self::STATUS_UNPROCESSED);
    }

    /**
     * @param string $rangeKey
     * @return bool
     */
    public function remove(string $rangeKey): bool
    {
        return $this->storage->remove($rangeKey);
    }
}