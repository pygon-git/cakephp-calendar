<?php
namespace Qobo\Calendar\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * EventsAttendeesFixture
 *
 */
class EventsAttendeesFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'uuid', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'calendar_event_id' => ['type' => 'uuid', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'calendar_attendee_id' => ['type' => 'uuid', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'trashed' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'response_status' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => '8e5223c7-fb0e-4cae-85a1-7f2dc52e1300',
            'calendar_event_id' => '7662a4d0-f52e-46a2-b94b-3844ac02ae3b',
            'calendar_attendee_id' => 'f62a89e0-ee98-44db-add8-e17273d5bd0d',
            'trashed' => '2017-08-04 13:53:57',
            'response_status' => 'Lorem ipsum dolor sit amet'
        ],
    ];
}
