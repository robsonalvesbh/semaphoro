[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/robsonalvesbh/Semaphoro/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/robsonalvesbh/Semaphoro/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/robsonalvesbh/Semaphoro/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/robsonalvesbh/Semaphoro/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/robsonalvesbh/Semaphoro/badges/build.png?b=master)](https://scrutinizer-ci.com/g/robsonalvesbh/Semaphoro/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/robsonalvesbh/Semaphoro/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)

# Semaphoro
This library will help you to run multiple process with PHP.

Semaphoro library performs the orchestration of the processes avoiding that two or more workers that are running in parallel run the same processes, 
avoiding duplication of processes and still have a contingency to process again in case some process fails.

### How to use

#### Set a storage

###### Parameters

* **[Predis/Client](https://packagist.org/packages/predis/predis)** $redisClient - _required_
* **String** $prefix - _optional_ (default value: semaphoro)

_prefix_ is a namespace for redis.

```php
$redis = new Redis($redisClient, $prefix);
```

#### Set a handler

###### Parameters
* **StorageInterface** $storage - _required_
* **int** $rangeLength - _optional_ (default value: 50)
 
_rangeLength_ is the quantity of process in a range


```php
$rangeHandler = new RangeHandler($storage, $rangeLength);
```

The RangeHandler is projected to work with incremental numbers like ID 

#### Get semaphoro

###### Parameters
* **HandlerInterface** $handler - _required_

```php
$semaphoro = new Semaphoro($handler);
```

###### Methods

##### getAvailableProcess()

Get the next range available

```php
$semaphoro->getAvailableProcess();
```
###### return
* ProcessInterface 

##### setUnprocessed()

Set unprocessed status when occurring an error  

###### Parameters
* **ProcessInterface** $process - _required_

```php
$semaphoro->setUnprocessed($process);
```
###### return
* void
 
##### remove()
Remove process from semaphoro when the process is finished  

###### Parameters
* **ProcessInterface** $process - _required_

```php
$semaphoro->remove($process);
```
###### return
* void


### Code example
```php
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
```


### Credits 

* Robson Alves - [github](https://github.com/robsonalvesbh)
* Gustavo Andrade - [github](https://github.com/jojovem)

