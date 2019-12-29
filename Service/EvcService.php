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

namespace Alexandre\Evc\Service;

use Alexandre\Evc\Exception\EvcException;
use Unirest\Request;
use Unirest\Response;

class EvcService implements EvcServiceInterface
{
    /**
     * @var string
     */
    private $api;

    /**
     * @var string
     */
    private $password;
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $username;

    /**
     * EvcService constructor.
     *
     * @param string $url      api url
     * @param string $api      api version provided by evc.de
     * @param string $username your username
     * @param string $password your api password
     */
    public function __construct(string $url, string $api, string $username, string $password)
    {
        $this->url = $url;
        $this->api = urlencode($api);
        $this->username = urlencode($username);
        $this->password = urlencode($password);
    }

    /**
     * {@inheritdoc}
     */
    public function addCredit(string $customer, int $credit): void
    {
        // TODO: Implement addCredit() method.
    }

    /**
     * Check a personal account balance.
     *
     * @param string $customer the customer id
     *
     * @throws EvcException when an error occured
     *
     * @return int
     */
    public function checkAccount(string $customer): int
    {
        // TODO: Implement checkAccount() method.
        return 0;
    }

    /**
     * {@inheritdoc}
     *
     * @throws EvcException when creating personal customer failed.
     */
    public function createPersonalCustomer(string $customer): void
    {
        $params = [
            'verb' => 'checkevccustomer',
            'customer' => $customer,
        ];
        $response = $this->getRequest($params);

        if ('ok: customer added' !== trim($response->body)) {
            throw new EvcException(sprintf('Evc error: %s', trim($response->body)));
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws EvcException
     */
    public function exists(string $customer): bool
    {
        $params = [
            'verb' => 'checkevccustomer',
            'customer' => $customer,
        ];
        $response = $this->getRequest($params);

        if ('ok: evc customer exists' === trim($response->body)) {
            return true;
        }

        if ('fail: unknown evc customer' === trim($response->body)) {
            return false;
        }

        throw new EvcException(sprintf('Evc error: %s', trim($response->body)));
    }

    /**
     * {@inheritdoc}
     */
    public function getPurchases(string $customer, int $days): array
    {
        // TODO: Implement getPurchases() method.
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function setCredit(string $customer, int $credit): void
    {
        // TODO: Implement setCredit() method.
    }

    /**
     * Return an array of headers.
     */
    private function getHeaders(): array
    {
        return [];
    }

    /**
     * Return array of default params.
     */
    private function getParams(): array
    {
        return [
            'apiid' => $this->api,
            'password' => $this->password,
            'username' => $this->username,
        ];
    }

    /**
     * Return the result of Request.
     *
     * @param array $params each params is a set of name and value
     *
     * @throws EvcException when response code is different from 200
     *
     * @return Response
     */
    private function getRequest(array $params): Response
    {
        $headers = $this->getHeaders();
        $params += $this->getParams();
        $request = $this->getUrl();

        $response = Request::get($request, $headers, $params);

        if (200 !== $response->code) {
            throw new EvcException(sprintf('Evc return a response with code %d', $response->code));
        }

        return $response;
    }

    private function getUrl(): string
    {
        return $this->url;
    }
}
