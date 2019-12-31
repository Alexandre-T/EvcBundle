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
 * @license   MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Alexandre\EvcBundle\Tests;

use Alexandre\EvcBundle\AlexandreEvcBundle;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @internal
 * @coversDefaultClass \Alexandre\EvcBundle\AlexandreEvcBundle
 */
class AlexandreEvcBundleTest extends TestCase
{
    /**
     * @test
     */
    public function couldBeConstructedWithoutAnyArguments(): void
    {
        $actual = new AlexandreEvcBundle();
        self::assertInstanceOf(AlexandreEvcBundle::class, $actual);
    }

    /**
     * @test
     */
    public function shouldBeSubClassOfBundle(): void
    {
        $rc = new ReflectionClass(AlexandreEvcBundle::class);
        self::assertTrue($rc->isSubclassOf(Bundle::class));
    }
}
