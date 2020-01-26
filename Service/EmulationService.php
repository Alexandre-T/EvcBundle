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
        'fail: no verb',
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
     * @throws NetworkException    when customer identifier is 55555
     * @throws CredentialException when customer identifier is 66666
     * @throws LogicException      when customer identifier is 77777
     */
    public function get(array $params): Response
    {
        $params += $this->getParams();

        if (!array_key_exists('verb', $params)) {
            return $this->response('fail: no verb');
        }

        $customer = $this->initCustomer($params);
        $credit = $this->initCredit($params);

        return $this->response($this->analyzeVerb($params['verb'], $customer, $credit));
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

        if ($this->isCredentialError($response->body)) {
            throw new CredentialException(sprintf('Credential error: %s', $response->body));
        }

        return $response;
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
     * Analyze verb and returns the response body.
     *
     * @param string $verb     the action name
     * @param int    $customer the customer identifier
     * @param int    $credit   the credit
     *
     * @return Response
     */
    private function analyzeVerb(string $verb, int $customer, int $credit): string
    {
        switch ($verb) {
            case 'addcustomer':
                return $this->addCustomer($customer);
            case 'addcustomeraccount':
                return $this->addCustomerAccount($customer, $credit);
            case 'checkcustomer':
                return $this->checkCustomer($customer);
            case 'checkevccustomer':
                return $this->checkEvcCustomer($customer);
            case 'getcustomeraccount':
                return $this->getCustomerAccount($customer);
            case 'getrecentpurchases':
                return $this->getRecentPurchases();
            case 'listcustomers':
                return $this->listCustomers();
            case 'setcustomeraccount':
                return $this->setCustomerAccount($customer, $credit);
            default:
                return 'fail: unknown verb';
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
                return 'ok: evc customer exists';
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
     * Get the recent purchases of reseller.
     */
    private function getRecentPurchases(): string
    {
        return file_get_contents(__DIR__.'/../Resources/tests/get-purchase.txt');
    }

    /**
     * Initialize credits.
     *
     * @param array $params the parameters
     */
    private function initCredit(array $params)
    {
        if (array_key_exists('credits', $params)) {
            return (int) $params['credits'];
        }

        return 0;
    }

    /**
     * Initialize customer identifier.
     *
     * @param array $params the parameters
     *
     * @throws NetworkException    when customer identifier is 55555
     * @throws CredentialException when customer identifier is 66666
     * @throws LogicException      when customer identifier is 77777
     */
    private function initCustomer(array $params)
    {
        if (!array_key_exists('customer', $params)) {
            return 0;
        }

        $customer = (int) $params['customer'];

        switch ($customer) {
            case 55555:
                throw new NetworkException('Network exception: you called the 55555 customer.');
            case 66666:
                throw new CredentialException('Credential exception: you called the 66666 customer.');
            case 77777:
                throw new LogicException('Logic exception: you called the 77777 customer.');
        }

        return $customer;
    }

    /**
     * Get the list of customer.
     */
    private function listCustomers(): string
    {
        return file_get_contents(__DIR__.'/../Resources/tests/get-customers.txt');
    }

    /**
     * Create and return an Unirest Response.
     *
     * @param string $body body of the response
     */
    private function response(string $body): Response
    {
        return new Response(200, $body, '');
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
