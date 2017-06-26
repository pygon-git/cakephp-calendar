<?php
namespace Qobo\Calendar\Model\Entity;

use Cake\ORM\Entity;

/**
 * CalendarAttendee Entity
 *
 * @property string $id
 * @property string $calendar_event_id
 * @property string $source_id
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property \Cake\I18n\Time $trashed
 *
 * @property \Qobo\Calendar\Model\Entity\Source $source
 * @property \Qobo\Calendar\Model\Entity\CalendarEvent $calendar_event
 */
class CalendarAttendee extends Entity
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
