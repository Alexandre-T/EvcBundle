<?php

namespace Alexandre\Evc\Service;

use Alexandre\Evc\Model\Purchase;

interface EvcServiceInterface
{
    /**
     * Does this customer exists.
     *
     * Note: This doesn't check if this is a personal customer of you. It checks the EVC customer base.
     *
     * @param string $customer the customer id
     *
     * @return bool
     */
    public function exists(string $customer): bool;

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
     * Check a personal account balance.
     *
     * @param string $customer the customer id
     *
     * @return int
     */
    public function checkAccount(string $customer): int;

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

    /**
     * Add some credits to customer balance.
     *
     * This is the recommended way for adding/removing points. If the customer purchases at the *same* time,
     * the balance will still be correct.
     *
     * The "credits" value can be negative, so you can subtract with this command as well.
     *
     * @param string $customer the customer id
     * @param int    $credit   the positive or negative number of credits to add (or remove).
     */
    public function addCredit(string $customer, int $credit);

    /**
     * Returns a collection of the purchases performed in the last X (up to 99) days.
     *
     * @param string $customer the customer id
     * @param int    $days     the number of
     *
     * @return Purchase[]
     */
    public function getPurchases(string $customer, int $days): array;
}