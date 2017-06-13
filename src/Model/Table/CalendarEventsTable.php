<?php
namespace Qobo\Calendar\Model\Table;

use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

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

        $this->belongsTo('Calendars', [
            'foreignKey' => 'calendar_id',
            'joinType' => 'INNER',
            'className' => 'Qobo/Calendar.Calendars'
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
            ->notEmpty('title');

        $validator
            ->requirePresence('content', 'create')
            ->notEmpty('content');

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
     * @param Cake\Table\Entity $calendar record
     * @param array $options with filter params
     *
     * @return array $result of events (minimal structure)
     */
    public function getCalendarEvents($calendar, $options = [])
    {
        $result = $conditions = [];

        if (!$calendar) {
            return $result;
        }
        $conditions['calendar_id'] = $calendar->id;

        if (!empty($options['period'])) {
            $conditions['start_date >='] = $options['period']['start_date'];
            $conditions['end_date <='] = $options['period']['end_date'];
        }

        $resultSet = $this->find()
                ->where($conditions)
                ->toArray();

        if (!empty($resultSet)) {
            foreach ($resultSet as $event) {
                $result[] = [
                    'id' => $event['id'],
                    'title' => $event['title'],
                    'content' => $event['content'],
                    'start_date' => date('Y-m-d H:i:s', strtotime($event['start_date'])),
                    'end_date' => date('Y-m-d H:i:s', strtotime($event['end_date'])),
                    'color' => (empty($event['color']) ? $calendar->color : $event['color']),
                    // NOTE: adding extra variable for lookup values, of the calendar.
                    'source' => $event['source'],
                    'source_id' => $event['source_id'],
                    'calendar_id' => $calendar->id,
                    'event_type' => (!empty($event['event_type']) ? $event['event_type'] : null),
                ];
            }
        }

        return $result;
    }

    /**
     * Get Calendar Event types based on configuration
     *
     * @param Cake\Table\Entity $calendar record
     *
     * @return array $result containing event types for select2 dropdown
     */
    public function getEventTypes($calendar)
    {
        $result = [];

        if (!$calendar || !isset($calendar->event_types)) {
            return $result;
        }

        foreach ($calendar->event_types as $k => $eventType) {
            array_push($result, [
                'id' => $eventType['value'],
                'text' => $eventType['name'],
            ]);
        }

        return $result;
    }
}
