<?php
/**
 * This file is part of the Evc Bundle.
 *
 * PHP version 7.1|7.2|7.3|7.4
 * Symfony version 4.4|5.0
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * @author    Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @copyright 2020 Alexandre Tranchant
 * @license   Cecill-B http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 */

namespace Alexandre\Evc\Service;

use Alexandre\Evc\Exception\EvcException;
use Alexandre\Evc\Model\Purchase;
use Unirest\Request;
use Unirest\Response;

class EvcService implements EvcServiceInterface
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $api;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

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
     * @inheritDoc
     */
    public function addCredit(string $customer, int $credit)
    {
        // TODO: Implement addCredit() method.
    }

    /**
     * Check a personal account balance.
     *
     * @param string $customer the customer id
     *
     * @return int
     *
     * @throws EvcException when an error occured
     */
    public function checkAccount(string $customer): int
    {
        // TODO: Implement checkAccount() method.
    }

    /**
     * @inheritDoc
     */
    public function createPersonalCustomer(string $customer)
    {
        // TODO: Implement createPersonalCustomer() method.
    }

    /**
     * @inheritDoc
     * @throws EvcException
     */
    public function exists(string $customer): bool
    {
        $params = [
            'verb' => 'checkevccustomer',
            'customer' => $customer
        ];
        $response = $this->getRequest($params);

        if (200 !== $response->code) {
            throw new EvcException(sprintf('Evc return a response with code %d', $response->code));
        }

        //Body of a good request
        //ok: evc customer exists
        //Bodies of bad requests
        //fail: unknown evc customer
        //fail: no user authorization
        //fail: no api authorization

        if (trim($response->body === 'ok: evc customer exists')) {
            return true;
        }

        if (trim($response->body === 'fail: unknown evc customer')) {
            return false;
        }

        throw new EvcException(sprintf('Evc error: %s', $response->body));
    }

    /**
     * @inheritDoc
     */
    public function setCredit(string $customer, int $credit)
    {
        // TODO: Implement setCredit() method.
    }

    /**
     * @inheritDoc
     */
    public function getPurchases(string $customer, int $days): array
    {
        // TODO: Implement getPurchases() method.
    }

    /**
     * Create the URL from check evc customer.
     *
     * @param string $customer the customer id
     *
     * @return string
     */
    private function createCheckEvcCustomer(string $customer): string
    {
        return $this->createRequest([
            'verb' => 'checkevccustomer',
            'customer' => urlencode($customer)
        ]);
    }

    /**
     * Create the full url for the request.
     *
     * @param array $params
     *
     * @return string
     */
    private function createRequest(array $params): string
    {
       $request = "appid={$this->api}&username={$this->username}&password={$this->password}";

       foreach($params as $key => $parameter) {
           $request .= '&' . urlencode($key) . '=' . urlencode($parameter);
       }

       return $this->url . '?' . $request;
    }

    /**
     * Return the result of Request.
     *
     * @param array  $params each params is a set of name and value
     *
     * @return Response
     */
    private function getRequest(array $params): Response
    {
        $headers = $this->getHeaders();
        $params += $this->getParams();
        $request = $this->getUrl();

        return Request::get($request, $headers, $params);
    }

    /**
     * Return an array of headers.
     *
     * @return array
     */
    private function getHeaders(): array
    {
        return [];
    }

    /**
     * Return array of default params
     * @return array
     */
    private function getParams(): array
    {
        return [
            'apiid' => $this->api,
            'password' => $this->password,
            'username' => $this->username,
        ];
    }

    private function getUrl(): string
    {
        return $this->url;
    }
}