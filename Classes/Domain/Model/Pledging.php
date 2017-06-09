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
 * Stage
 */
class Pledging extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * Title
     *
     * @var string
     * @validate NotEmpty
     */
    protected $title = '';

    /**
     * Description
     *
     * @var string
     * @validate NotEmpty
     */
    protected $description = '';

    /**
     * Amount
     *
     * @var float
     * @validate NotEmpty
     */
    protected $amount = 0.0;

    /**
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns the description
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the description
     *
     * @param string $description
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
}
