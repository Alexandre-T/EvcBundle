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

use DateTimeImmutable;
use DateTimeInterface;
use Exception;

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
     * @var int
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

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var null|DateTimeImmutable
     */
    private $date;

    /**
     * @var string
     */
    private $manufacturer;

    /**
     * @var string
     */
    private $series;

    /**
     * @var string
     */
    private $build;

    /**
     * @var string
     */
    private $model;

    /**
     * @var string
     */
    private $characteristic;

    /**
     * @var string
     */
    private $output;

    /**
     * @var string
     */
    private $project;

    /**
     * @var string
     */
    private $ecuManufacturer;

    /**
     * @var string
     */
    private $ecuBuild;

    /**
     * @var string
     */
    private $ecuNrEcu;

    /**
     * @var string
     */
    private $ecuNrProd;

    /**
     * @var string
     */
    private $software;

    /**
     * @var string
     */
    private $softwareVersion;

    public function __construct(array $data = []){
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'Customer':
                    $this->customer = (int) $value;
                    break;
                case 'Filename':
                    $this->filename = $value;
                    break;
                case 'ComputerName':
                    $this->computer = $value;
                    break;
                case 'IP':
                    $this->ip = $value;
                    break;
                case 'Date':
                    try {
                        $this->date = new DateTimeImmutable($value);
                    } catch (Exception $e) {
                        $this->date = null;
                    }
                    break;
                case 'Manufacturer':
                    $this->manufacturer = $value;
                    break;
                case 'Series':
                    $this->series = $value;
                    break;
                case 'Build':
                    $this->build = $value;
                    break;
                case 'Model':
                    $this->model = $value;
                    break;
                case 'Characteristic':
                    $this->characteristic = $value;
                    break;
                case 'Output':
                    $this->output = $value;
                    break;
                case 'Project type':
                    $this->project = $value;
                    break;
                case 'Ecu Manufacturer':
                    $this->ecuManufacturer = $value;
                    break;
                case 'Ecu Build':
                    $this->ecuBuild = $value;
                    break;
                case 'ECU_Nr_ECU':
                    $this->ecuNrEcu = $value;
                    break;
                case 'ECU_Nr_Prod':
                    $this->ecuNrProd = $value;
                    break;
                case 'Software':
                    $this->software = $value;
                    break;
                case 'SoftwareVersion':
                    $this->softwareVersion = $value;
                    break;
                default:
                    $this->options[$key] = $value;
                    break;
            }
        }
    }

    /**
     * Computer name getter.
     */
    public function getComputer(): ?string
    {
        return $this->computer;
    }

    /**
     * Creation datetime getter.
     */
    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Customer name getter.
     */
    public function getCustomer(): ?int
    {
        return $this->customer;
    }

    /**
     * Filename getter.
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * IP getter.
     */
    public function getIp(): ?string
    {
        return $this->ip;
    }

    /**
     * Return new columns provided by API.
     * 
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Date getter.
     * 
     * @return DateTimeImmutable|null
     */
    public function getDate(): ?DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * Manufacturer getter.
     *
     * @return string
     */
    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }

    /**
     * Series getter.
     *
     * @return string
     */
    public function getSeries(): ?string
    {
        return $this->series;
    }

    /**
     * Build getter.
     *
     * @return string
     */
    public function getBuild(): ?string
    {
        return $this->build;
    }

    /**
     * Model getter.
     *
     * @return string
     */
    public function getModel(): ?string
    {
        return $this->model;
    }

    /**
     * Characteristic getter.
     *
     * @return string
     */
    public function getCharacteristic(): ?string
    {
        return $this->characteristic;
    }

    /**
     * Output getter.
     *
     * @return string
     */
    public function getOutput(): ?string
    {
        return $this->output;
    }

    /**
     * Project getter.
     *
     * @return string
     */
    public function getProject(): ?string
    {
        return $this->project;
    }

    /**
     * Ecu manufacturer getter.
     *
     * @return string
     */
    public function getEcuManufacturer(): ?string
    {
        return $this->ecuManufacturer;
    }

    /**
     * Ecu build getter.
     *
     * @return string
     */
    public function getEcuBuild(): ?string
    {
        return $this->ecuBuild;
    }

    /**
     * Ecu Nr Ecu getter.
     *
     * @return string
     */
    public function getEcuNrEcu(): ?string
    {
        return $this->ecuNrEcu;
    }

    /**
     * Ecu Nr Prod getter.
     *
     * @return string
     */
    public function getEcuNrProd(): ?string
    {
        return $this->ecuNrProd;
    }

    /**
     * Software getter.
     *
     * @return string
     */
    public function getSoftware(): ?string
    {
        return $this->software;
    }

    /**
     * Software version getter.
     *
     * @return string
     */
    public function getSoftwareVersion(): ?string
    {
        return $this->softwareVersion;
    }
}
