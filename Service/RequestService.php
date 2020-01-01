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
use Unirest\Request;
use Unirest\Response;

/**
 * Request service Constructs request to evc.de and analyze the result.
 */
class RequestService implements RequestServiceInterface
{
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
     * @return Response
     */
    public function get(array $params)
    {
        $headers = $this->getHeaders();
        $params += $this->getParams();

        return Request::get($this->getUrl(), $headers, $params);
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
     * Return the result of Request.
     *
     * @param array $params each params is a set of name and value
     * @param bool  $throws throws an error as soon as the Response body began with fail:
     *
     * @throws EvcException when response code is different from 200
     */
    public function request(array $params, bool $throws = true): Response
    {
        $response = $this->get($params);

        if (200 !== $response->code) {
            throw new EvcException(sprintf('Evc return a response with code %d', $response->code));
        }

        if ($throws && $this->isFailing($response->body)) {
            throw new EvcException(sprintf('Evc error: %s', $response->body));
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
