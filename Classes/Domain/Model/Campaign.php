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
 * Project
 */
class Campaign extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
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
     * Pledged
     *
     * @var float
     * @validate NotEmpty
     */
    protected $pledged = 0.0;

    /**
     * MinAmount
     *
     * @var float
     * @validate NotEmpty
     */
    protected $minAmount = 0.0;

    /**
     * Pledges
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\Crowdfunding\Domain\Model\Pledging>
     * @cascade remove
     */
    protected $pledges = null;

    /**
     * Goals
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\Crowdfunding\Domain\Model\Goal>
     * @cascade remove
     */
    protected $goals = null;

    /**
     * Backers
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\Crowdfunding\Domain\Model\Backer>
     * @lazy
     */
    protected $backers = null;

    /**
     * totalBackedAmount
     *
     * @var float
     */
    protected $totalBackedAmount = null;

    /**
     * crdate
     *
     * @var int
     */
    protected $crdate;

    /**
     * numberOfValidTransactions
     *
     * @var int
     */
    protected $numberOfValidTransactions = null;

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
     * Returns the pledged
     *
     * @return float $pledged
     */
    public function getPledged()
    {
        return $this->pledged;
    }

    /**
     * Sets the pledged
     *
     * @param float $pledged
     * @return void
     */
    public function setPledged($pledged)
    {
        $this->pledged = $pledged;
    }

    /**
     * Returns the minAmount
     *
     * @return float $minAmount
     */
    public function getMinAmount()
    {
        return $this->minAmount;
    }

    /**
     * Sets the minAmount
     *
     * @param float $minAmount
     * @return void
     */
    public function setMinAmount($minAmount)
    {
        $this->minAmount = $minAmount;
    }

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
        $this->pledges = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->goals = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->backers = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Adds a Goal
     *
     * @param \Pixelant\Crowdfunding\Domain\Model\Goal $goal
     * @return void
     */
    public function addGoal(\Pixelant\Crowdfunding\Domain\Model\Goal $goal)
    {
        $this->goals->attach($goal);
    }

    /**
     * Removes a Goal
     *
     * @param \Pixelant\Crowdfunding\Domain\Model\Goal $goalToRemove The Goal to be removed
     * @return void
     */
    public function removeGoal(\Pixelant\Crowdfunding\Domain\Model\Goal $goalToRemove)
    {
        $this->goals->detach($goalToRemove);
    }

    /**
     * Returns the goals
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\Crowdfunding\Domain\Model\Goal> $goals
     */
    public function getGoals()
    {
        return $this->goals;
    }

    /**
     * Sets the goals
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\Crowdfunding\Domain\Model\Goal> $goals
     * @return void
     */
    public function setGoals(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $goals)
    {
        $this->goals = $goals;
    }

    /**
     * Adds a Backer
     *
     * @param \Pixelant\Crowdfunding\Domain\Model\Backer $backer
     * @return void
     */
    public function addBacker(\Pixelant\Crowdfunding\Domain\Model\Backer $backer)
    {
        $this->backers->attach($backer);
    }

    /**
     * Removes a Backer
     *
     * @param \Pixelant\Crowdfunding\Domain\Model\Backer $backerToRemove The Backer to be removed
     * @return void
     */
    public function removeBacker(\Pixelant\Crowdfunding\Domain\Model\Backer $backerToRemove)
    {
        $this->backers->detach($backerToRemove);
    }

    /**
     * Returns the backers
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\Crowdfunding\Domain\Model\Backer> $backers
     */
    public function getBackers()
    {
        return $this->backers;
    }

    /**
     * Sets the backers
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\Crowdfunding\Domain\Model\Backer> $backers
     * @return void
     */
    public function setBackers(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $backers)
    {
        $this->backers = $backers;
    }

    /**
     * Adds a Stage
     *
     * @param \Pixelant\Crowdfunding\Domain\Model\Pledging $pledge
     * @return void
     */
    public function addPledge(\Pixelant\Crowdfunding\Domain\Model\Pledging $pledge)
    {
        $this->pledges->attach($pledge);
    }

    /**
     * Removes a Stage
     *
     * @param \Pixelant\Crowdfunding\Domain\Model\Pledging $pledgeToRemove The Pledging to be removed
     * @return void
     */
    public function removePledge(\Pixelant\Crowdfunding\Domain\Model\Pledging $pledgeToRemove)
    {
        $this->pledges->detach($pledgeToRemove);
    }

    /**
     * Returns the pledges
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\Crowdfunding\Domain\Model\Pledging> pledges
     */
    public function getPledges()
    {
        return $this->pledges;
    }

    /**
     * Sets the pledges
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\Crowdfunding\Domain\Model\Pledging> $pledges
     * @return void
     */
    public function setPledges(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $pledges)
    {
        $this->pledges = $pledges;
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
                    $queryBuilder->expr()->eq('campaign_id', $queryBuilder->createNamedParameter($this->uid, \PDO::PARAM_INT)),
                    $queryBuilder->expr()->eq('state', $queryBuilder->createNamedParameter(1, \PDO::PARAM_INT))
                )
                ->groupBy('campaign_id')
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
     * Returns the pledged as "currency"
     *
     * @return string
     */
    public function getPledgedAsString()
    {
        return CrowdfundingUtility::formatCurrency(
            $this->pledged
        );
    }
    
    /**
     * Returns the total amount backed for the campaing as "currency"
     *
     * @return string
     */
    public function getTotalBackedAmountPercent()
    {
        $percent = 0;
        $totalBackedAmount = $this->getTotalBackedAmount();

        if ($totalBackedAmount > 0 && $this->pledged > 0) {
            if ((int)$totalBackedAmount < (int)$this->pledged) {
                $percent = ($totalBackedAmount / $this->pledged) * 100;
            } else {
                $percent = 100;
            }
        }

        return number_format($percent, 0);
    }

    /**
     * Returns the crdate
     *
     * @return int $crdate
     */
    public function getCrdate()
    {
        return $this->crdate;
    }

    /**
     * Returns the minAmount as "currency"
     *
     * @return string
     */
    public function getMinAmountAsString()
    {
        return CrowdfundingUtility::formatCurrency(
            $this->minAmount
        );
    }

    /**
     * Returns the total count of valid transactions for campaign
     *
     * @return float
     */
    public function getNumberOfValidTransactions()
    {
        if ($this->numberOfValidTransactions === null) {
            $this->numberOfValidTransactions = 0;

            $queryBuilder =
                \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                    \TYPO3\CMS\Core\Database\ConnectionPool::class
                )
                ->getQueryBuilderForTable('tx_crowdfunding_domain_model_transaction');

            $result = $queryBuilder
                ->count('uid')
                ->from('tx_crowdfunding_domain_model_transaction')
                ->where(
                    $queryBuilder->expr()->eq('campaign_id', $queryBuilder->createNamedParameter($this->uid, \PDO::PARAM_INT)),
                    $queryBuilder->expr()->eq('state', $queryBuilder->createNamedParameter(1, \PDO::PARAM_INT))
                )
                ->execute()
                ->fetchColumn(0);

            if (!empty($result)) {
                $this->numberOfValidTransactions = $result;
            }
        }
        return $this->numberOfValidTransactions;
    }
}
