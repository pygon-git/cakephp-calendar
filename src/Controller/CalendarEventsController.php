<?php
namespace Qobo\Calendar\Controller;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\ORM\TableRegistry;
use Qobo\Calendar\Controller\AppController;

/**
 * CalendarEvents Controller
 *
 * @property \Qobo\Calendar\Model\Table\CalendarEventsTable $CalendarEvents
 *
 * @method \Qobo\Calendar\Model\Entity\CalendarEvent[] paginate($object = null, array $settings = [])
 */
class CalendarEventsController extends AppController
{
    /**
     * Edit method
     *
     * @param string|null $id Calendar Event id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $eventTypes = [];

        $calendarEvent = $this->CalendarEvents->get($id, [
            'contain' => ['Calendars']
        ]);

        $calendars = $this->CalendarEvents->Calendars->find('list', ['limit' => 200]);

        $calendarType = $calendarEvent->calendar->calendar_type;
        $types = Configure::read('Types');

        foreach ($types as $typeInfo) {
            if ($typeInfo['value'] === $calendarType) {
                foreach ($typeInfo['types'] as $type) {
                    $eventTypes[$type['value']] = $type['name'];
                }
            }
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $calendarEvent = $this->CalendarEvents->patchEntity($calendarEvent, $this->request->getData());
            if ($this->CalendarEvents->save($calendarEvent)) {
                $this->Flash->success(__('The calendar event has been saved.'));

                return $this->redirect(['plugin' => 'Qobo/Calendar', 'controller' => 'Calendars', 'action' => 'index']);
            }
            $this->Flash->error(__('The calendar event could not be saved. Please, try again.'));
        }

        $this->set('eventTypes', $eventTypes);
        $this->set(compact('calendarEvent', 'calendars'));
        $this->set('_serialize', ['calendarEvent']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Calendar Event id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $calendarEvent = $this->CalendarEvents->get($id);
        if ($this->CalendarEvents->delete($calendarEvent)) {
            $this->Flash->success(__('The calendar event has been deleted.'));
        } else {
            $this->Flash->error(__('The calendar event could not be deleted. Please, try again.'));
        }

        return $this->redirect(['plugin' => 'Qobo/Calendar', 'controller' => 'Calendars', 'action' => 'index']);
    }

    /**
     * Create Event via AJAX call
     */
    public function add()
    {
        $result = [];
        $calendarEvent = $this->CalendarEvents->newEntity();
        $this->Calendars = TableRegistry::get('Calendars');

        if ($this->request->is(['patch', 'post', 'put'])) {
            $calendarEvent = $this->CalendarEvents->patchEntity($calendarEvent, $this->request->getData());

            $calendar = $this->Calendars->get($calendarEvent->calendar_id);

            $saved = $this->CalendarEvents->save($calendarEvent);
            $entity = [
                'id' => $saved->id,
                'title' => $saved->title,
                'start' => $saved->start_date,
                'end' => $saved->end_date,
                'color' => $calendar->color
            ];

            if ($saved) {
                $result['entity'] = $entity;
                $result['message'] = 'Successfully saved Event';
            } else {
                $result['entity'] = [];
                $result['message'] = 'Couldn\'t save Calendar Event';
            }
        }

        $event = $result;

        $this->set(compact('event'));
        $this->set('_serialize', ['event']);
    }

    /**
     * View Event via AJAX
     */
    public function view()
    {
        $this->viewBuilder()->setLayout('Qobo/Calendar.ajax');

        $calEvent = [];

        if ($this->request->is(['post', 'patch', 'put'])) {
            $event = new Event('Calendars.Model.getCalendarEventInfo', $this, [
                'options' => $this->request->getData(),
            ]);

            EventManager::instance()->dispatch($event);

            $calEvent = $event->result;
        }

        $this->set(compact('calEvent'));
        $this->set('_serialize', ['calEvent']);
    }

    /**
     * @return array $eventTypes of the current calendar type.
     */
    public function getEventTypes()
    {
        $eventTypes = [];

        if ($this->request->is(['post', 'patch', 'put'])) {
            $data = $this->request->getData();

            $event = new Event('Calendars.Model.getCalendars', $this, [
                'options' => ['id' => $data['id']]
            ]);

            EventManager::instance()->dispatch($event);

            $calendar = !empty($event->result[0]) ? $event->result[0]: [];

            if (isset($calendar->event_types) && !empty($calendar->event_types)) {
                foreach ($calendar->event_types as $k => $eventType) {
                    array_push($eventTypes, [
                        'id' => $eventType['value'],
                        'text' => $eventType['name'],
                    ]);
                }
            }
        }

        $this->set(compact('eventTypes'));
        $this->set('_serialize', ['eventTypes']);
    }
}
