<?php
namespace Qobo\Calendar\Controller;

use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\ORM\TableRegistry;
use Qobo\Calendar\Controller\AppController;
use Qobo\Utils\Utility;

/**
 * Calendars Controller
 *
 * @property \Qobo\Calendar\Model\Table\CalendarsTable $Calendars
 *
 * @method \Qobo\Calendar\Model\Entity\Calendar[] paginate($object = null, array $settings = [])
 */
class CalendarsController extends AppController
{
    /**
     * {@inheritDoc}
     */
    public function beforeFilter(Event $event)
    {
        $icons = Utility::getIcons();
        $colors = Utility::getColors();

        $calendarTypes = $this->Calendars->getCalendarTypes();

        $this->set('calendarTypes', $calendarTypes);
        $this->set('icons', $icons);
        $this->set('colors', $colors);
    }

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $event = new Event('Calendars.Model.getCalendars', $this, [
            'options' => []
        ]);

        EventManager::instance()->dispatch($event);

        $calendars = $event->result;

        $this->set(compact('calendars'));
        $this->set('_serialize', ['calendars']);
    }

    /**
     * View method
     *
     * @param string|null $id Calendar id.
     * @return void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $calendars = $this->Calendars->getCalendars(['id' => $id]);

        if (!empty($calendars)) {
            $calendar = array_shift($calendars);
        }

        $this->set('calendar', $calendar);
        $this->set('_serialize', ['calendar']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $calendar = $this->Calendars->newEntity();
        if ($this->request->is('post')) {
            $calendar = $this->Calendars->patchEntity($calendar, $this->request->getData());
            if ($this->Calendars->save($calendar)) {
                $this->Flash->success(__('The calendar has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The calendar could not be saved. Please, try again.'));
        }

        $this->set(compact('calendar'));
        $this->set('_serialize', ['calendar']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Calendar id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $calendar = $this->Calendars->get($id, [
            'contain' => []
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $calendar = $this->Calendars->patchEntity($calendar, $this->request->getData());
            if ($this->Calendars->save($calendar)) {
                $this->Flash->success(__('The calendar has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The calendar could not be saved. Please, try again.'));
        }

        $this->set(compact('calendar'));
        $this->set('_serialize', ['calendar']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Calendar id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $calendar = $this->Calendars->get($id);
        if ($this->Calendars->delete($calendar)) {
            $this->Flash->success(__('The calendar has been deleted.'));
        } else {
            $this->Flash->error(__('The calendar could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Get Calendar Events
     *
     * @return void
     */
    public function events()
    {
        $events = [];
        $calendar = null;

        $eventsTable = TableRegistry::get('Qobo/Calendar.CalendarEvents');

        $data = $this->request->getData();

        if (!empty($data['calendarId'])) {
            $calendars = $this->Calendars->getCalendars(['id' => $data['calendarId']]);
            if (!empty($calendars)) {
                $calendar = $calendars[0];
            }
            $resultSet = $eventsTable->getCalendarEvents($calendar, $data);
        }

        $event = new Event('Calendars.Model.getCalendarEvents', $this, [
            'calendar' => $calendar,
            'options' => $data,
        ]);

        EventManager::instance()->dispatch($event);

        if (!empty($event->result)) {
            $resultSet = $event->result;
        }

        if (!empty($resultSet)) {
            foreach ($resultSet as $event) {
                $events[] = [
                    'id' => $event['id'],
                    'title' => $event['title'],
                    'description' => $event['content'],
                    'start' => $event['start_date'],
                    'end' => $event['end_date'],
                    'color' => (empty($event['color']) ? $calendar->color : $event['color']),
                    // NOTE: adding extra variable for lookup values, of the calendar.
                    'calendar_id' => $calendar->id,
                    'event_type' => (!empty($event['event_type']) ? $event['event_type'] : null),
                ];
            }
        }

        $this->set(compact('events'));
        $this->set('_serialize', ['events']);
    }

    /**
     * @return array $colors of the calendar
     */
    protected function _getColors()
    {
        return $this->colors;
    }
}
