<?php
namespace Qobo\Calendar\Model\Table;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * Calendars Model
 *
 * @property \Cake\ORM\Association\BelongsTo $CalendarSources
 * @property \Cake\ORM\Association\HasMany $CalendarEvents
 *
 * @method \Qobo\Calendar\Model\Entity\Calendar get($primaryKey, $options = [])
 * @method \Qobo\Calendar\Model\Entity\Calendar newEntity($data = null, array $options = [])
 * @method \Qobo\Calendar\Model\Entity\Calendar[] newEntities(array $data, array $options = [])
 * @method \Qobo\Calendar\Model\Entity\Calendar|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Qobo\Calendar\Model\Entity\Calendar patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \Qobo\Calendar\Model\Entity\Calendar[] patchEntities($entities, array $data, array $options = [])
 * @method \Qobo\Calendar\Model\Entity\Calendar findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CalendarsTable extends Table
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

        $this->setTable('calendars');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Muffin/Trash.Trash');

        $this->hasMany('CalendarEvents', [
            'foreignKey' => 'calendar_id',
            'className' => 'Qobo/Calendar.CalendarEvents'
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
            ->allowEmpty('name');

        $validator
            ->allowEmpty('color');

        $validator
            ->allowEmpty('icon');

        $validator
            ->allowEmpty('calendar_source');

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
        return $rules;
    }

    /**
     * Get Calendar entities.
     *
     * @param array $options for filtering calendars
     *
     * @return array $result containing calendar entities with event_types
     */
    public function getCalendars($options = [])
    {
        $result = $conditions = [];
        $table = TableRegistry::get('Qobo/Calendar.Calendars');
        $tableEvents = TableRegistry::get('Qobo/Calendar.CalendarEvents');

        if (!empty($options['id'])) {
            $conditions['id'] = $options['id'];
        } elseif (!empty($options['conditions'])) {
            $conditions = $options['conditions'];
        }

        $query = $table->find()
                ->where($conditions)
                ->order(['name' => 'ASC'])
                ->all();
        $result = $query->toArray();

        // loading types for calendars and events.
        $types = Configure::read('Calendar.Types');

        //adding event_types & events attached for the calendars
        foreach ($result as $k => $calendar) {
            $result[$k]->event_types = [];
            $result[$k]->calendar_events = [];

            $events = $tableEvents->getCalendarEvents($calendar, $options);

            if (!empty($events)) {
                $result[$k]->calendar_events = $events;
            }

            if (empty($types)) {
                continue;
            }

            foreach ($types as $type) {
                if ($type['value'] == $calendar->calendar_type) {
                    $result[$k]->event_types = $type['types'];
                }
            }
        }

        return $result;
    }

    /**
     * Get Calendar Types
     *
     * @param array $options with extra filters
     *
     * @return array $result containing calendar types.
     */
    public function getCalendarTypes($options = [])
    {
        $result = [];

        $config = Configure::read('Calendar.Types');

        if (!empty($config)) {
            foreach ($config as $k => $val) {
                $result[$val['value']] = $val['name'];
            }
        }

        return $result;
    }

    /**
     * Synchronize calendars
     *
     * @param array $options passed from the outside.
     *
     * @return array $result of the synchronize method.
     */
    public function syncCalendars($options = [])
    {
        $result = $saved = [];

        $event = new Event('Plugin.Calendars.Model.getCalendars', $this, [
            'options' => $options,
        ]);

        EventManager::instance()->dispatch($event);

        if (empty($event->result)) {
            return $result;
        }

        $receivedCalendarsData = $event->result;

        $appCalendars = [];

        foreach ($receivedCalendarsData as $k => $item) {
            if (!empty($item['calendar'])) {
                $appCalendars[] = $item['calendar'];
            }
        }

        $eventsTable = TableRegistry::get('Qobo/Calendar.CalendarEvents');

        foreach ($receivedCalendarsData as $k => $calendarData) {
            $diffCalendar = $this->_getItemDifferences(
                $this,
                $calendarData['calendar'],
                [
                    'source' => 'calendar_source',
                    'source_id' => 'calendar_source_id',
                    'range' => (!empty($options['period']) ? $options['period'] : []),
                ]
            );

            $saved[$k]['calendar'] = $this->saveItemDifferences($this, $diffCalendar);

            if (!empty($calendarData['events'])) {
                $saved[$k]['events'] = [];
                foreach ($calendarData['events'] as $key => $ev) {
                    $diffEvent = $this->_getItemDifferences(
                        $eventsTable,
                        $ev,
                        [
                            'source' => 'event_source',
                            'source_id' => 'event_source_id',
                            'range' => (!empty($options['period']) ? $options['period'] : []),
                        ]
                    );
                    $saved[$k]['events'][] = $this->saveItemDifferences(
                        $eventsTable,
                        $diffEvent,
                        [
                            'extra_fields' => [
                                'calendar_id' => $saved[$k]['calendar']->id
                            ]
                        ]
                    );
                }
            }
        }

        //@FIXME: add Deletion of calendars/events.


        if (!empty($saved)) {
            $result = $saved;
        }

        return $result;
    }

    /**
     * saveCalendarDifferences method
     *
     * Updating calendar DB with differences
     *
     * @param array $calendarDiff prepopulated calendars
     *
     * @return array $result with updated/deleted/added calendars.
     */
    public function saveItemDifferences($table, $diff = [], $options = [])
    {
        $result = [];

        foreach ($diff as $actionName => $items) {
            if (empty($items)) {
                continue;
            }

            foreach ($items as $k => $item) {
                $data = [];

                if (empty($item)) {
                    continue;
                }

                switch ($actionName) {
                    case 'add':
                        $entity = $table->newEntity();
                        $data = $item;
                        break;
                    case 'update':
                        $entity = $item['entity'];
                        $data = $item['data'];
                        break;
                }

                if (in_array($actionName, ['add', 'update']) && !empty($data)) {
                    if (!empty($options['extra_fields'])) {
                        $data = array_merge($data, $options['extra_fields']);
                    }

                    $entity = $table->patchEntity($entity, $data);
                    $result = $table->save($entity);
                }

                if (in_array($actionName, ['delete']) && !empty($item)) {
                    if ($table->delete($item)) {
                        $result = $item;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Collect calendars difference.
     *
     * @param array $data containing received calendars from the events/models.
     *
     * @return $calendarDiff containing the list of calendars to add/update/delete.
     */
    protected function _getItemDifferences($table, $item = null, $options = [])
    {
        $conditions = [];
        $source = $options['source'];
        $sourceId = $options['source_id'];

        $diff = [
            'add' => [],
            'update' => [],
            'delete' => [],
        ];

        if (empty($item)) {
            return $diff;
        }

        if (is_null($item[$source])) {
            $conditions[$source . ' IS'] = $item[$source];
        } else {
            $conditions[$source] = $item[$source];
        }

        $query = $table->find()
                ->where($conditions)
                ->all();

        $dbItems = $query->toArray();

        $toAdd = $this->_itemsToAdd($item, $dbItems, $sourceId);
        if (!empty($toAdd)) {
            $diff['add'][] = $toAdd;
        }

        $toUpdate = $this->_itemsToUpdate($item, $dbItems, $sourceId);
        if (!empty($toUpdate)) {
            $diff['update'][] = $toUpdate;
        }

        return $diff;
    }

    /**
     * Check if calendar should be added
     *
     * @param array $calendar to inspect
     * @param array $existingCalendars with same calendar_source
     *
     * @return array $result with the comparison result.
     */
    protected function _itemsToAdd($item, $dbItems = [], $fieldToCheck = null)
    {
        $result = $item;

        if (empty($dbItems)) {
            return $result;
        }

        foreach ($dbItems as $k => $dbItem) {
            if ($dbItem->$fieldToCheck == $item[$fieldToCheck]) {
                $result = [];
                break;
            }
        }

        return $result;
    }

    /**
     * Check if the calendar should be updated
     *
     * @param array $calendar to be checked
     * @param array $existingCalendars to loop through for changes
     *
     * @return array $result containing entity of the calendar from DB and changes in data key
     */
    protected function _itemsToUpdate($item, $dbItems = [], $fieldToCheck = null)
    {
        $found = null;
        $result = [
            'entity' => [],
            'data' => []
        ];

        if (empty($dbItems)) {
            return $result;
        }

        foreach ($dbItems as $dbItem) {
            if ($dbItem->$fieldToCheck == $item[$fieldToCheck]) {
                $found = $dbItem;
            }
        }

        if (empty($found)) {
            return $result;
        }

        $result['entity'] = $found;
        $result['data'] = $item;

        return $result;
    }
}
