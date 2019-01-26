<?php

namespace Semaphoro\Handlers;


use Semaphoro\Exception\SemaphoroException;
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
    const STATUS_UNPROCESSED = "0";
    const STATUS_PROCESSING = "1";

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
     * @return boolean|string
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
     * @throws SemaphoroException
     */
    public function createProcessKey(): string
    {
        $lastExecutedNumber = $this->getLastExecutedNumber();

        $this->setLastExecutedNumber($this->calculateLastRangeNumber($lastExecutedNumber));

        return $this->createRangeKey($lastExecutedNumber);
    }

    /**
     * @return string
     * @throws SemaphoroException when doesn't have a last executed number
     */
    private function getLastExecutedNumber(): string
    {
        $lastExecutedNumber = $this->storage->getValue(self::LAST_EXECUTED_NUMBER_KEY);

        if (is_null($lastExecutedNumber) || empty($lastExecutedNumber)) {
            throw new SemaphoroException('Last executed number is required');
        }

        return $lastExecutedNumber;
    }

    /**
     * @param int $number
     */
    private function setLastExecutedNumber(int $number): void
    {
        $this->storage->save(self::LAST_EXECUTED_NUMBER_KEY, $number);
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
    private function calculateLastRangeNumber(int $number): int
    {
        return $number + $this->rangeLength;
    }

    /**
     * @param int $lastExecutedNumber
     * @return string
     */
    private function createRangeKey(int $lastExecutedNumber): string
    {
        return sprintf(
            '%s_%s',
            $this->calculateFirstRangeNumber($lastExecutedNumber),
            $this->calculateLastRangeNumber($lastExecutedNumber)
        );
    }

    /**
     * @param string $rangeKey
     */
    public function setUnprocessedStatus(string $rangeKey): void
    {
        $this->storage->save($rangeKey, self::STATUS_UNPROCESSED);
    }

    /**
     * @param string $rangeKey
     */
    public function remove(string $rangeKey): void
    {
        $this->storage->remove($rangeKey);
    }
}