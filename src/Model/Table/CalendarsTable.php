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
            ->allowEmpty('source');

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

        if (!empty($options['id'])) {
            $conditions['id'] = $options['id'];
        } elseif (!empty($options['conditions'])) {
            $conditions = $options['conditions'];
        }

        $query = $this->find()
                ->where($conditions)
                ->order(['name' => 'ASC'])
                ->all();
        $result = $query->toArray();

        // loading types for calendars and events.
        $types = Configure::read('Calendar.Types');

        //adding event_types & events attached for the calendars
        foreach ($result as $k => $calendar) {
            $result[$k]->event_types = [];

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
        $result = $saved = $savedCalendars = $removed = [];

        $event = new Event('Plugin.Calendars.Model.getCalendars', $this, [
            'options' => $options,
        ]);

        EventManager::instance()->dispatch($event);

        if (empty($event->result)) {
            return $result;
        }

        $appCalendars = $event->result;

        foreach ($appCalendars as $k => $calendarData) {
            $calendar = !empty($calendarData['calendar']) ? $calendarData['calendar'] : [];

            if (empty($calendar)) {
                continue;
            }

            $diffCalendar = $this->_getItemDifferences(
                $this,
                $calendar,
                [
                    'range' => (!empty($options['period']) ? $options['period'] : []),
                ]
            );

            $saved['modified'][] = $this->saveItemDifferences($this, $diffCalendar);
        }

        $ignored = $this->_itemsToDelete($this, $saved['modified']);

        $saved['removed'] = $this->saveItemDifferences($this, ['delete' => $ignored]);

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
                        $result[] = $item;
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
        $source = empty($options['source']) ? 'source' : $options['source'];
        $sourceId = empty($options['source_id']) ? 'source_id' : $options['source_id'];

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
     * @param array $existingCalendars with same source
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

    /**
     * Remove item from from the set
     *
     * @param ORM\Table $table instance of the target
     * @param array $items containing current items
     * @param array $options with extra config
     *
     * @return array $result containing the items that should be deleted.
     */
    protected function _itemsToDelete($table, $items, $options = [])
    {
        $result = $conditions = [];
        $source = empty($options['source']) ? 'source' : $options['source'];
        $sourceId = empty($options['source_id']) ? 'source_id' : $options['source_id'];

        if (!empty($options['range'])) {
            if (!empty($options['range']['start_date'])) {
                $conditions['start_date >='] = $options['range']['start_date'];
            }

            if (!empty($options['range']['end_date'])) {
                $conditions['end_date <='] = $options['range']['end_date'];
            }
        }

        $query = $table->find()
                    ->where($conditions)
                    ->all();

        $dbItems = $query->toArray();

        if (empty($dbItems) || empty($items)) {
            return $result;
        }

        foreach ($dbItems as $key => $dbItem) {
            foreach ($items as $k => $item) {
                if ($dbItem->$source == $item->$source
                    && $dbItem->$sourceId == $item->$sourceId) {
                    unset($dbItems[$key]);
                }
            }
        }

        if (!empty($dbItems)) {
            $result = $dbItems;
        }

        return $result;
    }
}
