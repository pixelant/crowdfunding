<?php
namespace Pixelant\Crowdfunding\Tests\Unit\Domain\Model;

/**
 * Test case.
 */
class TransactionTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Pixelant\Crowdfunding\Domain\Model\Transaction
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Pixelant\Crowdfunding\Domain\Model\Transaction();
    }

    protected function tearDown()
    {
        parent::tearDown();
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
    public function getPledgingIdReturnsInitialValueForInt()
    {
    }

    /**
     * @test
     */
    public function setPledgingIdForIntSetsPledgingId()
    {
    }

    /**
     * @test
     */
    public function getStateReturnsInitialValueForInt()
    {
    }

    /**
     * @test
     */
    public function setStateForIntSetsState()
    {
    }

    /**
     * @test
     */
    public function getStatusReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getStatus()
        );
    }

    /**
     * @test
     */
    public function setStatusForStringSetsStatus()
    {
        $this->subject->setStatus('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'status',
            $this->subject
        );
    }
}
