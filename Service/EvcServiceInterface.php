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
use Alexandre\EvcBundle\Model\Customer;
use Alexandre\EvcBundle\Model\Purchase;

/**
 * Interface Evc Service defines methods to request an api as evc.de.
 *
 * All methods can throw logic, credential or network exceptions.
 * All of these three exceptions inherits from EvcException.
 */
interface EvcServiceInterface
{
    /**
     * Add some credits to customer balance.
     *
     * This is the recommended way for adding/removing points. If the customer purchases at the *same* time,
     * the balance will still be correct.
     *
     * The "credits" value can be negative, so you can subtract with this command as well.
     *
     * @param int $customer the customer id
     * @param int $credit   the positive or negative number of credits to add (or remove)
     *
     * @throws LogicException      when a non-expected message is returned by API
     * @throws CredentialException when credentials are not valid
     * @throws NetworkException    when an error occurred while accessing EVC servers
     */
    public function addCredit(int $customer, int $credit);

    /**
     * Check a personal account balance.
     *
     * @param int $customer the customer id
     *
     * @throws LogicException      when a non-expected message is returned by API
     * @throws CredentialException when credentials are not valid
     * @throws NetworkException    when an error occurred while accessing EVC servers
     */
    public function checkAccount(int $customer): int;

    /**
     * Create a personal customer.
     *
     * A personal customer must already as EVC customer. Making him a personal customer allows him to see your reseller
     * files and creates a personal account balance relationship with you. Only add customers who asked for this.
     *
     * @param int $customer the customer id
     *
     * @throws LogicException      when a non-expected message is returned by API
     * @throws CredentialException when credentials are not valid
     * @throws NetworkException    when an error occurred while accessing EVC servers
     */
    public function createPersonalCustomer(int $customer);

    /**
     * Does this customer exists.
     *
     * Note: This doesn't check if this is a personal customer of you. It checks the EVC customer base.
     *
     * @param int $customer the customer id
     *
     * @throws LogicException      when a non-expected message is returned by API
     * @throws CredentialException when credentials are not valid
     * @throws NetworkException    when an error occurred while accessing EVC servers
     */
    public function exists(int $customer): bool;

    /**
     * Get list of personal customers.
     * A personal customer is an EVC customer which have a personal account balance with current reseller.
     *
     * @throws LogicException      when a non-expected message is returned by API
     * @throws CredentialException when credentials are not valid
     * @throws NetworkException    when an error occurred while accessing EVC servers
     *
     * @return Customer[] An array of customers
     */
    public function getPersonalCustomers(): array;

    /**
     * Returns a collection of the purchases performed in the last X (up to 99) days.
     *
     * @param int      $days     the number of
     * @param int|null $customer filter purchases on specified customer id
     *
     * @throws LogicException      when a non-expected message is returned by API
     * @throws CredentialException when credentials are not valid
     * @throws NetworkException    when an error occurred while accessing EVC servers
     *
     * @return Purchase[]
     */
    public function getPurchases(int $days, int $customer = null): array;

    /**
     * Is this customer a personal customer of the current reseller?
     *
     * @param int $customer the customer olsx identifier
     *
     * @throws LogicException      when a non-expected message is returned by API
     * @throws CredentialException when credentials are not valid
     * @throws NetworkException    when an error occurred while accessing EVC servers
     *
     * @return bool false when the customer have no personal account
     */
    public function isPersonal(int $customer): bool;

    /**
     * Setting a personal account balance.
     *
     * This is not recommended for adding/removing points because the customer might purchase at the same time,
     * causing a wrong balance.
     *
     * @param int $customer the customer id
     * @param int $credit   the new account balance
     *
     * @throws LogicException      when a non-expected message is returned by API
     * @throws CredentialException when credentials are not valid
     * @throws NetworkException    when an error occurred while accessing EVC servers
     */
    public function setCredit(int $customer, int $credit);
}
