<?php

use Cake\Core\Configure;
use Cake\Event\EventManager;
use Qobo\Calendar\Events\GetCalendarsListener;
use Qobo\Calendar\Events\GetCalendarEventsListener;

// loading Calendar/Event default types
Configure::load('Qobo/Calendar.types');

EventManager::instance()->on(new GetCalendarsListener());
EventManager::instance()->on(new GetCalendarEventsListener());
