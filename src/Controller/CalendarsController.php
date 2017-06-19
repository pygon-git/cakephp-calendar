<?php
namespace Qobo\Calendar\Controller;

use Cake\Core\Configure;
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
        $calendars = $this->Calendars->getCalendars();

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
        $calendar = null;
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
        $templatesConfig = Configure::read('Calendar.Templates');
        $templates = [];

        foreach ($templatesConfig as $k => $item) {
            $templates[$item['value']] = $item['name'];
        }

        $calendar = $this->Calendars->newEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();

            if (empty($data['templates'])) {
                $data['templates'] = array_merge($data['templates'], ['_default' => $templatesConfig['_default']]);
            } else {
                $templateChoices = [];
                foreach ($templatesConfig as $k => $template) {
                    if (in_array($template['value'], array_values($data['templates']))) {
                        $templateChoices[$template['value']] = $template;
                    }
                }
                if (!empty($templateChoices)) {
                    $data['templates'] = $templateChoices;
                }
            }

            $data['templates'] = json_encode($data['templates']);
            $calendar = $this->Calendars->patchEntity($calendar, $data);

            if ($this->Calendars->save($calendar)) {
                $this->Flash->success(__('The calendar has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The calendar could not be saved. Please, try again.'));
        }

        $this->set(compact('calendar', 'templates'));
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
        $templatesConfig = Configure::read('Calendar.Templates');
        $templates = $currentTemplates = [];

        foreach ($templatesConfig as $k => $item) {
            $templates[$item['value']] = $item['name'];
        }

        $calendar = $this->Calendars->get($id, [
            'contain' => []
        ]);

        if (!empty($calendar->templates)) {
            $currentTemplates = json_decode($calendar->templates, true);
            $tmp = [];
            foreach ($currentTemplates as $k => $template) {
                $tmp[] = $template['value'];
            }

            $calendar->templates = $tmp;
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();

            if (empty($data['templates'])) {
                $data['templates'] = ['_default' => $templatesConfig['_default']];
            } else {
                $templateChoices = [];
                foreach ($templatesConfig as $k => $template) {
                    if (in_array($template['value'], array_values($data['templates']))) {
                        $templateChoices[$template['value']] = $template;
                    }
                }
                if (!empty($templateChoices)) {
                    $data['templates'] = $templateChoices;
                }
            }
            $data['templates'] = json_encode($data['templates']);
            $calendar = $this->Calendars->patchEntity($calendar, $data);

            if ($this->Calendars->save($calendar)) {
                $this->Flash->success(__('The calendar has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The calendar could not be saved. Please, try again.'));
        }

        $this->set(compact('calendar', 'templates'));
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

            $events = $eventsTable->getCalendarEvents($calendar, $data);
        }

        $this->set(compact('events'));
        $this->set('_serialize', ['events']);
    }
}
