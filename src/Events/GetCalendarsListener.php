<?php
namespace Qobo\Calendar\Events;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Network\Request;
use Cake\ORM\TableRegistry;

class GetCalendarsListener implements EventListenerInterface
{
    /**
     * {@inheritDoc}
     */
    public function implementedEvents()
    {
        return [
            'Calendars.Model.getCalendars' => 'getCalendars',
        ];
    }

    /**
     * Get Calendar entities
     *
     * @param Cake\Event\Event $event passed through model
     * @param array $options containing model options
     *
     * @return array $result containing filtered out calendars.
     */
    public function getCalendars(Event $event, $options = [])
    {
        $conditions = [];
        $table = TableRegistry::get('Qobo/Calendar.Calendars');

        if (!empty($options['id'])) {
            $conditions['id'] = $options['id'];
        }

        $query = $table->find()
                ->where($conditions)
                ->order(['name' => 'ASC'])
                ->all();
        $result = $query->toArray();

        // loading types for calendars and events.
        $types = Configure::read('Types');

        //adding event_types attached for the calendars
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

        $event->result = $result;
    }
}
