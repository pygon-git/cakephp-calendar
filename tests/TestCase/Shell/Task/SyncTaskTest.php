<?php
namespace Qobo\Calendar\Test\TestCase\Shell\Task;

use Cake\TestSuite\TestCase;
use Qobo\Calendar\Shell\Task\SyncTask;

/**
 * Qobo\Calendar\Shell\Task\SyncTask Test Case
 */
class SyncTaskTest extends TestCase
{

    /**
     * ConsoleIo mock
     *
     * @var \Cake\Console\ConsoleIo|\PHPUnit_Framework_MockObject_MockObject
     */
    public $io;

    /**
     * Test subject
     *
     * @var \Qobo\Calendar\Shell\Task\SyncTask
     */
    public $Sync;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();

        $this->Sync = $this->getMockBuilder('Qobo\Calendar\Shell\Task\SyncTask')
            ->setConstructorArgs([$this->io])
            ->getMock();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Sync);

        parent::tearDown();
    }

    /**
     * Test main method
     *
     * @return void
     */
    public function testMain()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
