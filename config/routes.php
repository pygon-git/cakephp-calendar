<?php
use Cake\Routing\Router;

Router::plugin(
    'Calendar',
    ['path' => '/calendars'],
    function ($routes) {
        $routes->fallbacks('DashedRoute');
    }
);
