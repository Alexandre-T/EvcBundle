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

namespace Alexandre\EvcBundle\Model;

/**
 * Class Customer is defined by the API evc.de.
 */
class Customer
{
    public const PROPERTIES = [
        'Customer' => 'identifier',
        'Credits' => 'credit',
    ];

    /**
     * Personal account balance of customer for the current reseller.
     *
     * @var int
     */
    private $credit = 0;

    /**
     * Internal EVC identifier of customer.
     *
     * @var int
     */
    private $identifier;

    /**
     * If EVC API is updated and forward new data, they will be put in this "options" array.
     *
     * @var array
     */
    private $options = [];

    /**
     * Customer constructor.
     *
     * @param array $data data to construct Customer
     */
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'Customer':
                    $this->identifier = (int) $value;
                    break;
                case 'Credits':
                    $this->credit = (int) $value;
                    break;
                default:
                    $this->options[$key] = $value;
            }
        }
    }

    /**
     * Credit getter.
     */
    public function getCredit(): int
    {
        return $this->credit;
    }

    /**
     * Identifier getter.
     *
     * @return int
     */
    public function getIdentifier(): ?int
    {
        return $this->identifier;
    }

    /**
     * Return new columns provided by API.
     *
     * @param string|null $key key of the options to get value. let null to retrieve full array
     *
     * @return bool|string|int|float|array
     */
    public function getOptions(string $key = null)
    {
        if (null === $key) {
            return $this->options;
        }

        if (array_key_exists($key, $this->options)) {
            return $this->options[$key];
        }

        return false;
    }
}
