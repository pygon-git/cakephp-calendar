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
        $result = [];

        $event = new Event('Plugin.Calendars.Model.getCalendars', $this, [
            'options' => $options,
        ]);

        EventManager::instance()->dispatch($event);

        if (empty($event->result)) {
            return $result;
        }

        $calendarDiff = $this->_getCalendarsDifference($event->result);

        foreach ($calendarDiff as $actionName => $items) {
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
                        $entity = $this->newEntity();
                        $data = $item;
                        break;
                    case 'update':
                        $entity = $item['entity'];
                        $data = $item['data'];
                        break;
                }

                if (in_array($actionName, ['add', 'update']) && !empty($data)) {
                    $entity = $this->patchEntity($entity, $data);
                    $result[$actionName][] = $this->save($entity);
                }

                if (in_array($actionName, ['delete']) && !empty($item)) {
                    if ($this->delete($item)) {
                        $result['delete'][] = $item;
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
    protected function _getCalendarsDifference($data = [])
    {
        $calendars = [];
        $calendarDiff = [
            'add' => [],
            'update' => [],
            'delete' => [],
        ];

        foreach ($data as $k => $calendarData) {
            $calendar = !empty($calendarData['calendar']) ? $calendarData['calendar'] : null;
            $calendars[] = $calendar;

            $query = $this->find()
                        ->where([
                            'calendar_source' => $calendar['calendar_source'],
                        ])
                        ->all();

            $existingCalendars = $query->toArray();

            $toAdd = $this->_calendarToAdd($calendar, $existingCalendars);
            if (!empty($toAdd)) {
                $calendarDiff['add'][] = $toAdd;
            }

            $toUpdate = $this->_calendarToUpdate($calendar, $existingCalendars);
            if (!empty($toUpdate)) {
                $calendarDiff['update'][] = $toUpdate;
            }
        }

        $toDelete = $this->_calendarsToDelete($calendars);
        $calendarDiff['delete'] = $toDelete;

        return $calendarDiff;
    }

    /**
     * Check if calendar should be added
     *
     * @param array $calendar to inspect
     * @param array $existingCalendars with same calendar_source
     *
     * @return array $result with the comparison result.
     */
    protected function _calendarToAdd($calendar, $existingCalendars = [])
    {
        $result = $calendar;

        if (empty($existingCalendars)) {
            return $result;
        }

        foreach ($existingCalendars as $k => $item) {
            if ($item->calendar_source_id == $calendar['calendar_source_id']) {
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
    protected function _calendarToUpdate($calendar, $existingCalendars = [])
    {
        $found = [];
        $result = [
            'entity' => [],
            'data' => []
        ];

        if (empty($existingCalendars)) {
            return $result;
        }

        foreach ($existingCalendars as $item) {
            if ($item->calendar_source_id == $calendar['calendar_source_id']) {
                $found = $item;
            }
        }

        if (empty($found)) {
            return $result;
        }

        $fieldsToCheck = ['name', 'color', 'icon'];

        foreach ($fieldsToCheck as $fieldName) {
            // what should be changed.
            if ($found->$fieldName != $calendar[$fieldName]) {
                $result['data'][$fieldName] = $calendar[$fieldName];
            }
        }

        if (!empty($result['data'])) {
            $result['entity'] = $found;
        } else {
            $result = [];
        }

        return $result;
    }

    /**
     * Check calendars for deletion
     *
     * @param array $calendars taht should be unset from the DB if any match.
     *
     * @return array $dbCalendars that should be removed.
     */
    protected function _calendarsToDelete($calendars = [])
    {
        $query = $this->find()->all();
        $dbCalendars = $query->toArray();

        foreach ($calendars as $calendar) {
            foreach ($dbCalendars as $k => $dbCalendar) {
                if ($calendar['calendar_source_id'] == $dbCalendar->calendar_source_id
                    && $calendar['calendar_source'] == $dbCalendar->calendar_source) {
                    unset($dbCalendars[$k]);
                }
            }
        }

        return $dbCalendars;
    }
}
