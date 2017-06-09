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
     * Stages
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\Crowdfunding\Domain\Model\Pledging>
     * @cascade remove
     */
    protected $stages = null;

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
        $this->stages = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
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
     * Adds a Stage
     *
     * @param \Pixelant\Crowdfunding\Domain\Model\Pledging $stage
     * @return void
     */
    public function addStage(\Pixelant\Crowdfunding\Domain\Model\Pledging $stage)
    {
        $this->stages->attach($stage);
    }

    /**
     * Removes a Stage
     *
     * @param \Pixelant\Crowdfunding\Domain\Model\Pledging $stageToRemove The Pledging to be removed
     * @return void
     */
    public function removeStage(\Pixelant\Crowdfunding\Domain\Model\Pledging $stageToRemove)
    {
        $this->stages->detach($stageToRemove);
    }

    /**
     * Returns the stages
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\Crowdfunding\Domain\Model\Pledging> stages
     */
    public function getStages()
    {
        return $this->stages;
    }

    /**
     * Sets the stages
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\Crowdfunding\Domain\Model\Pledging> $stages
     * @return void
     */
    public function setStages(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $stages)
    {
        $this->stages = $stages;
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
}
