<?php
namespace Qobo\Calendar\Controller;

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
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Calendars']
        ];
        $calendarEvents = $this->paginate($this->CalendarEvents);

        $this->set(compact('calendarEvents'));
        $this->set('_serialize', ['calendarEvents']);
    }

    /**
     * View method
     *
     * @param string|null $id Calendar Event id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $calendarEvent = $this->CalendarEvents->get($id, [
            'contain' => ['Calendars']
        ]);

        $this->set('calendarEvent', $calendarEvent);
        $this->set('_serialize', ['calendarEvent']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $calendarEvent = $this->CalendarEvents->newEntity();
        if ($this->request->is('post')) {
            $calendarEvent = $this->CalendarEvents->patchEntity($calendarEvent, $this->request->getData());
            if ($this->CalendarEvents->save($calendarEvent)) {
                $this->Flash->success(__('The calendar event has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The calendar event could not be saved. Please, try again.'));
        }
        $calendars = $this->CalendarEvents->Calendars->find('list', ['limit' => 200]);
        $this->set(compact('calendarEvent', 'calendars'));
        $this->set('_serialize', ['calendarEvent']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Calendar Event id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $calendarEvent = $this->CalendarEvents->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $calendarEvent = $this->CalendarEvents->patchEntity($calendarEvent, $this->request->getData());
            if ($this->CalendarEvents->save($calendarEvent)) {
                $this->Flash->success(__('The calendar event has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The calendar event could not be saved. Please, try again.'));
        }
        $calendars = $this->CalendarEvents->Calendars->find('list', ['limit' => 200]);
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

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Create Event via AJAX call
     */
    public function createEvent()
    {
        $result = [];
        $calendarEvent = $this->CalendarEvents->newEntity();

        if ($this->request->is(['patch', 'post', 'put'])) {
            $calendarEvent = $this->CalendarEvents->patchEntity($calendarEvent, $this->request->getData());
            if ($this->CalendarEvents->save($calendarEvent)) {
                $result['message'] = 'Successfully saved Event';
            } else {
                $result['message'] = 'Couldn\'t save Calendar Event';
            }
        }

        $this->set(compact('result'));
        $this->set('_serialize', ['result']);
    }

    /**
     * View Event via AJAX
     */
    public function viewEvent()
    {
        $this->viewBuilder()->setLayout('Qobo/Calendar.ajax');

        $calEvent = [];

        if ($this->request->is(['post', 'patch', 'put'])) {
            $data = $this->request->getData();

            $calEvent = $this->CalendarEvents->get($data['id'], ['contain' => 'Calendars']);
        }
        $this->set(compact('calEvent'));
        $this->set('_serialize', ['calEvent']);
    }
}
