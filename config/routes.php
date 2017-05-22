<?php
use Cake\Routing\Router;

Router::plugin(
    'Qobo/Calendar',
    ['path' => '/calendars'],
    function ($routes) {
        $routes->fallbacks('DashedRoute');
    }
);
