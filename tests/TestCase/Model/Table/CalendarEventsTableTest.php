<?php
namespace Qobo\Calendar\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Qobo\Calendar\Model\Table\CalendarEventsTable;

/**
 * Qobo\Calendar\Model\Table\CalendarEventsTable Test Case
 */
class CalendarEventsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Qobo\Calendar\Model\Table\CalendarEventsTable
     */
    public $CalendarEvents;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.qobo/calendar.calendar_events',
        'plugin.qobo/calendar.calendars',
        'plugin.qobo/calendar.events_attendees',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('CalendarEvents') ? [] : ['className' => 'Qobo\Calendar\Model\Table\CalendarEventsTable'];
        $this->CalendarEvents = TableRegistry::get('CalendarEvents', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CalendarEvents);

        parent::tearDown();
    }

    public function testGetCalendarEvents()
    {
        $calendars = TableRegistry::get('Qobo/Calendar.Calendars');
        $dbItems = $calendars->getCalendars();

        $result = $this->CalendarEvents->getCalendarEvents($dbItems[0]);
        $this->assertTrue(is_array($result));
        $this->assertNotEmpty($result);
        $this->assertEquals($result[0]['color'], '#05497d');
        $this->assertEquals($result[0]['id'], '489b726e-b785-469b-a203-0f12b07cc984');
    }

    public function testGetCalendarEventsWithPeriod()
    {
        $calendars = TableRegistry::get('Qobo/Calendar.Calendars');
        $dbItems = $calendars->getCalendars();

        $result = $this->CalendarEvents->getCalendarEvents($dbItems[0], [
            'period' => [
                'start_date' => '2017-06-16 09:00:00',
                'end_date' => '2017-06-16 20:00:00',
            ],
        ]);

        $this->assertNotEmpty($result);
        $this->assertTrue(is_array($result));
        $this->assertEquals($result[0]['id'], '489b726e-b785-469b-a203-0f12b07cc984');

        $result = $this->CalendarEvents->getCalendarEvents(null);
        $this->assertEquals($result, []);
        $this->assertTrue(is_array($result));
    }

    /**
     * @dataProvider testEventTitleProvider
     */
    public function testSetEventTitle($data, $expected)
    {
        $calendars = TableRegistry::get('Qobo/Calendar.Calendars');
        $dbItems = $calendars->getCalendars();

        $title = $this->CalendarEvents->setEventTitle($data, $dbItems[0]);
        $this->assertEquals($title, $expected);
    }

    public function testEventTitleProvider()
    {
        return [
            [
                ['CalendarEvents' => [
                    'start_date' => '2017-09-01 09:00:00',
                    'end_date' => '2017-09-02 09:00:00'
                    ]
                ],
                'Calendar - 1 Event',
            ],
            [
                ['CalendarEvents' => [
                    'start_date' => '2017-09-01 09:00:00',
                    'end_date' => '2017-09-02 09:00:00',
                    'event_type' => 'foobar',
                    ]
                ],
                'Calendar - 1 - Foobar',
            ]
        ];
    }

    public function testGetEventTypes()
    {
        $calendars = TableRegistry::get('Qobo/Calendar.Calendars');
        $dbItems = $calendars->getCalendars();

        foreach ($dbItems as $item) {
            $this->assertNotEmpty($item->event_types);
        }

        $this->assertEquals([], $this->CalendarEvents->getEventTypes());

        $testType = [
            'foo' => [
                'name' => 'foo',
                'value' => 'foo',
            ]
        ];

        $testCalendar = clone $dbItems[0];

        $testCalendar->event_types = $testType;

        $result = $this->CalendarEvents->getEventTypes($testCalendar);
        $this->assertEquals([ ['name' => 'foo', 'value' => 'foo'] ], $result);
    }
}
