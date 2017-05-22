<?php
namespace Qobo\Calendar\Model\Entity;

use Cake\ORM\Entity;

/**
 * CalendarEvent Entity
 *
 * @property string $id
 * @property string $calendar_id
 * @property string $event_source_id
 * @property string $title
 * @property string $content
 * @property \Cake\I18n\Time $start_date
 * @property \Cake\I18n\Time $end_date
 * @property \Cake\I18n\Time $duration
 *
 * @property \Qobo\Calendar\Model\Entity\EventSource $event_source
 * @property \Qobo\Calendar\Model\Entity\Calendar $calendar
 */
class CalendarEvent extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];
}
