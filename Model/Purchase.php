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

namespace Alexandre\Evc\Model;

use DateTimeInterface;

class Purchase
{
    /**
     * The customer name.
     *
     * @var string
     */
    private $computer;

    /**
     * The datetime of subscription.
     *
     * @var DateTimeInterface
     */
    private $createdAt;
    /**
     * The customer id.
     *
     * @var string
     */
    private $customer;

    /**
     * The filename subscribed.
     *
     * @var string
     */
    private $filename;

    /**
     * The Ipv4 of subscriber.
     *
     * @var string
     */
    private $ip;

    public function getComputer(): string
    {
        return $this->computer;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getCustomer(): string
    {
        return $this->customer;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setComputer(string $computer): Purchase
    {
        $this->computer = $computer;

        return $this;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): Purchase
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function setCustomer(string $customer): Purchase
    {
        $this->customer = $customer;

        return $this;
    }

    public function setFilename(string $filename): Purchase
    {
        $this->filename = $filename;

        return $this;
    }

    public function setIp(string $ip): Purchase
    {
        $this->ip = $ip;

        return $this;
    }
}
