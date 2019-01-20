<?php

namespace Semaphoro\Handlers;


interface HandlerInterface
{
    /**
     * @return boolean|string
     */
    public function getProcessOpened(): ?string;

    /**
     * @return string
     */
    public function createProcessKey(): string;

    /**
     * @param string $rangeKey
     */
    public function setUnprocessedStatus(string $rangeKey): void;

    /**
     * @param string $rangeKey
     */
    public function remove(string $rangeKey): void;
}
