<?php

use Cake\Core\Configure;
use Cake\Event\EventManager;
use Qobo\Calendar\Events\GetCalendarsListener;


// loading Calendar/Event default types
Configure::load('Qobo/Calendar.types');

EventManager::instance()->on(new GetCalendarsListener());
