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

namespace Alexandre\EvcBundle\Tests\Model;

use Alexandre\EvcBundle\Model\Purchase;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class PurchaseTest test Purchase class.
 *
 * @coversDefaultClass \Alexandre\EvcBundle\Model\Purchase
 *
 * @internal
 */
class PurchaseTest extends TestCase
{
    /**
     * @covers ::getBuild
     */
    public function testBuild(): void
    {
        $actual = $expected = 'foo';
        $purchase = new Purchase(['Build' => $actual]);
        self::assertStringContainsString($expected, $purchase->getBuild());
    }

    /**
     * @covers ::getCharacteristic
     */
    public function testCharacteristic(): void
    {
        $actual = $expected = 'foo';
        $purchase = new Purchase(['Characteristic' => $actual]);
        self::assertStringContainsString($expected, $purchase->getCharacteristic());
    }

    /**
     * @covers ::getComputer
     */
    public function testComputer(): void
    {
        $actual = $expected = 'foo';
        $purchase = new Purchase(['ComputerName' => $actual]);
        self::assertStringContainsString($expected, $purchase->getComputer());
    }

    /**
     * @covers ::getCustomer
     */
    public function testCustomer(): void
    {
        $actual = $expected = 33333;
        $purchase = new Purchase(['Customer' => $actual]);
        self::assertIsInt($purchase->getCustomer());
        self::assertEquals($expected, $purchase->getCustomer());
    }

    /**
     * @covers ::getDate
     */
    public function testDate(): void
    {
        $actual = $expected = '2019-12-30T00:00:00';
        $purchase = new Purchase(['Date' => $actual]);
        self::assertInstanceOf(DateTimeInterface::class, $purchase->getDate());

        $actual = $expected = 'some error';
        $purchase = new Purchase(['Date' => $actual]);
        self::assertNull($purchase->getDate());
    }

    /**
     * @covers ::getEcuBuild
     */
    public function testEcuBuild(): void
    {
        $actual = $expected = 'foo';
        $purchase = new Purchase(['Ecu Build' => $actual]);
        self::assertStringContainsString($expected, $purchase->getEcuBuild());
    }

    /**
     * @covers ::getEcuManufacturer
     */
    public function testEcuManufacturer(): void
    {
        $actual = $expected = 'foo';
        $purchase = new Purchase(['Ecu Manufacturer' => $actual]);
        self::assertStringContainsString($expected, $purchase->getEcuManufacturer());
    }

    /**
     * @covers ::getEcuNrEcu
     */
    public function testEcuNrEcu(): void
    {
        $actual = $expected = 'foo';
        $purchase = new Purchase(['ECU_Nr_ECU' => $actual]);
        self::assertStringContainsString($expected, $purchase->getEcuNrEcu());
    }

    /**
     * @covers ::getEcuNrProd
     */
    public function testEcuNrProd(): void
    {
        $actual = $expected = 'foo';
        $purchase = new Purchase(['ECU_Nr_Prod' => $actual]);
        self::assertStringContainsString($expected, $purchase->getEcuNrProd());
    }

    /**
     * @covers ::getFilename
     */
    public function testFilename(): void
    {
        $actual = $expected = 'foo';
        $purchase = new Purchase(['Filename' => $actual]);
        self::assertStringContainsString($expected, $purchase->getFilename());
    }

    /**
     * @covers ::getIp
     */
    public function testIp(): void
    {
        $actual = $expected = 'foo';
        $purchase = new Purchase(['IP' => $actual]);
        self::assertStringContainsString($expected, $purchase->getIp());
    }

    /**
     * @covers ::getManufacturer
     */
    public function testManufacturer(): void
    {
        $actual = $expected = 'foo';
        $purchase = new Purchase(['Manufacturer' => $actual]);
        self::assertStringContainsString($expected, $purchase->getManufacturer());
    }

    /**
     * @covers ::getModel
     */
    public function testModel(): void
    {
        $actual = $expected = 'foo';
        $purchase = new Purchase(['Model' => $actual]);
        self::assertStringContainsString($expected, $purchase->getModel());
    }

    /**
     * @covers ::getOptions
     */
    public function testOptions(): void
    {
        $actual = $expected = 'bar';
        $purchase = new Purchase([
            'Foo' => $actual,
            'Other' => 'bob',
        ]);
        self::assertStringContainsString($expected, $purchase->getOptions('Foo'));
        self::assertFalse($purchase->getOptions('others'));
        self::assertIsArray($purchase->getOptions());
        self::assertCount(2, $purchase->getOptions());
    }

    /**
     * @covers ::getOutput
     */
    public function testOutput(): void
    {
        $actual = $expected = 'foo';
        $purchase = new Purchase(['Output' => $actual]);
        self::assertStringContainsString($expected, $purchase->getOutput());
    }

    /**
     * @covers ::getProject
     */
    public function testProject(): void
    {
        $actual = $expected = 'foo';
        $purchase = new Purchase(['Project type' => $actual]);
        self::assertStringContainsString($expected, $purchase->getProject());
    }

    /**
     * @covers ::getSeries
     */
    public function testSeries(): void
    {
        $actual = $expected = 'foo';
        $purchase = new Purchase(['Series' => $actual]);
        self::assertStringContainsString($expected, $purchase->getSeries());
    }

    /**
     * @covers ::getSoftware
     */
    public function testSoftware(): void
    {
        $actual = $expected = 'foo';
        $purchase = new Purchase(['Software' => $actual]);
        self::assertStringContainsString($expected, $purchase->getSoftware());
    }

    /**
     * @covers ::getSoftwareVersion
     */
    public function testSoftwareVersion(): void
    {
        $actual = $expected = 'foo';
        $purchase = new Purchase(['SoftwareVersion' => $actual]);
        self::assertStringContainsString($expected, $purchase->getSoftwareVersion());
    }
}
