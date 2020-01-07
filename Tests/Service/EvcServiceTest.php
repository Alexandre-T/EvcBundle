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
use Alexandre\EvcBundle\Exception\LogicException;
use Alexandre\EvcBundle\Exception\NetworkException;
use Alexandre\EvcBundle\Model\Purchase;
use Alexandre\EvcBundle\Service\EvcService;
use Alexandre\EvcBundle\Service\RequestService;
use Alexandre\EvcBundle\Service\RequestServiceInterface;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Unirest\Response;

/**
 * @internal
 * @coversDefaultClass \Alexandre\EvcBundle\Service\EvcService
 */
class EvcServiceTest extends TestCase
{
    /**
     * @var EvcService
     */
    private $evcService;

    /**
     * @var MockObject|RequestServiceInterface
     */
    private $requester;

    /**
     * Initialize evc service before each test.
     */
    public function setUp(): void
    {
        $params = [
            'url' => 'http://example.org/url',
            'api' => 'api-id',
            'username' => '33333',
            'password' => 'foobar',
        ];

        $this->requester = $this->getMockBuilder(RequestService::class)
            ->setConstructorArgs($params)
            ->onlyMethods(['get'])
            ->getMock()
        ;

        $this->evcService = new EvcService($this->requester);
    }

    /**
     * Tear Down.
     *
     * Remove all registered test::double.
     */
    protected function tearDown(): void
    {
        $this->requester = null;
        $this->evcService = null;

        parent::tearDown();
    }

    /**
     * Return a file.
     *
     * @param string $filename the filename in /Resources/tests subdirectory
     *
     * @return false|string
     */
    private static function getMockedFile(string $filename)
    {
        $filename = basename($filename);

        return file_get_contents(__DIR__.'/../../Resources/tests/'.$filename);
    }

    /**
     * @test
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should not happen
     */
    public function accountShouldBeHuge(): void
    {
        $response = new Response(200, 'ok: 9876543210', '', []);
        $this->setMockedResponse($response);

        self::assertEquals(9876543210, $this->evcService->checkAccount(33333));
    }

    /**
     * @test
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should not happen
     */
    public function accountShouldBeNegative(): void
    {
        $response = new Response(200, 'ok: -8', '', []);
        $this->setMockedResponse($response);

        self::assertEquals(-8, $this->evcService->checkAccount(33333));
    }

    /**
     * @test
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should not happen
     */
    public function accountShouldBeZero(): void
    {
        $response = new Response(200, 'ok: 0', '', []);
        $this->setMockedResponse($response);

        self::assertEquals(0, $this->evcService->checkAccount(33333));
    }

    /**
     * @test
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should happen
     */
    public function accountShouldNotBeAccessible(): void
    {
        $response = new Response(200, 'fail: this is not a personal customer of you', '', []);
        $this->setMockedResponse($response);

        self::expectException(EvcException::class);
        self::expectExceptionMessage('fail: this is not a personal customer of you');

        $this->evcService->checkAccount(33333);
    }

    /**
     * @test
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should not happen
     */
    public function customerShouldBeCredited(): void
    {
        $response = new Response(200, 'ok: 123', '', []);
        $this->setMockedResponse($response);

        self::assertEquals(123, $this->evcService->addCredit(33333, 78));
    }

    /**
     * @test
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should happen
     */
    public function customerShouldNotBeCredited(): void
    {
        $response = new Response(200, 'ok: foobar', '', []);
        $this->setMockedResponse($response);

        self::expectException(EvcException::class);
        self::expectExceptionMessage('Unexpected message from evc: ok: foobar');

        self::assertEquals(123, $this->evcService->addCredit(33333, 78));
    }

    /**
     * @test
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should not happen
     */
    public function existsShouldReturnFalse(): void
    {
        $response = new Response(200, 'fail: unknown evc customer', '', []);
        $this->setMockedResponse($response);

        self::assertFalse($this->evcService->exists(33333));
    }

    /**
     * @test
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should not happen
     */
    public function existsShouldReturnTrue(): void
    {
        $response = new Response(200, 'ok: evc customer exists', '', []);
        $this->setMockedResponse($response);

        self::assertTrue($this->evcService->exists(33333));
    }

    /**
     * @test
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should happen
     */
    public function existsShouldThrowAnotherException(): void
    {
        $response = new Response(500, 'foo bar', '', []);
        $this->setMockedResponse($response);

        self::expectException(NetworkException::class);
        self::expectExceptionMessage('Evc API returns a response with code 500');

        $this->evcService->exists(33333);
    }

    /**
     * @test
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should happen
     */
    public function existsShouldThrowException(): void
    {
        $response = new Response(200, 'fail: no user authorization', '', []);
        $this->setMockedResponse($response);

        self::expectException(EvcException::class);
        self::expectExceptionMessage('fail: no user authorization');

        $this->evcService->exists(33333);
    }

    /**
     * @test
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should happen
     */
    public function existsShouldThrowUnexpectedException(): void
    {
        $response = new Response(200, 'ok: foo bar', '', []);
        $this->setMockedResponse($response);

        self::expectException(EvcException::class);
        self::expectExceptionMessage('Unexpected evc message: ok: foo bar');

        $this->evcService->exists(33333);
    }

    /**
     * @test
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should not happen
     */
    public function getPurchasesEmpty(): void
    {
        $content = self::getMockedFile('empty-purchase.txt');
        $response = new Response(200, $content, '', []);
        $this->setMockedResponse($response);

        $purchases = $this->evcService->getPurchases(10);

        self::assertIsArray($purchases);
        self::assertCount(0, $purchases);
    }

    /**
     * @test
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should happen
     */
    public function getPurchasesFailed(): void
    {
        $response = new Response(200, 'fail: foo bar', '', []);
        $this->setMockedResponse($response);

        self::expectException(EvcException::class);
        self::expectExceptionMessage('fail: foo bar');

        $this->evcService->getPurchases(10);
    }

    /**
     * @test
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should happen
     */
    public function getPurchasesFailedWhenDaysAreTooGreat(): void
    {
        self::expectException(EvcException::class);
        self::expectExceptionMessage('days shall be between 1 and 99');

        $this->evcService->getPurchases(150);
    }

    /**
     * @test
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should happen
     */
    public function getPurchasesFailedWhenDaysAreTooSmall(): void
    {
        self::expectException(EvcException::class);
        self::expectExceptionMessage('days shall be between 1 and 99');

        $this->evcService->getPurchases(0);
    }

    /**
     * @test
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should happen
     */
    public function getPurchasesJsonWithNoData(): void
    {
        $content = self::getMockedFile('empty-json.txt');
        $response = new Response(200, $content, '', []);
        $this->setMockedResponse($response);

        self::expectException(EvcException::class);
        self::expectExceptionMessage('Json from evc.de does not contain data');
        $this->evcService->getPurchases(10);
    }

    /**
     * @test
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should not happen
     */
    public function getPurchasesWithFilter(): void
    {
        $actual = $expected = 33333;
        $content = self::getMockedFile('get-purchase.txt');
        $response = new Response(200, $content, '', []);
        $this->setMockedResponse($response);

        $purchases = $this->evcService->getPurchases(10, $actual);

        self::assertIsArray($purchases);
        self::assertCount(1, $purchases);
        self::assertInstanceOf(Purchase::class, $purchases[0]);

        self::assertEquals($expected, $purchases[0]->getCustomer());
    }

    /**
     * @test
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should happen
     */
    public function getPurchasesWithNonValidJson(): void
    {
        $content = self::getMockedFile('invalid-json.txt');
        $response = new Response(200, $content, '', []);
        $this->setMockedResponse($response);

        self::expectException(EvcException::class);
        self::expectExceptionMessage('Json from evc.de is not a valid JSON');
        $this->evcService->getPurchases(10);
    }

    /**
     * @test
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should not happen
     */
    public function getPurchasesWithoutFilter(): void
    {
        $content = self::getMockedFile('get-purchase.txt');
        $response = new Response(200, $content, '', []);
        $this->setMockedResponse($response);

        $purchases = $this->evcService->getPurchases(10);

        self::assertIsArray($purchases);
        self::assertCount(2, $purchases);
        foreach ($purchases as $purchase) {
            self::assertInstanceOf(Purchase::class, $purchase);
            self::assertIsArray($purchase->getOptions());
            self::assertEmpty($purchase->getOptions());
        }

        self::assertNotEquals($purchases[0]->getCustomer(), $purchases[1]->getCustomer());
    }

    /**
     * @test
     *
     * IsPersonal method should return true
     *
     * @throws EvcException this should not happen
     */
    public function isPersonalOnAnExistentCustomer(): void
    {
        $response = new Response(200, 'ok', '', []);
        $this->setMockedResponse($response);

        self::assertTrue($this->evcService->isPersonal(42));
    }

    /**
     * @test
     *
     * IsPersonal method should return false
     *
     * @throws EvcException this should not happen
     */
    public function isPersonalOnNonExistentCustomer(): void
    {
        $response = new Response(200, 'fail: this is not a personal customer of you', '', []);
        $this->setMockedResponse($response);

        self::assertFalse($this->evcService->isPersonal(42));
    }

    /**
     * @test
     *
     * IsPersonal method should return true
     *
     * @throws EvcException this should happen
     */
    public function isPersonalWithNonExpectedFailingAnswer(): void
    {
        $response = new Response(200, 'fail: FOOBAR', '', []);
        $this->setMockedResponse($response);

        self::expectException(LogicException::class);
        self::expectExceptionMessage('Unexpected evc message: fail: FOOBAR');
        $this->evcService->isPersonal(42);
    }

    /**
     * @test
     *
     * IsPersonal method should throw a EvcException
     *
     * @throws EvcException this should happen
     */
    public function isPersonalWithNonExpectedGoodAnswer(): void
    {
        $response = new Response(200, 'ok: FOOBAR', '', []);
        $this->setMockedResponse($response);

        self::expectException(EvcException::class);
        self::expectExceptionMessage('Unexpected evc message: ok: FOOBAR');
        $this->evcService->isPersonal(42);
    }

    /**
     * @test
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should not happen
     */
    public function personalCustomerShouldBeCreated(): void
    {
        $response = new Response(200, 'ok: customer added', '', []);
        $this->setMockedResponse($response);

        $this->evcService->createPersonalCustomer(33333);
        self::assertTrue(true); // Mark test as done, no exception is thrown.
    }

    /**
     * @test
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should happen
     */
    public function personalCustomerShouldNotBeCreated(): void
    {
        $response = new Response(200, 'fail: customer already exists', '', []);
        $this->setMockedResponse($response);

        self::expectException(EvcException::class);
        self::expectExceptionMessage('fail: customer already exists');

        $this->evcService->createPersonalCustomer(33333);
    }

    /**
     * @test
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should happen
     */
    public function personalCustomerWithAnotherSentence(): void
    {
        $response = new Response(200, 'ok: something unexpected', '', []);
        $this->setMockedResponse($response);

        self::expectException(EvcException::class);
        self::expectExceptionMessage('Unexpected evc message received: ok: something unexpected');

        $this->evcService->createPersonalCustomer(33333);
    }

    /**
     * @test
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should NOT happen
     */
    public function setCredit(): void
    {
        $response = new Response(200, 'ok: 121', '', []);
        $this->setMockedResponse($response);

        $this->evcService->setCredit(33333, 150);
        self::assertTrue(true); //mark test as successful, no exception is thrown
    }

    /**
     * @test
     *
     * @throws Exception    when Aspect Mock is not well initialized
     * @throws EvcException this should happen
     */
    public function setCreditFailed(): void
    {
        $response = new Response(200, 'fail: this is not a personal customer of you', '', []);
        $this->setMockedResponse($response);

        self::expectException(EvcException::class);
        self::expectExceptionMessage('fail: this is not a personal customer of you');

        $this->evcService->setCredit(33333, 150);
    }

    /**
     * Force the requester to return a mocked response.
     *
     * @param Response $response the response returned by mocked requester
     */
    private function setMockedResponse(Response $response): void
    {
        $this->requester
            ->expects(self::once())
            ->method('get')
            ->willReturn($response)
        ;
    }
}
