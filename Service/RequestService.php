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
use Unirest\Exception;
use Unirest\Request;
use Unirest\Response;

/**
 * Request service Constructs request to evc.de and analyze the result.
 */
class RequestService implements RequestServiceInterface
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
     * The URL of EVC service.
     *
     * @var string
     */
    private $url;

    /**
     * The reseller number.
     *
     * @var string
     */
    private $username;

    /**
     * Request service constructor.
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
     * Launch the request with a get.
     *
     * @param array $params the query parameters
     *
     * @throws Exception when an error occurred with curl
     *
     * @return Response
     */
    public function get(array $params)
    {
        $headers = $this->getHeaders();
        $params += $this->getParams();

        return Request::get($this->getUrl(), $headers, $params);
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
        try {
            $response = $this->get($params);
        } catch (Exception $curlException) {
            throw new NetworkException($curlException->getMessage(), $curlException->getCode(), $curlException);
        }

        if (200 !== $response->code) {
            throw new NetworkException(sprintf('Evc API returns a response with code %d', $response->code));
        }

        if ($this->isCredentialError($response->body)) {
            throw new CredentialException(sprintf('Credential error: %s', $response->body));
        }

        if ($this->isFailing($response->body)) {
            throw new LogicException(sprintf('Unexpected evc message: %s', $response->body));
        }

        return $response;
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
     * URL getter.
     */
    private function getUrl(): string
    {
        return $this->url;
    }
}
