<?php

use Cake\Core\Configure;
use Cake\Event\EventManager;
use Qobo\Calendar\Events\GetCalendarsListener;

$config = Configure::read('Calendar.Types');

if (empty($config)) {
    Configure::load('Qobo/Calendar.calendar', 'default');
}

EventManager::instance()->on(new GetCalendarsListener());
