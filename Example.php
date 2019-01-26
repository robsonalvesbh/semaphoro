<?php
require_once 'vendor/autoload.php';

use Predis\Client;
use Semaphoro\Handlers\RangeHandler;
use Semaphoro\Semaphoro;
use Semaphoro\Storages\Redis;

$redis = new Redis(new Client([
    'scheme' => 'tcp',
    'host' => 'redis',
    'port' => 6379,
]));
$rangeHandler = new RangeHandler($redis);
$semaphoro = new Semaphoro($rangeHandler);
$process = $semaphoro->getAvailableProcess();

try {
    /**
     * YOUR CODE HERE
     */

    $semaphoro->remove($process);
} catch (Throwable $e) {
    $semaphoro->setUnprocessed($process);
}