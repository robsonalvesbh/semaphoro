<?php

namespace Semaphoro;


class Process implements ProcessInterface
{
    /**
     * @var int
     */
    private $start;

    /**
     * @var int
     */
    private $end;

    /**
     * @var bool
     */
    private $isReprocess;

    /**
     * Range constructor.
     *
     * @param int $start
     * @param int $end
     * @param bool $isReprocess
     */
    public function __construct(int $start, int $end, bool $isReprocess)
    {
        $this->start = $start;
        $this->end = $end;
        $this->isReprocess = $isReprocess;
    }

    /**
     * @return string
     */
    public function getStart(): string
    {
        return $this->start;
    }

    /**
     * @return string
     */
    public function getEnd(): string
    {
        return $this->end;
    }

    /**
     * @return bool
     */
    public function isReprocess(): bool
    {
        return $this->isReprocess === true;
    }
}