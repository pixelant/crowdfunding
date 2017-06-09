<?php
namespace Pixelant\Crowdfunding\Tests\Unit\Domain\Model;

/**
 * Test case.
 */
class BackerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Pixelant\Crowdfunding\Domain\Model\Backer
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Pixelant\Crowdfunding\Domain\Model\Backer();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getEmailReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getEmail()
        );
    }

    /**
     * @test
     */
    public function setEmailForStringSetsEmail()
    {
        $this->subject->setEmail('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'email',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getNameReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getName()
        );
    }

    /**
     * @test
     */
    public function setNameForStringSetsName()
    {
        $this->subject->setName('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'name',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getTransactionsReturnsInitialValueForTransaction()
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getTransactions()
        );
    }

    /**
     * @test
     */
    public function setTransactionsForObjectStorageContainingTransactionSetsTransactions()
    {
        $transaction = new \Pixelant\Crowdfunding\Domain\Model\Transaction();
        $objectStorageHoldingExactlyOneTransactions = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneTransactions->attach($transaction);
        $this->subject->setTransactions($objectStorageHoldingExactlyOneTransactions);

        self::assertAttributeEquals(
            $objectStorageHoldingExactlyOneTransactions,
            'transactions',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function addTransactionToObjectStorageHoldingTransactions()
    {
        $transaction = new \Pixelant\Crowdfunding\Domain\Model\Transaction();
        $transactionsObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $transactionsObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($transaction));
        $this->inject($this->subject, 'transactions', $transactionsObjectStorageMock);

        $this->subject->addTransaction($transaction);
    }

    /**
     * @test
     */
    public function removeTransactionFromObjectStorageHoldingTransactions()
    {
        $transaction = new \Pixelant\Crowdfunding\Domain\Model\Transaction();
        $transactionsObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $transactionsObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($transaction));
        $this->inject($this->subject, 'transactions', $transactionsObjectStorageMock);

        $this->subject->removeTransaction($transaction);
    }
}
