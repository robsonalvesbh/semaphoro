<?php

namespace SemaphoroTests\Unity;


use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    const PROCESS_KEY = '101_150';
    const PROCESS_PATTEN = '[0-9]*';

    const PROCESS_START = '101';
    const PROCESS_END = '150';
    const IS_REPROCESS = true;
    const ISNT_REPROCESS = false;

    const STATUS_UNPROCESSED = "0";
    const STATUS_PROCESSING = "1";
}