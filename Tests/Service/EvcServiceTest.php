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

namespace Alexandre\Evc\Tests\Service;

use Alexandre\Evc\Exception\EvcException;
use Alexandre\Evc\Service\EvcService;
use AspectMock\Test as test;
use Exception;
use PHPUnit\Framework\TestCase;
use Unirest\Request;
use Unirest\Response;

/**
 * @internal
 * @coversDefaultClass \Alexandre\Evc\Service\EvcService
 */
class EvcServiceTest extends TestCase
{
    /**
     * @var EvcService
     */
    private $evcService;

    /**
     * Initialize evc service before each test.
     */
    public function setUp(): void
    {
        $this->evcService = new EvcService('http://example.org/url', 'api', 'username', 'password');
    }

    /**
     * Tear Down.
     *
     * Remove all registered test::double.
     */
    protected function tearDown(): void
    {
        test::clean(); // remove all registered test doubles
    }

    /**
     * @test
     * @covers ::checkAccount
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should not happen
     */
    public function accountShouldBeHuge(): void
    {
        $response = new Response(200, 'ok: 9876543210', '', []);
        $request = test::double(Request::class, ['get' => $response]);
        self::assertEquals(9876543210, $this->evcService->checkAccount('33333'));
        $request->verifyInvokedOnce('get');
    }

    /**
     * @test
     * @covers ::checkAccount
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should not happen
     */
    public function accountShouldBeNegative(): void
    {
        $response = new Response(200, 'ok: -8', '', []);
        $request = test::double(Request::class, ['get' => $response]);
        self::assertEquals(-8, $this->evcService->checkAccount('33333'));
        $request->verifyInvokedOnce('get');
    }

    /**
     * @test
     * @covers ::checkAccount
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should not happen
     */
    public function accountShouldBeZero(): void
    {
        $response = new Response(200, 'ok: 0', '', []);
        $request = test::double(Request::class, ['get' => $response]);
        self::assertEquals(0, $this->evcService->checkAccount('33333'));
        $request->verifyInvokedOnce('get');
    }

    /**
     * @test
     * @covers ::checkAccount
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should happen
     */
    public function accountShouldNotBeAccessible(): void
    {
        $response = new Response(200, 'fail: this is not a personal customer of you', '', []);
        $request = test::double(Request::class, ['get' => $response]);

        self::expectException(EvcException::class);
        self::expectExceptionMessage('Evc error: fail: this is not a personal customer of you');

        $this->evcService->checkAccount('33333');
        $request->verifyInvokedOnce('get');
    }

    /**
     * @test
     * @covers ::addCredit
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should not happen
     */
    public function customerShouldBeCredited(): void
    {
        $response = new Response(200, 'ok: 123', '', []);
        $request = test::double(Request::class, ['get' => $response]);
        self::assertEquals(123, $this->evcService->addCredit('33333', 78));
        $request->verifyInvokedOnce('get');
    }

    /**
     * @test
     * @covers ::addCredit
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should happen
     */
    public function customerShouldNotBeCredited(): void
    {
        $response = new Response(200, 'ok: foobar', '', []);
        $request = test::double(Request::class, ['get' => $response]);

        self::expectException(EvcException::class);
        self::expectExceptionMessage('Evc error: ok: foobar');

        self::assertEquals(123, $this->evcService->addCredit('33333', 78));
        $request->verifyInvokedOnce('get');
    }

    /**
     * @test
     * @covers ::exists
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should not happen
     */
    public function existsShouldReturnFalse(): void
    {
        $response = new Response(200, 'fail: unknown evc customer', '', []);
        $request = test::double(Request::class, ['get' => $response]);
        self::assertFalse($this->evcService->exists('33333'));
        $request->verifyInvokedOnce('get');
    }

    /**
     * @test
     * @covers ::exists
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should not happen
     */
    public function existsShouldReturnTrue(): void
    {
        $response = new Response(200, 'ok: evc customer exists', '', []);
        $request = test::double(Request::class, ['get' => $response]);
        self::assertTrue($this->evcService->exists('33333'));
        $request->verifyInvoked('get');
    }

    /**
     * @test
     * @covers ::exists
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should happen
     */
    public function existsShouldThrowAnotherException(): void
    {
        $response = new Response(500, 'foo bar', '', []);
        $request = test::double(Request::class, ['get' => $response]);

        self::expectException(EvcException::class);
        self::expectExceptionMessage('Evc return a response with code 500');

        $this->evcService->exists('33333');
        $request->verifyInvokedOnce('get');
    }

    /**
     * @test
     * @covers ::exists
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should happen
     */
    public function existsShouldThrowException(): void
    {
        $response = new Response(200, 'fail: no user authorization', '', []);
        $request = test::double(Request::class, ['get' => $response]);

        self::expectException(EvcException::class);
        self::expectExceptionMessage('fail: no user authorization');

        $this->evcService->exists('33333');
        $request->verifyInvokedOnce('get');
    }

    /**
     * @test
     * @covers ::createPersonalCustomer
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should not happen
     */
    public function personalCustomerShouldBeCreated(): void
    {
        $response = new Response(200, 'ok: customer added', '', []);
        $request = test::double(Request::class, ['get' => $response]);
        $this->evcService->createPersonalCustomer('33333');
        $request->verifyInvokedOnce('get');
        self::assertTrue(true); // Mark test as done.
    }

    /**
     * @test
     * @covers ::createPersonalCustomer
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should happen
     */
    public function personalCustomerShouldNotBeCreated(): void
    {
        $response = new Response(200, 'fail: customer already exists', '', []);
        $request = test::double(Request::class, ['get' => $response]);

        self::expectException(EvcException::class);
        self::expectExceptionMessage('Evc error: fail: customer already exists');

        $this->evcService->createPersonalCustomer('33333');
        $request->verifyInvokedOnce('get');
    }
}
