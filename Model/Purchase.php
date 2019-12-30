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

namespace Alexandre\EvcBundle\Model;

use DateTimeImmutable;
use Exception;

/**
 * Class Purchase is defined by the API evc.de.
 */
class Purchase
{
    public const PROPERTIES = [
        'Build' => 'build',
        'Characteristic' => 'characteristic',
        'ComputerName' => 'computer',
        'Ecu Build' => 'ecuBuild',
        'Ecu Manufacturer' => 'ecuManufacturer',
        'ECU_Nr_ECU' => 'ecuNrEcu',
        'ECU_Nr_Prod' => 'ecuNrProd',
        'Filename' => 'filename',
        'IP' => 'ip',
        'Manufacturer' => 'manufacturer',
        'Model' => 'model',
        'Output' => 'output',
        'Project type' => 'project',
        'Series' => 'series',
        'Software' => 'software',
        'SoftwareVersion' => 'softwareVersion',
    ];

    /**
     * @var string
     */
    private $build;

    /**
     * @var string
     */
    private $characteristic;

    /**
     * The customer name.
     *
     * @var string
     */
    private $computer;

    /**
     * The customer id.
     *
     * @var int
     */
    private $customer;

    /**
     * @var DateTimeImmutable|null
     */
    private $date;

    /**
     * @var string
     */
    private $ecuBuild;

    /**
     * @var string
     */
    private $ecuManufacturer;

    /**
     * @var string
     */
    private $ecuNrEcu;

    /**
     * @var string
     */
    private $ecuNrProd;

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
     * @var string
     */
    private $manufacturer;

    /**
     * @var string
     */
    private $model;

    /**
     * @var array
     */
    private $options = [];

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
    private $series;

    /**
     * @var string
     */
    private $software;

    /**
     * @var string
     */
    private $softwareVersion;

    /**
     * Purchase constructor.
     *
     * @param array $data data to construct Purchase
     */
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'Customer':
                    $this->customer = (int) $value;
                    break;
                case $this->hasProperty($key):
                    $column = $this->getColumn($key);
                    $this->{$column} = $value;
                    break;
                case 'Date':
                    try {
                        $this->date = new DateTimeImmutable($value);
                    } catch (Exception $e) {
                        $this->date = null;
                    }
                    break;
                default:
                    $this->options[$key] = $value;
            }
        }
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
     * Characteristic getter.
     *
     * @return string
     */
    public function getCharacteristic(): ?string
    {
        return $this->characteristic;
    }

    /**
     * Computer name getter.
     */
    public function getComputer(): ?string
    {
        return $this->computer;
    }

    /**
     * Customer name getter.
     */
    public function getCustomer(): ?int
    {
        return $this->customer;
    }

    /**
     * Date getter.
     */
    public function getDate(): ?DateTimeImmutable
    {
        return $this->date;
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
     * Ecu manufacturer getter.
     *
     * @return string
     */
    public function getEcuManufacturer(): ?string
    {
        return $this->ecuManufacturer;
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
     * Manufacturer getter.
     *
     * @return string
     */
    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
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
     * Series getter.
     *
     * @return string
     */
    public function getSeries(): ?string
    {
        return $this->series;
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

    /**
     * Get the property corresponding to the associated field.
     *
     * @param string $field the associated field
     */
    private function getColumn(string $field)
    {
        return self::PROPERTIES[$field];
    }

    /**
     * Is there a property matching the associated field?
     *
     * @param string $field the associated field
     */
    private function hasProperty(string $field)
    {
        return array_key_exists($field, self::PROPERTIES);
    }
}
