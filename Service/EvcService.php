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

namespace Alexandre\EvcBundle\Service;

use Alexandre\EvcBundle\Exception\EvcException;
use Alexandre\EvcBundle\Model\Purchase;
use Unirest\Request;
use Unirest\Response;

/**
 * Class Evc Service is requesting the API evc.de and analyses responses.
 */
class EvcService implements EvcServiceInterface
{
    public const DAYS_MAX = 99;
    public const DAYS_MIN = 1;

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
     * @param int $customer the customer id
     * @param int $credit   the positive or negative number of credits to add (or remove)
     *
     * @throws EvcException when an error occurred
     *
     * @return int the new account balance
     */
    public function addCredit(int $customer, int $credit): int
    {
        $params = [
            'verb' => 'addcustomeraccount',
            'customer' => $customer,
            'credits' => $credit,
        ];
        $response = $this->getRequest($params);

        $result = preg_match('/^ok:\s([-+]?\d+)/', $response->body, $matches);

        $this->checkResult($result, $matches, $response->body);

        return (int) $matches[1];
    }

    /**
     * Check a personal account balance.
     *
     * @param int $customer the customer id
     *
     * @throws EvcException when an error occurred
     */
    public function checkAccount(int $customer): int
    {
        $params = [
            'verb' => 'getcustomeraccount',
            'customer' => $customer,
        ];
        $response = $this->getRequest($params);

        $result = preg_match('/^ok:\s([-+]?\d+)/', $response->body, $matches);

        $this->checkResult($result, $matches, $response->body);

        return (int) $matches[1];
    }

    /**
     * Create a personal customer.
     *
     * A personal customer must already as EVC customer. Making him a personal customer allows him to see your reseller
     * files and creates a personal account balance relationship with you. Only add customers who asked for this.
     *
     * @param int $customer the customer id
     *
     * @throws EvcException when creating personal customer failed
     */
    public function createPersonalCustomer(int $customer): void
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
     * @param int $customer the customer id
     *
     * @throws EvcException when an error occurred when accessing EVC API
     */
    public function exists(int $customer): bool
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
     * @param int      $days     the number of
     * @param int|null $customer filter purchases on specified customer id
     *
     * @throws EvcException when an error occurred when requesting EVC
     *
     * @return Purchase[]
     */
    public function getPurchases(int $days = 99, int $customer = null): array
    {
        $this->checkDays($days);

        $params = [
            'verb' => 'checkevccustomer',
            'customer' => $customer,
        ];
        $response = $this->getRequest($params);

        $result = preg_match('/^ok:\sJSON\sfollows\s([\S\s]+)/', $response->body, $matches);

        $this->checkResult($result, $matches, $response->body);

        $json = json_decode($matches[1], true);

        $this->checkJson($json);

        $result = [];
        foreach ($json['data'] as $data) {
            $purchase = new Purchase($data);
            if (null === $customer || $purchase->getCustomer() === $customer) {
                $result[] = $purchase;
            }
        }

        return $result;
    }

    /**
     * Setting a personal account balance.
     *
     * This is not recommended for adding/removing points because the customer might purchase at the same time,
     * causing a wrong balance.
     *
     * @param int $customer the customer id
     * @param int $credit   the new account balance
     *
     * @throws EvcException when an error occurred
     */
    public function setCredit(int $customer, int $credit): void
    {
        $params = [
            'verb' => 'setcustomeraccount',
            'customer' => $customer,
            'credits' => $credit,
        ];
        $response = $this->getRequest($params);

        $result = preg_match('/^ok: ([-+]?\\d+)$/', $response->body, $matches);

        $this->checkResult($result, $matches, $response->body);
    }

    /**
     * Check that days are between minimum and maximum.
     *
     * @param int $days the number provided
     *
     * @throws EvcException when days are not in range
     */
    private function checkDays(int $days): void
    {
        if ($days < self::DAYS_MIN || $days > self::DAYS_MAX) {
            throw new EvcException('Evc error: days shall be between 1 and 99');
        }
    }

    /**
     * Check that the json file is valid.
     *
     * @param string $json provided by preg_match function
     *
     * @throws EvcException when json is not valid
     */
    private function checkJson($json): void
    {
        if (!is_array($json)) {
            throw new EvcException(sprintf('Evc error: Json from evc.de is not a valid JSON'));
        }

        if (!isset($json['data']) || !is_array($json['data'])) {
            throw new EvcException(sprintf('Evc error: Json from evc.de does not contain data'));
        }
    }

    /**
     * Check result of preg_match function.
     *
     * @param int    $result  result of preg_match function
     * @param array  $matches matches created by preg_match function
     * @param string $body    body of request
     *
     * @throws EvcException when the result of preg_match is not valid
     */
    private function checkResult(int $result, $matches, string $body): void
    {
        if (1 !== $result || !is_array($matches) || 2 !== count($matches)) {
            throw new EvcException(sprintf('Evc error: %s', trim($body)));
        }
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
