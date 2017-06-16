<?php
namespace Qobo\Calendar\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CalendarEventsFixture
 *
 */
class CalendarEventsFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'uuid', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'calendar_id' => ['type' => 'uuid', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'source_id' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'source' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'title' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'content' => ['type' => 'text', 'length' => 4294967295, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null],
        'start_date' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'end_date' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'duration' => ['type' => 'time', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'trashed' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'event_type' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
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
            'id' => '489b726e-b785-469b-a203-0f12b07cc983',
            'calendar_id' => 'f3cf5dd7-e466-419b-b16d-ce46abaf6f43',
            'source_id' => 'Lorem ipsum dolor sit amet',
            'source' => 'Lorem ipsum dolor sit amet',
            'title' => 'Lorem ipsum dolor sit amet',
            'content' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'start_date' => '2017-06-16 09:30:56',
            'end_date' => '2017-06-16 09:30:56',
            'duration' => '09:30:56',
            'created' => '2017-06-16 09:30:56',
            'modified' => '2017-06-16 09:30:56',
            'trashed' => null,
            'event_type' => 'Lorem ipsum dolor sit amet'
        ],
        [
            'id' => '489b726e-b785-469b-a203-0f12b07cc984',
            'calendar_id' => '9390cbc1-dc1d-474a-a372-de92dce85aae',
            'source_id' => null,
            'source' => null,
            'title' => 'Roxanne!',
            'content' => 'Lorem ipsum dolor',
            'start_date' => '2017-06-16 11:30:56',
            'end_date' => '2017-06-16 19:30:56',
            'duration' => '09:30:56',
            'created' => '2017-06-16 09:30:56',
            'modified' => '2017-06-16 09:30:56',
            'trashed' => null,
            'event_type' => 'Lorem ipsum dolor sit amet'
        ],

    ];
}
