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
use Alexandre\Evc\Model\Purchase;
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
     * Add some credits to customer balance.
     *
     * This is the recommended way for adding/removing points. If the customer purchases at the *same* time,
     * the balance will still be correct.
     *
     * The "credits" value can be negative, so you can subtract with this command as well.
     *
     * @param string $customer the customer id
     * @param int    $credit   the positive or negative number of credits to add (or remove)
     *
     * @throws EvcException when an error occured
     *
     * @return int the new account balance
     */
    public function addCredit(string $customer, int $credit): int
    {
        $params = [
            'verb' => 'addcustomeraccount',
            'customer' => $customer,
            'credits' => $credit,
        ];
        $response = $this->getRequest($params);

        $result = preg_match('/^ok:\s([-+]?\d+)/', $response->body, $matches);

        if (1 === $result && 2 === count($matches)) {
            return (int) $matches[1];
        }

        throw new EvcException(sprintf('Evc error: %s', trim($response->body)));
    }

    /**
     * Check a personal account balance.
     *
     * @param string $customer the customer id
     *
     * @throws EvcException when an error occured
     */
    public function checkAccount(string $customer): int
    {
        $params = [
            'verb' => 'getcustomeraccount',
            'customer' => $customer,
        ];
        $response = $this->getRequest($params);

        $result = preg_match('/^ok:\s([-+]?\d+)/', $response->body, $matches);

        if (1 === $result && 2 === count($matches)) {
            return (int) $matches[1];
        }

        throw new EvcException(sprintf('Evc error: %s', trim($response->body)));
    }

    /**
     * Create a personal customer.
     *
     * A personal customer must already as EVC customer. Making him a personal customer allows him to see your reseller
     * files and creates a personal account balance relationship with you. Only add customers who asked for this.
     *
     * @param string $customer the customer id
     *
     * @throws EvcException when creating personal customer failed
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
     * Does this customer exists.
     *
     * Note: This doesn't check if this is a personal customer of you. It checks the EVC customer base.
     *
     * @param string $customer the customer id
     *
     * @throws EvcException when an error occured when accessing EVC API
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
     * Returns a collection of the purchases performed in the last X (up to 99) days.
     *
     * @param string $customer the customer id
     * @param int    $days     the number of
     *
     * @return Purchase[]
     */
    public function getPurchases(string $customer, int $days): array
    {
        // TODO: Implement getPurchases() method.
        return [];
    }

    /**
     * Setting a personal account balance.
     *
     * This is not recommended for adding/removing points because the customer might purchase at the same time,
     * causing a wrong balance.
     *
     * @param string $customer the customer id
     * @param int    $credit   the new account balance
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

    /**
     * URL getter.
     */
    private function getUrl(): string
    {
        return $this->url;
    }
}
