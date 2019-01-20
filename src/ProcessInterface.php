<?php


namespace Semaphoro;

interface ProcessInterface
{
    /**
     * @return string
     */
    public function getStart(): string;

    /**
     * @return string
     */
    public function getEnd(): string;

    /**
     * @return bool
     */
    public function isReprocess(): bool;
}