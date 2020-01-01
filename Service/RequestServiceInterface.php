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
use Unirest\Response;

/**
 * Interface RequestServiceInterface.
 */
interface RequestServiceInterface
{
    /**
     * Request the evc.de service and return a Unirest response.
     *
     * @param array $params the params to complete request
     *
     * @throws EvcException when an error occurred while requesting evc.de service
     */
    public function request(array $params): Response;
}