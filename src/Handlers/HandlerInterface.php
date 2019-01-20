<?php

namespace Semaphoro\Handlers;


interface HandlerInterface
{
    /**
     * @return null|string
     */
    public function getProcessOpened(): ?string;

    /**
     * @return string
     */
    public function createProcessKey(): string;

    /**
     * @param string $rangeKey
     * @return bool
     */
    public function setUnprocessedStatus(string $rangeKey): bool;

    /**
     * @param string $rangeKey
     * @return bool
     */
    public function remove(string $rangeKey): bool;
}