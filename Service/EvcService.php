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

use Alexandre\EvcBundle\Exception\CredentialException;
use Alexandre\EvcBundle\Exception\LogicException;
use Alexandre\EvcBundle\Exception\NetworkException;
use Alexandre\EvcBundle\Model\Customer;
use Alexandre\EvcBundle\Model\Purchase;

/**
 * Class Evc Service is requesting the API evc.de and analyses responses.
 */
class EvcService implements EvcServiceInterface
{
    /*
     * Max is a limitation of EVC API.
     */
    public const DAYS_MAX = 99;
    public const DAYS_MIN = 1;

    /**
     * The requester.
     *
     * This was created to avoid used of AspectMock
     *
     * @var RequestServiceInterface
     */
    private $requester;

    /**
     * EvcService constructor.
     *
     * @param RequestServiceInterface $requester the initialized requester provided by dependency injection
     */
    public function __construct(RequestServiceInterface $requester)
    {
        $this->requester = $requester;
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
     * @throws LogicException      when a non-expected message is returned by API
     * @throws CredentialException when credentials are not valid
     * @throws NetworkException    when an error occurred while accessing EVC servers
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
        $response = $this->requester->request($params);

        $result = preg_match('/^ok:\s([-+]?\d+)/', $response->body, $matches);

        $this->checkResult($result, $matches, $response->body);

        return (int) $matches[1];
    }

    /**
     * Check a personal account balance.
     *
     * @param int $customer the customer id
     *
     * @throws LogicException      when a non-expected message is returned by API
     * @throws CredentialException when credentials are not valid
     * @throws NetworkException    when an error occurred while accessing EVC servers
     */
    public function checkAccount(int $customer): int
    {
        $params = [
            'verb' => 'getcustomeraccount',
            'customer' => $customer,
        ];
        $response = $this->requester->request($params);

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
     * @throws LogicException      when EVC API returns a non-expected answer
     * @throws CredentialException when bad credentials was sent
     * @throws NetworkException    when a network or curl error occurred
     */
    public function createPersonalCustomer(int $customer): void
    {
        $params = [
            'verb' => 'addcustomer',
            'customer' => $customer,
        ];
        $response = $this->requester->request($params);

        if ('ok: customer added' !== trim($response->body)) {
            throw new LogicException(sprintf('Unexpected evc message received: %s', trim($response->body)));
        }
    }

    /**
     * Does this customer exists.
     *
     * Note: This doesn't check if this is a personal customer of you. It checks the EVC customer base.
     *
     * @param int $customer the customer id
     *
     * @throws LogicException      when EVC API returns a non-expected answer
     * @throws CredentialException when bad credentials was sent
     * @throws NetworkException    when a network or curl error occurred
     */
    public function exists(int $customer): bool
    {
        $params = [
            'verb' => 'checkevccustomer',
            'customer' => $customer,
        ];

        //Do not throws an error on failed.
        try {
            $response = $this->requester->request($params);

            if ('ok: evc customer exists' === $response->body) {
                return true;
            }

            throw new LogicException(sprintf('Unexpected evc message: %s', $response->body));
        } catch (LogicException $exception) {
            if (1 === preg_match('/fail: unknown evc customer$/', $exception->getMessage())) {
                return false;
            }

            throw new LogicException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * Get list of personal customers.
     * A personal customer is an EVC customer which have a personal account balance with current reseller.
     *
     * @throws LogicException      when a non-expected message is returned by API
     * @throws CredentialException when credentials are not valid
     * @throws NetworkException    when an error occurred while accessing EVC servers
     *
     * @return Customer[] An array of customers
     */
    public function getPersonalCustomers(): array
    {
        $params = [
            'verb' => 'listcustomers',
        ];

        $response = $this->requester->request($params);
        $json = $this->getJson($response->body);

        $result = [];

        foreach ($json['data'] as $data) {
            $customer = new Customer($data);
            if (null !== $customer->getIdentifier()) {
                $result[] = $customer;
            }
        }

        return $result;
    }

    /**
     * Returns a collection of the purchases performed in the last X (up to 99) days.
     *
     * @param int      $days     the number of
     * @param int|null $customer filter purchases on specified customer id
     *
     * @throws LogicException      when EVC API returns a non-expected answer
     * @throws CredentialException when bad credentials was sent
     * @throws NetworkException    when a network or curl error occurred
     *
     * @return Purchase[]
     */
    public function getPurchases(int $days = 99, int $customer = null): array
    {
        $this->checkDays($days);

        $params = [
            'verb' => 'getrecentpurchases',
            'customer' => $customer,
        ];

        $response = $this->requester->request($params);
        $json = $this->getJson($response->body);

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
     * Is this customer a personal customer of the current reseller?
     *
     * @param int $customer the customer olsx identifier
     *
     * @throws LogicException      when EVC API returns a non-expected answer
     * @throws CredentialException when bad credentials was sent
     * @throws NetworkException    when a network or curl error occurred
     *
     * @return bool false when the customer have no personal account
     */
    public function isPersonal(int $customer): bool
    {
        $response = null;
        $params = [
            'verb' => 'checkcustomer',
            'customer' => $customer,
        ];

        try {
            $response = $this->requester->request($params);

            if ('ok' === $response->body) {
                return true;
            }

            throw new LogicException(sprintf('Unexpected evc message: %s', $response->body));
        } catch (LogicException $exception) {
            if (1 === preg_match('/fail: this is not a personal customer of you$/', $exception->getMessage())) {
                return false;
            }

            throw new LogicException($exception->getMessage(), $exception->getCode(), $exception);
        }
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
     * @throws LogicException      when EVC API returns a non-expected answer
     * @throws CredentialException when bad credentials was sent
     * @throws NetworkException    when a network or curl error occurred
     */
    public function setCredit(int $customer, int $credit): void
    {
        $params = [
            'verb' => 'setcustomeraccount',
            'customer' => $customer,
            'credits' => $credit,
        ];
        $response = $this->requester->request($params);

        $result = preg_match('/^ok: ([-+]?\\d+)$/', $response->body, $matches);

        $this->checkResult($result, $matches, $response->body);
    }

    /**
     * Check that days are between minimum and maximum.
     *
     * @param int $days the number provided
     *
     * @throws LogicException when days are not in range
     */
    private function checkDays(int $days): void
    {
        if ($days < self::DAYS_MIN || $days > self::DAYS_MAX) {
            throw new LogicException('Evc error: days shall be between 1 and 99');
        }
    }

    /**
     * Check that the json file is valid.
     *
     * @param string $json provided by preg_match function
     *
     * @throws LogicException when json is not valid
     */
    private function checkJson($json): void
    {
        if (!is_array($json)) {
            throw new LogicException(sprintf('Evc error: Json from evc.de is not a valid JSON'));
        }

        if (!isset($json['data']) || !is_array($json['data'])) {
            throw new LogicException(sprintf('Evc error: Json from evc.de does not contain data'));
        }
    }

    /**
     * Check result of preg_match function.
     *
     * @param int    $result  result of preg_match function
     * @param array  $matches matches created by preg_match function
     * @param string $body    body of request
     *
     * @throws LogicException when the result of preg_match is not valid
     */
    private function checkResult(int $result, $matches, string $body): void
    {
        if (1 !== $result || !is_array($matches) || 2 !== count($matches)) {
            throw new LogicException(sprintf('Unexpected message from evc: %s', trim($body)));
        }
    }

    /**
     * Return the JSON from the response body.
     *
     * @param string $body the EVC response body
     *
     * @throws LogicException when data returned by EVC as not formatted as expected
     *
     * @return array An array from json
     */
    private function getJson(string $body): array
    {
        $result = preg_match('/^ok:\sJSON\sfollows\s([\S\s]+)/', $body, $matches);

        $this->checkResult($result, $matches, $body);

        $json = json_decode($matches[1], true);

        $this->checkJson($json);

        return $json;
    }
}
