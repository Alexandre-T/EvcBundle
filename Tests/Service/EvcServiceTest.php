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

    protected function tearDown(): void
    {
        test::clean(); // remove all registered test doubles
    }

    /**
     * @test
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
}
