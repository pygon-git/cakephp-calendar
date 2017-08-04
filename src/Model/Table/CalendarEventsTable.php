<?php
namespace Qobo\Calendar\Model\Table;

use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\I18n\Time;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use \RRule\RRule;

/**
 * CalendarEvents Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Calendars
 * @property \Cake\ORM\Association\BelongsTo $EventSources
 *
 * @method \Qobo\Calendar\Model\Entity\CalendarEvent get($primaryKey, $options = [])
 * @method \Qobo\Calendar\Model\Entity\CalendarEvent newEntity($data = null, array $options = [])
 * @method \Qobo\Calendar\Model\Entity\CalendarEvent[] newEntities(array $data, array $options = [])
 * @method \Qobo\Calendar\Model\Entity\CalendarEvent|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Qobo\Calendar\Model\Entity\CalendarEvent patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \Qobo\Calendar\Model\Entity\CalendarEvent[] patchEntities($entities, array $data, array $options = [])
 * @method \Qobo\Calendar\Model\Entity\CalendarEvent findOrCreate($search, callable $callback = null, $options = [])
 */
class CalendarEventsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('calendar_events');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Muffin/Trash.Trash');
        $this->addBehavior('AuditStash.AuditLog');

        $this->belongsTo('Calendars', [
            'foreignKey' => 'calendar_id',
            'joinType' => 'INNER',
            'className' => 'Qobo/Calendar.Calendars'
        ]);

        $this->belongsToMany('CalendarAttendees', [
            'joinTable' => 'events_attendees',
            'foreignKey' => 'calendar_event_id',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->uuid('id')
            ->allowEmpty('id', 'create');

        $validator
            ->allowEmpty('source');

        $validator
            ->requirePresence('title', 'create')
            ->allowEmpty('title');

        $validator
            ->requirePresence('content', 'create')
            ->allowEmpty('content');

        $validator
            ->dateTime('start_date')
            ->allowEmpty('start_date');

        $validator
            ->dateTime('end_date')
            ->allowEmpty('end_date');

        $validator
            ->time('duration')
            ->allowEmpty('duration');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['calendar_id'], 'Calendars'));

        return $rules;
    }

    /**
     * Get Events of specific calendar
     *
     * @param \Cake\ORM\Table $calendar record
     * @param array $options with filter params
     *
     * @return array $result of events (minimal structure)
     */
    public function getCalendarEvents($calendar, $options = [])
    {
        $result = [];

        if (!$calendar) {
            return $result;
        }

        $options = array_merge($options, ['calendarId' => $calendar->id]);
        $resultSet = $this->findCalendarEvents($options);

        if (empty($resultSet)) {
            return $result;
        }

        foreach ($resultSet as $k => $event) {
            $eventItem = $this->prepareEventData($event, $calendar);

            if (empty($eventItem)) {
                continue;
            }

            array_push($result, $eventItem);
        }

        return $result;
    }

    /**
     * Get Events of specific calendar
     *
     * @param \Cake\ORM\Table $calendar record
     * @param array $options with filter params
     *
     * @return array $result of events (minimal structure)
     */
    public function getEvents($calendar, $options = [])
    {
        $result = $infiniteEvents = [];

        if (!$calendar) {
            return $result;
        }

        $events = $this->findCalendarEvents($options);
        $infiniteEvents = $this->getInfiniteEvents($calendar->id, $events, $options);

        if (!empty($infiniteEvents)) {
            $resultSet = array_merge($events, $infiniteEvents);
        }

        if (empty($events)) {
            return $result;
        }

        foreach ($events as $k => $event) {
            $extra = [];
            if (!empty($event['calendar_attendees'])) {
                foreach ($event['calendar_attendees'] as $att) {
                    array_push($extra, $att->display_name);
                }
            }

            $eventItem = $this->prepareEventData($event, $calendar, [
                'title' => sprintf("%s - %s", $event['title'], implode("\n", $extra)),
            ]);

            if (empty($eventItem)) {
                continue;
            }

            array_push($result, $eventItem);

            $recurringEvents = $this->getRecurringEvents($eventItem, $options);

            if (!empty($recurringEvents)) {
                $result = array_merge($result, $recurringEvents);
            }
        }

        return $result;
    }

    /**
     * Get infinite calendar events for given calendar
     *
     * @param mixed $calendarId as its id.
     * @param array $events from findCalendarEvents
     * @param array $options containing month viewport (end/start interval).
     *
     * @return array $result containing event records
     */
    public function getInfiniteEvents($calendarId, $events, $options = [])
    {
        $result = $existingEventIds = [];

        $query = $this->find()
            ->where([
                'is_recurring' => true,
                'calendar_id' => $calendarId
            ])
            ->contain(['CalendarAttendees']);

        if (!$query) {
            return $result;
        }

        if (!empty($events)) {
            $existingEventIds = array_map(function ($item) {
                return $item->id;
            }, $events);
        }

        foreach ($query as $item) {
            if (in_array($item->id, $existingEventIds) || empty($item->recurrence)) {
                continue;
            }

            $rule = $this->getRRuleConfiguration(json_decode($item->recurrence, true));
            $rrule = new RRule($rule);

            if ($rrule->isInfinite()) {
                array_push($result, $item);
            }
        }

        return $result;
    }

    /**
     * Pre-populate Recurring events based on the parent event
     *
     * @param array $origin event object
     * @param array $options with events configs
     *
     * @return array $result with assembled recurring entities
     */
    public function getRecurringEvents($origin, array $options = [])
    {
        $result = [];

        $rule = $this->getRRuleConfiguration($origin['recurrence']);

        if (empty($rule)) {
            return $result;
        }

        $rrule = new RRule($rule, new \DateTime($origin['start_date']));

        $eventDates = $rrule->getOccurrencesBetween(
            new \DateTime($origin['start_date']),
            new \DateTime($options['period']['end_date'])
        );

        $startDateTime = new \DateTime($origin['start_date'], new \DateTimeZone('UTC'));
        $endDateTime = new \DateTime($origin['end_date'], new \DateTimeZone('UTC'));
        $diff = $startDateTime->diff($endDateTime);

        $diffString = $diff->format('%R%y years, %R%a days, %R%h hours, %R%i minutes');

        foreach ($eventDates as $eventDate) {
            if ($eventDate->format('Y-m-d') == $startDateTime->format('Y-m-d')) {
                continue;
            }

            $entity = $this->newEntity();
            $entity = $this->patchEntity($entity, $origin);

            $entity->start_date->year((int)$eventDate->format('Y'));
            $entity->start_date->month((int)$eventDate->format('m'));
            $entity->start_date->day((int)$eventDate->format('d'));

            $entity->end_date = clone $entity->start_date;
            $entity->end_date->modify($diffString);

            $entity->start_date->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $entity->end_date->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $entity->id = $origin['id'] . '__' . $this->setIdSuffix($entity);

            array_push($result, $entity->toArray());

            unset($entity);
        }

        return $result;
    }

    /**
     * Set ID suffix for recurring events
     *
     * We attach timestamp suffix for recurring events
     * that haven't been saved in the DB yet.
     *
     * @param array $entity of the event
     *
     * @return string $result with suffix.
     */
    public function setIdSuffix($entity = null)
    {
        if (is_object($entity)) {
            $result = strtotime($entity->start_date) . '_' . strtotime($entity->end_date);
        } else {
            $result = strtotime($entity['start_date']) . '_' . strtotime($entity['end_date']);
        }

        return $result;
    }

    /**
     * Get RRULE configuration from the event
     *
     * @param array $recurrence received from the calendar
     *
     * @return array $result containing the RRULE
     */
    public function getRRuleConfiguration($recurrence = [])
    {
        $result = '';

        if (empty($recurrence)) {
            return $result;
        }

        foreach ($recurrence as $rule) {
            if (preg_match('/^RRULE/i', $rule)) {
                $result = $rule;
            }
        }

        return $result;
    }

    /**
     * Get Calendar Event types based on configuration
     *
     * @param \Cake\ORM\Table $calendar record
     *
     * @return array $result containing event types for select2 dropdown
     */
    public function getEventTypes($calendar = null)
    {
        $result = [];

        if (!$calendar || !isset($calendar['calendar']['event_types'])) {
            return $result;
        }

        foreach ($calendar['calendar']['event_types'] as $k => $eventType) {
            array_push($result, $eventType);
        }

        return $result;
    }

    /**
     * Get Event info
     *
     * @param array $options containing event id
     *
     * @return array $result containing record data
     */
    public function getEventInfo($options = [])
    {
        $result = [];
        $end = $start = null;

        if (empty($options)) {
            return $result;
        }

        if (!empty($options['timestamp'])) {
            $parts = explode('_', $options['timestamp']);
            $start = date('Y-m-d H:i:s', $parts[0]);
            $end = date('Y-m-d H:i:s', $parts[1]);
        }

        $result = $this->find()
                ->where(['id' => $options['id']])
                ->contain(['CalendarAttendees'])
                ->first();

        //@NOTE: we're faking the start/end intervals for recurring events
        if (!empty($end)) {
            $time = Time::parse($end);
            $result->end_date = $time;
            unset($time);
        }

        if (!empty($start)) {
            $time = Time::parse($start);
            $result->start_date = $time;
            unset($time);
        }

        return $result;
    }

    /**
     * PrepareEventData method
     *
     * @param array $event of the calendar
     * @param array $calendar currently checked
     * @param array $options with extra configs
     *
     * @return array $item containing calendar event record.
     */
    protected function prepareEventData($event, $calendar, $options = [])
    {
        $item = [];

        $item = [
            'id' => $event['id'],
            'title' => (!empty($options['title']) ? $options['title'] : $event['title']),
            'content' => $event['content'],
            'start_date' => date('Y-m-d H:i:s', strtotime($event['start_date'])),
            'end_date' => date('Y-m-d H:i:s', strtotime($event['end_date'])),
            'color' => (empty($event['color']) ? $calendar->color : $event['color']),
            'source' => $event['source'],
            'source_id' => $event['source_id'],
            'calendar_id' => $calendar->id,
            'event_type' => (!empty($event['event_type']) ? $event['event_type'] : null),
            'is_recurring' => $event['is_recurring'],
            'recurrence' => json_decode($event['recurrence'], true),
        ];

        return $item;
    }

    /**
     * findCalendarEvents method
     *
     * @param array $options containing conditions for query
     *
     * @return array $result with events found.
     */
    protected function findCalendarEvents($options = [])
    {
        $conditions = [];

        if (!empty($options['calendarId'])) {
            $conditions['calendar_id'] = $options['calendarId'];
        }

        if (!empty($options['period']['start_date'])) {
            $conditions['start_date >='] = $options['period']['start_date'];
        }

        if (!empty($options['period']['end_date'])) {
            $conditions['end_date <='] = $options['period']['end_date'];
        }

        $result = $this->find()
                ->where($conditions)
                ->contain(['CalendarAttendees'])
                ->toArray();

        return $result;
    }
}
