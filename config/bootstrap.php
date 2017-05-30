<?php

use Cake\Core\Configure;
use Cake\Event\EventManager;
use Qobo\Calendar\Events\GetCalendarsListener;
use Qobo\Calendar\Events\GetCalendarEventsListener;

$config = Configure::read('Calendar.Types');

if (empty($config)) {
    Configure::load('Qobo/Calendar.calendar', 'default');
}

EventManager::instance()->on(new GetCalendarsListener());
EventManager::instance()->on(new GetCalendarEventsListener());
