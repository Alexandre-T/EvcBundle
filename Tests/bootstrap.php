<?php
/**
 * This file is part of the Evc Bundle.
 *
 * PHP version 7.1|7.2|7.3|7.4
 * Symfony version 4.4|5.0|5.1
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * @author    Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @copyright 2020 Alexandre Tranchant
 * @license   Cecill-B http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 */

declare(strict_types=1);

use AspectMock\Kernel;

include __DIR__.'/../vendor/autoload.php'; // composer autoload

$kernel = Kernel::getInstance();
$kernel->init([
    'debug' => true,
    'cacheDir' => 'build/cache',
    'appDir' => __DIR__.'/..',
    'includePaths' => [
        __DIR__.'/../vendor/mashape/unirest-php',
        __DIR__.'/../vendor/codeception',
        __DIR__.'/../vendor/codeception/aspect-mock',
        __DIR__.'/../vendor/codeception/aspect-mock/tests',
    ],
    'excludePaths' => [
        __DIR__.'/../Tests',
    ],
]);
