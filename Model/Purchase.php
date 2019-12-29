<?php

namespace Alexandre\Evc\Model;

use DateTimeInterface;

class Purchase
{
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
     * The customer name.
     *
     * @var string
     */
    private $computer;

    /**
     * The Ipv4 of subscriber.
     *
     * @var string
     */
    private $ip;

    /**
     * The datetime of subscription.
     *
     * @var DateTimeInterface
     */
    private $createdAt;

    /**
     * @return string
     */
    public function getCustomer(): string
    {
        return $this->customer;
    }

    /**
     * @param string $customer
     *
     * @return Purchase
     */
    public function setCustomer(string $customer): Purchase
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     *
     * @return Purchase
     */
    public function setFilename(string $filename): Purchase
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return string
     */
    public function getComputer(): string
    {
        return $this->computer;
    }

    /**
     * @param string $computer
     *
     * @return Purchase
     */
    public function setComputer(string $computer): Purchase
    {
        $this->computer = $computer;
        return $this;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     *
     * @return Purchase
     */
    public function setIp(string $ip): Purchase
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeInterface $createdAt
     *
     * @return Purchase
     */
    public function setCreatedAt(DateTimeInterface $createdAt): Purchase
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}