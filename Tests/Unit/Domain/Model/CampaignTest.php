<?php
namespace Pixelant\Crowdfunding\Tests\Unit\Domain\Model;

/**
 * Test case.
 */
class CampaignTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Pixelant\Crowdfunding\Domain\Model\Campaign
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Pixelant\Crowdfunding\Domain\Model\Campaign();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getTitleReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getTitle()
        );
    }

    /**
     * @test
     */
    public function setTitleForStringSetsTitle()
    {
        $this->subject->setTitle('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'title',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getDescriptionReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getDescription()
        );
    }

    /**
     * @test
     */
    public function setDescriptionForStringSetsDescription()
    {
        $this->subject->setDescription('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'description',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getPledgedReturnsInitialValueForFloat()
    {
        self::assertSame(
            0.0,
            $this->subject->getPledged()
        );
    }

    /**
     * @test
     */
    public function setPledgedForFloatSetsPledged()
    {
        $this->subject->setPledged(3.14159265);

        self::assertAttributeEquals(
            3.14159265,
            'pledged',
            $this->subject,
            '',
            0.000000001
        );
    }

    /**
     * @test
     */
    public function getStagesReturnsInitialValueForStage()
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getStages()
        );
    }

    /**
     * @test
     */
    public function setStagesForObjectStorageContainingStageSetsStages()
    {
        $stage = new \Pixelant\Crowdfunding\Domain\Model\Stage();
        $objectStorageHoldingExactlyOneStages = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneStages->attach($stage);
        $this->subject->setStages($objectStorageHoldingExactlyOneStages);

        self::assertAttributeEquals(
            $objectStorageHoldingExactlyOneStages,
            'stages',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function addStageToObjectStorageHoldingStages()
    {
        $stage = new \Pixelant\Crowdfunding\Domain\Model\Stage();
        $stagesObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $stagesObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($stage));
        $this->inject($this->subject, 'stages', $stagesObjectStorageMock);

        $this->subject->addStage($stage);
    }

    /**
     * @test
     */
    public function removeStageFromObjectStorageHoldingStages()
    {
        $stage = new \Pixelant\Crowdfunding\Domain\Model\Stage();
        $stagesObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $stagesObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($stage));
        $this->inject($this->subject, 'stages', $stagesObjectStorageMock);

        $this->subject->removeStage($stage);
    }

    /**
     * @test
     */
    public function getGoalsReturnsInitialValueForGoal()
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getGoals()
        );
    }

    /**
     * @test
     */
    public function setGoalsForObjectStorageContainingGoalSetsGoals()
    {
        $goal = new \Pixelant\Crowdfunding\Domain\Model\Goal();
        $objectStorageHoldingExactlyOneGoals = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneGoals->attach($goal);
        $this->subject->setGoals($objectStorageHoldingExactlyOneGoals);

        self::assertAttributeEquals(
            $objectStorageHoldingExactlyOneGoals,
            'goals',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function addGoalToObjectStorageHoldingGoals()
    {
        $goal = new \Pixelant\Crowdfunding\Domain\Model\Goal();
        $goalsObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $goalsObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($goal));
        $this->inject($this->subject, 'goals', $goalsObjectStorageMock);

        $this->subject->addGoal($goal);
    }

    /**
     * @test
     */
    public function removeGoalFromObjectStorageHoldingGoals()
    {
        $goal = new \Pixelant\Crowdfunding\Domain\Model\Goal();
        $goalsObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $goalsObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($goal));
        $this->inject($this->subject, 'goals', $goalsObjectStorageMock);

        $this->subject->removeGoal($goal);
    }
}
