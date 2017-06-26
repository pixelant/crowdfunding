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

use Pixelant\Crowdfunding\Utility\CrowdfundingUtility;

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
     * totalBackedAmount
     *
     * @var float
     */
    protected $totalBackedAmount = null;

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

    /**
     * Returns the total amount backed for the campaing
     *
     * @return float
     */
    public function getTotalBackedAmount()
    {
        if ($this->totalBackedAmount === null) {
            $this->totalBackedAmount = 0;

            $queryBuilder =
                \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                    \TYPO3\CMS\Core\Database\ConnectionPool::class
                )
                ->getQueryBuilderForTable('tx_crowdfunding_domain_model_transaction');

            $result = $queryBuilder
                ->addSelectLiteral(
                    $queryBuilder->expr()->sum('amount', 'totalAmount')
                )
                ->from('tx_crowdfunding_domain_model_transaction')
                ->where(
                    $queryBuilder->expr()->eq('pledging_id', $queryBuilder->createNamedParameter($this->uid, \PDO::PARAM_INT)),
                    $queryBuilder->expr()->eq('state', $queryBuilder->createNamedParameter(1, \PDO::PARAM_INT))
                )
                ->groupBy('pledging_id')
                ->execute()
                ->fetch();

            if (!empty($result)) {
                $this->totalBackedAmount = $result['totalAmount'];
            }
        }
        return $this->totalBackedAmount;
    }

    /**
     * Returns the total amount backed for the campaing as "currency"
     *
     * @return string
     */
    public function getTotalBackedAmountAsString()
    {
        return CrowdfundingUtility::formatCurrency(
            $this->getTotalBackedAmount()
        );
    }

    /**
     * Returns the amount as "currency"
     *
     * @return string
     */
    public function getAmountAsString()
    {
        return CrowdfundingUtility::formatCurrency(
            $this->amount
        );
    }
}
