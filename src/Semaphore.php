<?php


namespace Semaphoro;

use Semaphoro\Handlers\HandlerInterface;

class Semaphore
{
    const RANGE_OPEN = 0;
    const RANGE_IS_REPROCESS = true;
    const INCREMENT_FIRST_NUMBER = 1;
    const INCREMENT_LAST_NUMBER = 50;

    /**
     * @var HandlerInterface
     */
    private $handler;

    /**
     * @var ProcessFactory
     */
    private $processFactory;

    /**
     * Semaphoro constructor.
     * @param HandlerInterface $handler
     */
    public function __construct(HandlerInterface $handler)
    {
        $this->handler = $handler;
        $this->processFactory = new ProcessFactory();
    }

    /**
     * @return ProcessInterface
     */
    public function getProcessAvailable(): ProcessInterface
    {
        if ($openProcessKey = $this->handler->getProcessOpened()) {
            return $this->processFactory->createFromKey($openProcessKey, self::RANGE_IS_REPROCESS);
        }

        $processKey = $this->handler->createProcessKey();

        return $this->processFactory->createFromKey($processKey);
    }

    /**
     * @param ProcessInterface $process
     * @return bool
     */
    public function setUnprocessed(ProcessInterface $process): bool
    {
        return $this->handler->setUnprocessedStatus($this->processFactory->getProcessKey($process));
    }

    /**
     * @param ProcessInterface $process
     * @return bool
     */
    public function remove(ProcessInterface $process): bool
    {
        return $this->handler->remove($this->processFactory->getProcessKey($process));
    }
}