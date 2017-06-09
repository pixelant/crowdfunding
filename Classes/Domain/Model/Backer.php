<?php
namespace Pixelant\Crowdfunding\Domain\Model;

/***
 *
 * This file is part of the "Crowdfunding" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2017
 *
 ***/

/**
 * Backers
 */
class Backer extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * Email
     *
     * @var string
     * @validate NotEmpty
     */
    protected $email = '';

    /**
     * Name
     *
     * @var string
     * @validate NotEmpty
     */
    protected $name = '';

    /**
     * Transactions
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\Crowdfunding\Domain\Model\Transaction>
     * @cascade remove
     * @lazy
     */
    protected $transactions = null;

    /**
     * __construct
     */
    public function __construct()
    {
        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    /**
     * Initializes all ObjectStorage properties
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->transactions = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Returns the email
     *
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets the email
     *
     * @param string $email
     * @return void
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Returns the name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name
     *
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Adds a Transaction
     *
     * @param \Pixelant\Crowdfunding\Domain\Model\Transaction $transaction
     * @return void
     */
    public function addTransaction(\Pixelant\Crowdfunding\Domain\Model\Transaction $transaction)
    {
        $this->transactions->attach($transaction);
    }

    /**
     * Removes a Transaction
     *
     * @param \Pixelant\Crowdfunding\Domain\Model\Transaction $transactionToRemove The Transaction to be removed
     * @return void
     */
    public function removeTransaction(\Pixelant\Crowdfunding\Domain\Model\Transaction $transactionToRemove)
    {
        $this->transactions->detach($transactionToRemove);
    }

    /**
     * Returns the transactions
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\Crowdfunding\Domain\Model\Transaction> $transactions
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * Sets the transactions
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\Crowdfunding\Domain\Model\Transaction> $transactions
     * @return void
     */
    public function setTransactions(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $transactions)
    {
        $this->transactions = $transactions;
    }
}
