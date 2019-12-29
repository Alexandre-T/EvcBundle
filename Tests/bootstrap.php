<?php


use AspectMock\Kernel;

include __DIR__.'/../vendor/autoload.php'; // composer autoload

$kernel = Kernel::getInstance();
$kernel->init([
    'debug' => true,
    'cacheDir' => 'build/cache',
    'appDir' => __DIR__ . '/..',
    'includePaths' => [
        __DIR__.'/../vendor/mashape/unirest-php',
        __DIR__.'/../vendor/codeception',
        __DIR__.'/../vendor/codeception/aspect-mock',
        __DIR__.'/../vendor/codeception/aspect-mock/tests',
    ],
    'excludePaths' => [
        __DIR__ . '/../Tests',
    ]
]);
