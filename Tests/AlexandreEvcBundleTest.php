<?php

namespace Alexandre\Evc\Tests;

use Alexandre\Evc\AlexandreEvcBundle;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AlexandreEvcBundleTest extends TestCase
{
    /**
     * @test
     */
    public function shouldBeSubClassOfBundle()
    {
        $rc = new ReflectionClass(AlexandreEvcBundle::class);
        self::assertTrue($rc->isSubclassOf(Bundle::class));
    }

    /**
     * @test
     */
    public function couldBeConstructedWithoutAnyArguments()
    {
        $actual = new AlexandreEvcBundle;
        self::assertInstanceOf(AlexandreEvcBundle::class, $actual);
    }
}
