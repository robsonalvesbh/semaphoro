<?php

namespace Semaphoro;


class ProcessFactory
{
    /**
     * @param string $rangeName
     * @param bool $isReprocess
     * @return ProcessInterface
     */
    public function createFromKey(string $rangeName, bool $isReprocess = false): ProcessInterface
    {
        list($firstNumber, $lastNumber) = $this->extractNumbers($rangeName);

        return new Process($firstNumber, $lastNumber, $isReprocess);
    }

    /**
     * @param $rangeNameWithoutPrefix
     * @return array
     */
    private function extractNumbers($rangeNameWithoutPrefix): array
    {
        return explode('_', $rangeNameWithoutPrefix);
    }

    /**
     * @param ProcessInterface $process
     * @return string
     */
    public function getProcessKey(ProcessInterface $process): string
    {
        return sprintf('%s_%s', $process->getStart(), $process->getEnd());
    }
}
