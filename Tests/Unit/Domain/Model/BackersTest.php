<?php
namespace Pixelant\Crowdfunding\Tests\Unit\Domain\Model;

/**
 * Test case.
 */
class BackersTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Pixelant\Crowdfunding\Domain\Model\Backers
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Pixelant\Crowdfunding\Domain\Model\Backers();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getCampaignIdReturnsInitialValueForInt()
    {
    }

    /**
     * @test
     */
    public function setCampaignIdForIntSetsCampaignId()
    {
    }

    /**
     * @test
     */
    public function getStageIdReturnsInitialValueForInt()
    {
    }

    /**
     * @test
     */
    public function setStageIdForIntSetsStageId()
    {
    }

    /**
     * @test
     */
    public function getFrontendUserIdReturnsInitialValueForInt()
    {
    }

    /**
     * @test
     */
    public function setFrontendUserIdForIntSetsFrontendUserId()
    {
    }

    /**
     * @test
     */
    public function getReferenceReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getReference()
        );
    }

    /**
     * @test
     */
    public function setReferenceForStringSetsReference()
    {
        $this->subject->setReference('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'reference',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getAmountReturnsInitialValueForFloat()
    {
        self::assertSame(
            0.0,
            $this->subject->getAmount()
        );
    }

    /**
     * @test
     */
    public function setAmountForFloatSetsAmount()
    {
        $this->subject->setAmount(3.14159265);

        self::assertAttributeEquals(
            3.14159265,
            'amount',
            $this->subject,
            '',
            0.000000001
        );
    }

    /**
     * @test
     */
    public function getStatusReturnsInitialValueForInt()
    {
    }

    /**
     * @test
     */
    public function setStatusForIntSetsStatus()
    {
    }
}
