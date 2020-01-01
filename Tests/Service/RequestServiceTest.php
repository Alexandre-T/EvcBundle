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

use Alexandre\EvcBundle\Exception\EvcException;
use Alexandre\EvcBundle\Service\RequestService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Unirest\Response;

/**
 * @internal
 * @coversDefaultClass
 */
class RequestServiceTest extends TestCase
{
    /**
     * The instance to test.
     *
     * @var RequestService
     */
    private $requester;

    /**
     * Setup the requester before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->requester = new RequestService(
            'http://example.org/url',
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
     * Test the request method.
     *
     * @throws EvcException this should happened
     */
    public function testAnotherBadRequest(): void
    {
        $code = 200;
        $body = 'fail: foo';
        $mock = self::getResponseMocked($body, $code);

        self::expectException(EvcException::class);
        self::expectExceptionMessage('Evc error: fail: foo');
        $mock->request([]);
    }

    /**
     * Test the request method returning an error 500.
     *
     * @throws EvcException this should happened
     */
    public function testBadRequest(): void
    {
        $code = 500;
        $body = 'ok: foo';
        $mock = self::getResponseMocked($body, $code);

        self::expectException(EvcException::class);
        self::expectExceptionMessage('Evc return a response with code 500');
        $mock->request([]);
    }

    /**
     * Test the get method.
     */
    public function testGet(): void
    {
        self::assertInstanceOf(Response::class, $this->requester->get([]));
    }

    /**
     * Test the isFailing method.
     */
    public function testIsFailing(): void
    {
        self::assertTrue($this->requester->isFailing('fail: '));
        self::assertTrue($this->requester->isFailing('fail: no data'));
        self::assertTrue($this->requester->isFailing('fail: error'));
        self::assertFalse($this->requester->isFailing('ok: 42'));
        self::assertFalse($this->requester->isFailing('ok: customer created'));
    }

    /**
     * Return a mocked object of RequestService.
     *
     * The private get method will return a Response without launching a request.
     *
     * @param string $body content of the response returned
     * @param int    $code the code of the response
     *
     * @return MockObject|RequestService
     */
    private function getResponseMocked(string $body, int $code): MockObject
    {
        $params = [
            'url' => 'http://example.org/url',
            'api' => 'api-id',
            'username' => '33333',
            'password' => 'foobar',
        ];

        $response = new Response($code, $body, null, []);
        $mock = $this->getMockBuilder(RequestService::class)
            ->setConstructorArgs($params)
            ->onlyMethods(['get'])
            ->getMock()
        ;

        $mock->expects(self::once())
            ->method('get')
            ->willReturn($response)
        ;

        return $mock;
    }
}
