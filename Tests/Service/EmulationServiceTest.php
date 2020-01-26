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

namespace Alexandre\EvcBundle\Tests\Service;

use Alexandre\EvcBundle\Exception\CredentialException;
use Alexandre\EvcBundle\Exception\LogicException;
use Alexandre\EvcBundle\Exception\NetworkException;
use Alexandre\EvcBundle\Service\EmulationService;
use PHPUnit\Framework\TestCase;
use Unirest\Response;

/**
 * @internal
 * @coversDefaultClass
 */
final class EmulationServiceTest extends TestCase
{
    /**
     * The instance to test.
     *
     * @var EmulationService
     */
    private $requester;

    /**
     * Setup the requester before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->requester = new EmulationService(
            'api-id',
            '33333',
            'foobar'
        );
    }

    /**
     * Tear down the requester to avoid memory leaks.
     */
    protected function tearDown(): void
    {
        $this->requester = null;

        parent::tearDown();
    }

    /**
     * Assert actual is a Response with code 200 and the expected body.
     *
     * @param string   $body   expected body
     * @param Response $actual the response to test
     */
    private static function assertResponse(string $body, $actual): void
    {
        self::assertInstanceOf(Response::class, $actual);
        self::assertIsInt($actual->code);
        self::assertEquals(200, $actual->code);
        self::assertStringContainsString($body, $actual->body);
        self::assertEquals($body, $actual->body);
    }

    /**
     * @test the request method
     *
     * @throws CredentialException this should happen
     * @throws LogicException      this should NOT happen
     * @throws NetworkException    this should NOT happen
     */
    public function nonExistentVerb(): void
    {
        self::expectException(CredentialException::class);
        self::expectExceptionMessage('fail: unknown verb');

        $this->requester->request(['verb' => 'foo']);
    }

    /**
     * @test the request method
     *
     * @throws CredentialException this should happen
     * @throws LogicException      this should NOT happen
     * @throws NetworkException    this should NOT happen
     */
    public function noVerb(): void
    {
        self::expectException(CredentialException::class);
        self::expectExceptionMessage('fail: no verb');

        $this->requester->request([]);
    }

    /**
     * Test valid request.
     *
     * @throws NetworkException    It should NOT happen
     * @throws LogicException      It should NOT happen
     * @throws CredentialException It should NOT happen
     */
    public function testAddCustomer(): void
    {
        $actual = $this->requester->get([
            'verb' => 'addcustomer',
            'customer' => 11111,
        ]);
        self::assertResponse('fail: unknown customer', $actual);

        $actual = $this->requester->get([
            'verb' => 'addcustomer',
            'customer' => 22222,
        ]);
        self::assertResponse('ok', $actual);

        $actual = $this->requester->get([
            'verb' => 'addcustomer',
            'customer' => 33333,
        ]);
        self::assertResponse('fail: customer already exists', $actual);

        $actual = $this->requester->get([
            'verb' => 'addcustomer',
            'customer' => 44444,
        ]);
        self::assertResponse('fail: customer already exists', $actual);
    }

    /**
     * Test valid request.
     *
     * @throws NetworkException    It should NOT happen
     * @throws LogicException      It should NOT happen
     * @throws CredentialException It should NOT happen
     */
    public function testAddCustomerAccount(): void
    {
        $actual = $this->requester->get([
            'verb' => 'addcustomeraccount',
            'customer' => 11111,
            'credits' => 10,
        ]);
        self::assertResponse('fail: this is not a personal customer of you', $actual);

        $actual = $this->requester->get([
            'verb' => 'addcustomeraccount',
            'customer' => 22222,
        ]);
        self::assertResponse('fail: this is not a personal customer of you', $actual);

        $actual = $this->requester->get([
            'verb' => 'addcustomeraccount',
            'customer' => 33333,
            'credits' => 20,
        ]);
        self::assertResponse('ok: 62', $actual);

        $actual = $this->requester->get([
            'verb' => 'addcustomeraccount',
            'customer' => 44444,
            'credits' => 10,
        ]);
        self::assertResponse('ok: 52', $actual);
    }

    /**
     * Test valid request.
     *
     * @throws NetworkException    It should NOT happen
     * @throws LogicException      It should NOT happen
     * @throws CredentialException It should NOT happen
     */
    public function testCheckCustomer(): void
    {
        $actual = $this->requester->get([
            'verb' => 'checkcustomer',
            'customer' => 11111,
        ]);
        self::assertResponse('fail: this is not a personal customer of you', $actual);

        $actual = $this->requester->get([
            'verb' => 'checkcustomer',
            'customer' => 22222,
        ]);
        self::assertResponse('fail: this is not a personal customer of you', $actual);

        $actual = $this->requester->get([
            'verb' => 'checkcustomer',
            'customer' => 33333,
        ]);
        self::assertResponse('ok', $actual);

        $actual = $this->requester->get([
            'verb' => 'checkcustomer',
            'customer' => 44444,
        ]);
        self::assertResponse('ok', $actual);
    }

    /**
     * Test valid request.
     *
     * @throws NetworkException    It should NOT happen
     * @throws LogicException      It should NOT happen
     * @throws CredentialException It should NOT happen
     */
    public function testCheckEvcCustomer(): void
    {
        $actual = $this->requester->get([
            'verb' => 'checkevccustomer',
            'customer' => 11111,
        ]);
        self::assertResponse('fail: unknown evc customer', $actual);

        $actual = $this->requester->get([
            'verb' => 'checkevccustomer',
            'customer' => 22222,
        ]);
        self::assertResponse('ok: evc customer exists', $actual);

        $actual = $this->requester->get([
            'verb' => 'checkevccustomer',
            'customer' => 33333,
        ]);
        self::assertResponse('ok: evc customer exists', $actual);

        $actual = $this->requester->get([
            'verb' => 'checkevccustomer',
            'customer' => 44444,
        ]);
        self::assertResponse('ok: evc customer exists', $actual);
    }

    /**
     * When I use the 66666 customer, Emulation service throws a CredenditalException.
     *
     * @throws NetworkException    It should NOT happen
     * @throws LogicException      It should NOT happen
     * @throws CredentialException It should happen
     */
    public function testCredentialException(): void
    {
        self::expectException(CredentialException::class);
        self::expectExceptionMessage(
            'Credential exception: you called the 66666 customer.'
        );
        $this->requester->request([
            'verb' => 'foo',
            'customer' => 66666,
        ]);
    }

    /**
     * Test valid request.
     *
     * @throws NetworkException    It should NOT happen
     * @throws LogicException      It should NOT happen
     * @throws CredentialException It should NOT happen
     */
    public function testGetCustomerAccount(): void
    {
        $actual = $this->requester->get([
            'verb' => 'getcustomeraccount',
            'customer' => 11111,
        ]);
        self::assertResponse('fail: this is not a personal customer of you', $actual);

        $actual = $this->requester->get([
            'verb' => 'getcustomeraccount',
            'customer' => 22222,
        ]);
        self::assertResponse('fail: this is not a personal customer of you', $actual);

        $actual = $this->requester->get([
            'verb' => 'getcustomeraccount',
            'customer' => 33333,
        ]);
        self::assertResponse('ok: 42', $actual);

        $actual = $this->requester->get([
            'verb' => 'getcustomeraccount',
            'customer' => 44444,
        ]);
        self::assertResponse('ok: 42', $actual);
    }

    /**
     * Test valid request.
     *
     * @throws NetworkException    It should NOT happen
     * @throws LogicException      It should NOT happen
     * @throws CredentialException It should NOT happen
     */
    public function testGetRecentPurchases(): void
    {
        $expected = file_get_contents(__DIR__.'/../../Resources/tests/get-purchase.txt');
        $actual = $this->requester->get([
            'verb' => 'getrecentpurchases',
        ]);
        self::assertResponse($expected, $actual);
    }

    /**
     * Test valid request.
     *
     * @throws NetworkException    It should NOT happen
     * @throws LogicException      It should NOT happen
     * @throws CredentialException It should NOT happen
     */
    public function testListCustomers(): void
    {
        $expected = file_get_contents(__DIR__.'/../../Resources/tests/get-customers.txt');
        $actual = $this->requester->get([
            'verb' => 'listcustomers',
        ]);
        self::assertResponse($expected, $actual);
    }

    /**
     * When I use the 77777 customer, Emulation service throws a LogicException.
     *
     * @throws NetworkException    It should NOT happen
     * @throws LogicException      It should happen
     * @throws CredentialException It should NOT happen
     */
    public function testLogicException(): void
    {
        self::expectException(LogicException::class);
        self::expectExceptionMessage(
            'Logic exception: you called the 77777 customer.'
        );
        $this->requester->request([
            'verb' => 'foo',
            'customer' => 77777,
        ]);
    }

    /**
     * When I use the 55555 customer, Emulation service throws a NetworkException.
     *
     * @throws NetworkException    It should happen
     * @throws LogicException      It should NOT happen
     * @throws CredentialException It should NOT happen
     */
    public function testNetworkException(): void
    {
        self::expectException(NetworkException::class);
        self::expectExceptionMessage(
            'Network exception: you called the 55555 customer.'
        );
        $this->requester->request([
            'verb' => 'foo',
            'customer' => 55555,
        ]);
    }

    /**
     * @test a full valid request
     *
     * @throws NetworkException    It should NOT happen
     * @throws LogicException      It should NOT happen
     * @throws CredentialException It should NOT happen
     */
    public function testRequest(): void
    {
        $actual = $this->requester->request([
            'verb' => 'checkevccustomer',
            'customer' => 33333,
        ]);
        self::assertResponse('ok: evc customer exists', $actual);
    }

    /**
     * Test valid request.
     *
     * @throws NetworkException    It should NOT happen
     * @throws LogicException      It should NOT happen
     * @throws CredentialException It should NOT happen
     */
    public function testSetCustomerAccount(): void
    {
        $actual = $this->requester->get([
            'verb' => 'setcustomeraccount',
            'customer' => 11111,
            'credits' => 10,
        ]);
        self::assertResponse('fail: this is not a personal customer of you', $actual);

        $actual = $this->requester->get([
            'verb' => 'setcustomeraccount',
            'customer' => 22222,
        ]);
        self::assertResponse('fail: this is not a personal customer of you', $actual);

        $actual = $this->requester->get([
            'verb' => 'setcustomeraccount',
            'customer' => 33333,
            'credits' => 20,
        ]);
        self::assertResponse('ok: 20', $actual);

        $actual = $this->requester->get([
            'verb' => 'setcustomeraccount',
            'customer' => 44444,
            'credits' => 10,
        ]);
        self::assertResponse('ok: 10', $actual);
    }
}
