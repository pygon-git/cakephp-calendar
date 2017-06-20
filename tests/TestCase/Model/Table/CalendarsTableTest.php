<?php
namespace Qobo\Calendar\Test\TestCase\Model\Table;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Qobo\Calendar\Model\Table\CalendarsTable;

/**
 * Qobo\Calendar\Model\Table\CalendarsTable Test Case
 */
class CalendarsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Qobo\Calendar\Model\Table\CalendarsTable
     */
    public $Calendars;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.qobo/calendar.calendars',
        'plugin.qobo/calendar.calendar_events'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Calendars') ? [] : ['className' => 'Qobo\Calendar\Model\Table\CalendarsTable'];
        $this->Calendars = TableRegistry::get('Calendars', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Calendars);

        parent::tearDown();
    }

    public function testGetCalendarTypes()
    {
        $result = $this->Calendars->getCalendarTypes();
        $this->assertTrue(is_array($result));

        Configure::write('Calendar.Types', ['foo' => ['name' => 'bar', 'value' => 'bar']]);
        $result = $this->Calendars->getCalendarTypes();
        $this->assertEquals(['bar' => 'bar'], $result);
    }

    public function testGetCalendars()
    {
        $result = $this->Calendars->getCalendars();
        $this->assertTrue(!empty($result));

        $result = $this->Calendars->getCalendars(['id' => '9390cbc1-dc1d-474a-a372-de92dce85aae']);
        $this->assertNotEmpty($result);
        $this->assertEquals($result[0]->id, '9390cbc1-dc1d-474a-a372-de92dce85aae');

        $result = $this->Calendars->getCalendars(['conditions' => ['id' => '9390cbc1-dc1d-474a-a372-de92dce85aaa']]);
        $this->assertNotEmpty($result);
        $this->assertEquals($result[0]->id, '9390cbc1-dc1d-474a-a372-de92dce85aaa');

        Configure::write('Calendar.Types', ['foo' => ['name' => 'bar', 'value' => 'bar']]);
        $result = $this->Calendars->getCalendars(['conditions' => ['id' => '9390cbc1-dc1d-474a-a372-de92dce85aaa']]);
    }

    public function testItemsToAdd()
    {
        $dbItems = $this->Calendars->getCalendars();
        $item = $dbItems[0];

        $result = $this->Calendars->itemsToAdd($item, $dbItems, 'source');
        $this->assertTrue(is_array($result));

        $result = $this->Calendars->itemsToAdd($item, [], 'source');
        $this->assertEquals($result, $item);

        $item->source = 'foobar';
        $dbItems = $this->Calendars->getCalendars();

        $result = $this->Calendars->itemsToAdd($item, $dbItems, 'source');
        $this->assertNotEmpty($result);
        $this->assertEquals($item->id, $result->id);
    }

    public function testItemsToUpdate()
    {
        $result = $this->Calendars->itemsToUpdate([]);
        $this->assertEquals($result, ['entity' => [], 'data' => []]);

        $dbItems = $this->Calendars->getCalendars();
        $item = $dbItems[0];

        $result = $this->Calendars->itemsToUpdate($item, $dbItems, 'source');
        $this->assertNotEmpty($result['data']);
        $this->assertNotEmpty($result['entity']);

        $item->source = 'foobar';
        $dbItems = $this->Calendars->getCalendars();
        $result = $this->Calendars->itemsToUpdate($item, $dbItems, 'source');
        $this->assertEquals($result['entity'], []);
        $this->assertEquals($result['data'], []);
    }

    public function testItemsToDelete()
    {
        $items = [];
        $options = [];

        $result = $this->Calendars->itemsToDelete($this->Calendars, $items, $options);
        $this->assertTrue(is_array($result));
        $this->assertEmpty($result);

        $dbItems = $this->Calendars->getCalendars();
        $result = $this->Calendars->itemsToDelete($this->Calendars, [$dbItems[0]], $options);
        $this->assertTrue(is_array($result));
        $this->assertNotEmpty($result);
    }

    public function testSyncCalendars()
    {
        $result = $this->Calendars->syncCalendars();
        $this->assertTrue(is_array($result));
    }

    public function testSyncCalendarEvents()
    {
        $dbItems = $this->Calendars->getCalendars();
        $result = $this->Calendars->syncCalendarEvents($dbItems[0]);
        $this->assertTrue(is_array($result));

        $result = $this->Calendars->syncCalendarEvents([]);
        $this->assertEmpty($result);
    }

    public function testSaveItemDifferences()
    {
        $result = $this->Calendars->saveItemDifferences($this->Calendars);
        $this->assertEmpty($result);
    }

    public function testGetItemDifferences()
    {
        $result = $this->Calendars->getItemDifferences($this->Calendars);
        $this->assertTrue(is_array($result));
        $this->assertEquals($result, ['add' => [], 'update' => [], 'delete' => []]);
    }

    public function testGetCalendarTemplatesFieldList()
    {
        $result = Configure::read('Calendar.Templates');
        $this->assertNotEmpty($result);
        $this->assertTrue(is_array($result));

        $templates = $this->Calendars->getCalendarTemplatesFieldList();
        $this->assertTrue(is_array($templates));
        $this->assertEquals($templates, ['_default' => 'Default Template', 'calls' => 'Calls', 'meetings' => 'Meetings']);
    }

    public function testGetCalendarTemplatesFieldListEmpty()
    {
        Configure::delete('Calendar.Templates');
        $result = Configure::read('Calendar.Templates');
        $this->assertEmpty($result);

        $result = $this->Calendars->getCalendarTemplatesFieldList();
        $this->assertEquals($result, []);
    }

    public function testGetCalendarTemplatesField()
    {
        $result = $this->Calendars->getCalendarTemplatesField();
        $this->assertTrue(is_array($result));
        $this->assertNotEmpty($result);

        $dummyObj = (object)[
            'id' => 'foobar',
            'templates' => json_encode([
                '_default' => [
                    'name' => 'Default Foo',
                    'value' => '_default',
                    'minDuration' => '100 years'
                ]
            ])
        ];

        $result = $this->Calendars->getCalendarTemplatesField($dummyObj);
        $this->assertNotEmpty($result);
        $this->assertEquals($result, ['_default']);
    }

    public function testSetCalendarTemplatesField()
    {
        $result = $this->Calendars->setCalendarTemplatesField();
        $this->assertNotEmpty($result);

        $result = $this->Calendars->setCalendarTemplatesField(['templates' => ['_default', 'meetings']]);
        $this->assertNotEmpty($result);
        $result = json_decode($result, true);
        $this->assertEquals(['_default', 'meetings'], array_keys($result));
    }
}
