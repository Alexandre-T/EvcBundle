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

namespace Alexandre\Evc\Service;

use Alexandre\Evc\Model\Purchase;

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
     * @param string $customer the customer id
     * @param int    $credit   the positive or negative number of credits to add (or remove)
     */
    public function addCredit(string $customer, int $credit);

    /**
     * Check a personal account balance.
     *
     * @param string $customer the customer id
     */
    public function checkAccount(string $customer): int;

    /**
     * Create a personal customer.
     *
     * A personal customer must already as EVC customer. Making him a personal customer allows him to see your reseller
     * files and creates a personal account balance relationship with you. Only add customers who asked for this.
     *
     * @param string $customer the customer id
     */
    public function createPersonalCustomer(string $customer);

    /**
     * Does this customer exists.
     *
     * Note: This doesn't check if this is a personal customer of you. It checks the EVC customer base.
     *
     * @param string $customer the customer id
     */
    public function exists(string $customer): bool;

    /**
     * Returns a collection of the purchases performed in the last X (up to 99) days.
     *
     * @param string $customer the customer id
     * @param int    $days     the number of
     *
     * @return Purchase[]
     */
    public function getPurchases(string $customer, int $days): array;

    /**
     * Setting a personal account balance.
     *
     * This is not recommended for adding/removing points because the customer might purchase at the same time,
     * causing a wrong balance.
     *
     * @param string $customer the customer id
     * @param int    $credit   the new account balance
     */
    public function setCredit(string $customer, int $credit);
}