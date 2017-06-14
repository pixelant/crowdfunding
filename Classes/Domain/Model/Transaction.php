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
 * Transaction
 */
class Transaction extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * Reference
     *
     * @var string
     * @validate NotEmpty
     */
    protected $reference = '';

    /**
     * Amount
     *
     * @var float
     * @validate NotEmpty
     */
    protected $amount = 0.0;

    /**
     * Campaign
     *
     * @var int
     */
    protected $campaignId = 0;

    /**
     * Pledging
     *
     * @var int
     */
    protected $pledgingId = 0;

    /**
     * State
     *
     * @var int
     * @validate NotEmpty
     */
    protected $state = 0;

    /**
     * Status
     *
     * @var string
     * @validate NotEmpty
     */
    protected $status = '';

    /**
     * Returns the reference
     *
     * @return string $reference
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Sets the reference
     *
     * @param string $reference
     * @return void
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    /**
     * Returns the amount
     *
     * @return float $amount
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Sets the amount
     *
     * @param float $amount
     * @return void
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * Returns the campaignId
     *
     * @return int $campaignId
     */
    public function getCampaignId()
    {
        return $this->campaignId;
    }

    /**
     * Sets the campaignId
     *
     * @param int $campaignId
     * @return void
     */
    public function setCampaignId($campaignId)
    {
        $this->campaignId = $campaignId;
    }

    /**
     * Returns the pledgingId
     *
     * @return int $pledgingId
     */
    public function getPledgingId()
    {
        return $this->pledgingId;
    }

    /**
     * Sets the pledgingId
     *
     * @param int $pledgingId
     * @return void
     */
    public function setPledgingId($pledgingId)
    {
        $this->pledgingId = $pledgingId;
    }

    /**
     * Returns the state
     *
     * @return int $state
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Sets the state
     *
     * @param int $state
     * @return void
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * Returns the status
     *
     * @return string $status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the status
     *
     * @param string $status
     * @return void
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }
}
