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
use Unirest\Response;

/**
 * Emulation service does NOT request evc.de and could be use to test.
 */
class EmulationService implements RequestServiceInterface
{
    /**
     * Error messages returned by API.
     */
    public const CREDENTIAL_ERRORS = [
        'fail: no api authorization',
        'fail: no user authorization',
        'fail: unknown verb',
    ];

    /**
     * The api id.
     *
     * @var string
     */
    private $api;

    /**
     * The password to access API.
     *
     * @var string
     */
    private $password;

    /**
     * The reseller number.
     *
     * @var string
     */
    private $username;

    /**
     * Request service constructor.
     *
     * @param string $api      api version provided by evc.de
     * @param string $username your username
     * @param string $password your api password
     */
    public function __construct(string $api, string $username, string $password)
    {
        $this->api = urlencode($api);
        $this->username = urlencode($username);
        $this->password = urlencode($password);
    }

    /**
     * Launch the request with a get.
     *
     * @param array $params the query parameters
     *
     * @return Response
     */
    public function get(array $params): Response
    {
        $params += $this->getParams();

        if (!key_exists('verb', $params)) {
            return $this->response('fail: no verb');
        }

        $customer = 0;
        if (key_exists('customer', $params)) {
            $customer = (int)$params['customer'];
        }

        $credit = 0;
        if (key_exists('credits', $params)) {
            $credit = (int)$params['credits'];
        }

        switch ($params['verb']) {
            case 'addcustomer':
                return $this->response($this->addCustomer($customer));
            case 'addcustomeraccount':
                return $this->response($this->addCustomerAccount($customer, $credit));
            case 'checkcustomer':
                return $this->response($this->checkCustomer($customer));
            case 'checkevccustomer':
                return $this->response($this->checkEvcCustomer($customer));
            case 'getcustomeraccount':
                return $this->response($this->getCustomerAccount($customer));
            case 'getrecentpurchases':
                return $this->response($this->getRecentPurchases());
            case 'listcustomers':
                return $this->response($this->listCustomers());
            case 'setcustomeraccount':
                return $this->response($this->setCustomerAccount($customer, $credit));
            default:
                return $this->response('fail: unknown verb');
        }
    }

    /**
     * Is the message corresponding to a credential error.
     *
     * @param string $message the body to analyze
     */
    public function isCredentialError(string $message): bool
    {
        return in_array($message, self::CREDENTIAL_ERRORS);
    }

    /**
     * Does this text is a fail?
     *
     * @param string $body the text to analyze
     *
     * @return bool True when the text begins with "fail:"
     */
    public function isFailing(string $body): bool
    {
        return 1 === preg_match('|^fail:\s|', $body);
    }

    /**
     * Request the evc.de service and return a Unirest response.
     *
     * @param array $params the params to complete request
     *
     * @throws NetworkException    when an error occurred while requesting evc.de service
     * @throws CredentialException when credentials are not valid
     * @throws LogicException      when EVC API returns a non-expected answer
     */
    public function request(array $params): Response
    {
        $response = $this->get($params);

        if (200 !== $response->code) {
            //TODO dead code to test
            throw new NetworkException(sprintf('Evc API returns a response with code %d', $response->code));
        }

        if ($this->isCredentialError($response->body)) {
            throw new CredentialException(sprintf('Credential error: %s', $response->body));
        }

        if ($this->isFailing($response->body)) {
            //TODO dead code to test
            throw new LogicException(sprintf('Unexpected evc message: %s', $response->body));
        }

        return $response;
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
     * Create and return an Unirest Response.
     *
     * @param string $body body of the response
     *
     * @return Response
     */
    private function response(string $body): Response
    {
        return new Response(200, $body, '');
    }

    /**
     * Add a customer as personal customer.
     *
     * @param int $customer customer identifier
     */
    private function addCustomer(int $customer): string
    {
        switch ($customer) {
            case '44444':
            case '33333':
                return 'fail: customer already exists';
            case '22222':
                return 'ok';
            case '11111':
            default:
                return 'fail: unknown customer';
        }
    }

    /**
     * Add credit to the personal balance of a personal customer.
     *
     * @param int $customer customer identifier
     * @param int $credits  new credit to add
     */
    private function addCustomerAccount(int $customer, int $credits): string
    {
        switch ($customer) {
            case '44444':
            case '33333':
                return sprintf('ok: %d', 42 + $credits);
            case '22222':
            case '11111':
            default:
                return 'fail: this is not a personal customer of you';
        }
    }

    /**
     * Check that customer is a personal customer.
     *
     * @param int $customer customer identifier
     */
    private function checkCustomer(int $customer): string
    {
        switch ($customer) {
            case '44444':
            case '33333':
                return 'ok';
            case '22222':
            case '11111':
            default:
                return 'fail: this is not a personal customer of you';
        }
    }

    /**
     * Check that customer exists.
     *
     * @param int $customer customer identifier
     */
    private function checkEvcCustomer(int $customer): string
    {
        switch ($customer) {
            case '44444':
            case '33333':
            case '22222':
                return 'ok';
            case '11111':
            default:
                return 'fail: unknown evc customer';
        }
    }

    /**
     * Get the personal balance of a personal customer.
     *
     * @param int $customer customer identifier
     */
    private function getCustomerAccount(int $customer): string
    {
        switch ($customer) {
            case '44444':
            case '33333':
                return 'ok: 42';
            case '22222':
            case '11111':
            default:
                return 'fail: this is not a personal customer of you';
        }
    }

    /**
     * Get the recent purchases of reseller.
     */
    private function getRecentPurchases(): string
    {
         return file_get_contents(__DIR__ . '/../Resources/tests/get-purchase.txt');
    }

    /**
     * Get the list of customer.
     */
    private function listCustomers(): string
    {
         return file_get_contents(__DIR__ . '/../Resources/tests/get-customers.txt');
    }

    /**
     * Set the personal balance of a personal customer.
     *
     * @param int $customer customer identifier
     * @param int $credits  new credit to add
     */
    private function setCustomerAccount(int $customer, int $credits): string
    {
        switch ($customer) {
            case '44444':
            case '33333':
                return sprintf('ok: %d', $credits);
            case '22222':
            case '11111':
            default:
                return 'fail: this is not a personal customer of you';
        }
    }
}
