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

namespace Alexandre\EvcBundle\Tests\Model;

use Alexandre\EvcBundle\Model\Customer;
use PHPUnit\Framework\TestCase;

/**
 * Class CustomerTest test Customer class.
 *
 * @coversDefaultClass \Alexandre\EvcBundle\Model\Customer
 *
 * @internal
 */
class CustomerTest extends TestCase
{
    /**
     * Test the constructor without parameter.
     */
    public function testConstructor(): void
    {
        $customer = new Customer();
        self::assertIsInt($customer->getCredit());
        self::assertEmpty($customer->getCredit());
        self::assertNull($customer->getIdentifier());
        self::assertIsArray($customer->getOptions());
        self::assertEmpty($customer->getOptions());
        self::assertCount(0, $customer->getOptions());
    }

    /**
     * test getCredit method.
     */
    public function testCredit(): void
    {
        $actual = $expected = 42;
        $customer = new Customer(['Credits' => $actual]);
        self::assertIsInt($customer->getCredit());
        self::assertEquals($expected, $customer->getCredit());
    }

    /**
     * test getIdentifier method.
     */
    public function testIdentifier(): void
    {
        $actual = $expected = 42;
        $customer = new Customer(['Customer' => $actual]);
        self::assertIsInt($customer->getIdentifier());
        self::assertEquals($expected, $customer->getIdentifier());
    }

    /**
     * test getOptions.
     */
    public function testOptions(): void
    {
        $actual = $expected = 'bar';
        $customer = new Customer([
            'Foo' => $actual,
            'Other' => 'bob',
        ]);
        self::assertStringContainsString($expected, $customer->getOptions('Foo'));
        self::assertFalse($customer->getOptions('others'));
        self::assertIsArray($customer->getOptions());
        self::assertCount(2, $customer->getOptions());
    }
}
