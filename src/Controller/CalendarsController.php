<?php
namespace Qobo\Calendar\Controller;

use Cake\ORM\TableRegistry;
use Qobo\Calendar\Controller\AppController;

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
     * @var array $colors for the Calendars
     */
    public $colors = [
        '#e3910f' => 'Orange',
        '#ffc8e8' => 'Pink',
        '#e0e8f2' => 'Ice Blue',
        '#C0362C' => 'Red',
        '#668D3C' => 'Green',
        '#007996' => 'Blue',
    ];

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $calendars = $this->paginate($this->Calendars);

        $this->set(compact('calendars'));
        $this->set('_serialize', ['calendars']);
    }

    /**
     * View method
     *
     * @param string|null $id Calendar id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $calendar = $this->Calendars->get($id, [
            'contain' => ['CalendarEvents']
        ]);

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

        $this->set('calendarColors', $this->_getColors());
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

        $this->set('calendarColors', $this->_getColors());
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
     * @return array $events containing indexed array of events by calendar.
     */
    public function getEvents()
    {
        $events = [];
        $calendar = null;

        $eventTable = TableRegistry::get('CalendarEvents');

        $data = $this->request->getData();

        if (!empty($data['calendarId'])) {
            $calendar = $this->Calendars->get($data['calendarId']);

            $resultSet = $eventTable->find()
                ->where(
                    [
                        'calendar_id' => $data['calendarId']
                    ]
                )
                ->toArray();
        }

        if (!empty($resultSet)) {
            foreach ($resultSet as $event) {
                $events[] = [
                    'id' => $event['id'],
                    'title' => $event['title'],
                    'description' => $event['content'],
                    'start' => $event['start_date'],
                    'end' => $event['end_date'],
                    'color' => (isset($calendar) ? $calendar->color : 'blue'),
                ];
            }
        }

        $this->set(compact('events'));
        $this->set('_serialize', ['events']);
    }

    protected function _getColors()
    {
        return $this->colors;
    }
}
