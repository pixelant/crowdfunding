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
    public function getPledgesReturnsInitialValueForPledging()
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getPledges()
        );
    }

    /**
     * @test
     */
    public function setPledgesForObjectStorageContainingPledgingSetsPledges()
    {
        $pledge = new \Pixelant\Crowdfunding\Domain\Model\Pledging();
        $objectStorageHoldingExactlyOnePledges = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOnePledges->attach($pledge);
        $this->subject->setPledges($objectStorageHoldingExactlyOnePledges);

        self::assertAttributeEquals(
            $objectStorageHoldingExactlyOnePledges,
            'pledges',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function addPledgeToObjectStorageHoldingPledges()
    {
        $pledge = new \Pixelant\Crowdfunding\Domain\Model\Pledging();
        $pledgesObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $pledgesObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($pledge));
        $this->inject($this->subject, 'pledges', $pledgesObjectStorageMock);

        $this->subject->addPledge($pledge);
    }

    /**
     * @test
     */
    public function removePledgeFromObjectStorageHoldingPledges()
    {
        $pledge = new \Pixelant\Crowdfunding\Domain\Model\Pledging();
        $pledgesObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $pledgesObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($pledge));
        $this->inject($this->subject, 'pledges', $pledgesObjectStorageMock);

        $this->subject->removePledge($pledge);
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

    /**
     * @test
     */
    public function getBackersReturnsInitialValueForBacker()
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getBackers()
        );
    }

    /**
     * @test
     */
    public function setBackersForObjectStorageContainingBackerSetsBackers()
    {
        $backer = new \Pixelant\Crowdfunding\Domain\Model\Backer();
        $objectStorageHoldingExactlyOneBackers = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneBackers->attach($backer);
        $this->subject->setBackers($objectStorageHoldingExactlyOneBackers);

        self::assertAttributeEquals(
            $objectStorageHoldingExactlyOneBackers,
            'backers',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function addBackerToObjectStorageHoldingBackers()
    {
        $backer = new \Pixelant\Crowdfunding\Domain\Model\Backer();
        $backersObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $backersObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($backer));
        $this->inject($this->subject, 'backers', $backersObjectStorageMock);

        $this->subject->addBacker($backer);
    }

    /**
     * @test
     */
    public function removeBackerFromObjectStorageHoldingBackers()
    {
        $backer = new \Pixelant\Crowdfunding\Domain\Model\Backer();
        $backersObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $backersObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($backer));
        $this->inject($this->subject, 'backers', $backersObjectStorageMock);

        $this->subject->removeBacker($backer);
    }

    /**
     * @test
     */
    public function getCrdateReturnsInitialValueForInt()
    {
        self::assertSame(
            null,
            $this->subject->getCrdate()
        );
    }

    /**
     * @test
     */
    public function getMinAmountReturnsInitialValueForFloat()
    {
        self::assertSame(
            0.0,
            $this->subject->getMinAmount()
        );
    }

    /**
     * @test
     */
    public function setMinAmountForFloatSetsMinAmount()
    {
        $this->subject->setMinAmount(19.10);

        self::assertAttributeEquals(
            19.10,
            'minAmount',
            $this->subject
        );
    }
}
