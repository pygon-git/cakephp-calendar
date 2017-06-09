<?php
namespace Qobo\Calendar\Events;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventManager;
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
            'App.Calendars.Model.getCalendars' => 'getPluginCalendars',
            'Plugin.Calendars.Model.getCalendars' => 'sendGetCalendarsToApp',
        ];
    }

    /**
     * Re-broadcasting the event outside of the plugin
     *
     * @param Cake\Event\Event $event received by the plugin
     * @param array $options for calendar conditions
     *
     * @return void
     */
    public function sendGetCalendarsToApp(Event $event, $options = [])
    {
        $eventName = preg_replace('/^(Plugin)/','App', $event->name());

        $ev = new Event($eventName, $this, [
            'options' => $options
        ]);

        EventManager::instance()->dispatch($ev);

        $event->result = $ev->result;
    }

    /**
     * Get calendars from the plugin only.
     *
     * @param Cake\Event\Event $event passed through
     * @param array $options for calendars
     *
     * @return void
     */
    public function getPluginCalendars(Event $event, $options = [])
    {
        $content = $result = [];
        $table = TableRegistry::get('Qobo/Calendar.Calendars');

        if (!empty($event->result)) {
            $result = $event->result;
        }

        // locally created calendars don't have calendar_source_id (external link).
        $options = [
            'conditions' => [
                'calendar_source LIKE' => 'Plugin__%',
            ]
        ];

        $calendars = $table->getCalendars($options);

        if (empty($calendars)) {
            return;
        }

        foreach ($calendars as $k => $calendar) {
            $events = !empty($calendar->calendar_events) ? $calendar->calendar_events : [];
            unset($calendar->calendar_events);

            $content[$k]['calendar'] = json_decode(json_encode($calendar), true);
            $content[$k]['events'] = json_decode(json_encode($events), true);
        }

        if (!empty($content)) {
            $result = array_merge($result, $content);
        }

        $event->result = $result;
    }
}
