# CakePHP-Calendar Plugin

[![Build Status](https://travis-ci.org/QoboLtd/cakephp-calendar.svg?branch=master)](https://travis-ci.org/QoboLtd/cakephp-calendar)
[![Latest Stable Version](https://poser.pugx.org/qobo/cakephp-calendar/v/stable)](https://packagist.org/packages/qobo/cakephp-calendar)
[![Total Downloads](https://poser.pugx.org/qobo/cakephp-calendar/downloads)](https://packagist.org/packages/qobo/cakephp-calendar)
[![Latest Unstable Version](https://poser.pugx.org/qobo/cakephp-calendar/v/unstable)](https://packagist.org/packages/qobo/cakephp-calendar)
[![License](https://poser.pugx.org/qobo/cakephp-calendar/license)](https://packagist.org/packages/qobo/cakephp-calendar)
[![codecov](https://codecov.io/gh/QoboLtd/cakephp-calendar/branch/master/graph/badge.svg)](https://codecov.io/gh/QoboLtd/cakephp-calendar)

CakePHP-Calendar Plugin
=======================

CakePHP 3 plugin that uses FullCalendar JS (as part of AdminLTE) to manage calendar events and attendees.

Some of the things that we'll be adding shortly:
- [x] Calendar Attendees search via auto-complete (using Select2 checkbox).
- [x] Recurring calendar events. 
- [x] Prototyping calendar attendees.
- [ ] Full re-write of jQuery to VueJS components.
- [ ] FreeBusy Calendar implementation.


### Note

The plugin is under development, so any **Bugs** and **pull requests** are more than welcomed.

### Plugin installation

``` composer require qobo/cakephp-calendar ```

Don't forget to include it in the application. In `config/bootstrap.php`:

```php
# Optionally adding AdminLTE and Qobo Utils that are partially used inside.
Plugin::load('AdminLTE', ['bootstrap' => true, 'routes' => true]);
Plugin::load('Qobo/Utils');
Plugin::load('Qobo/Calendar', ['bootstrap' => true, 'routes' => true]);
```

### Database Schema

Add missing database tables that will hold calendars/events/attendees in your app.

```./bin/cake migrations migrate --plugin Qobo/Calendar``` 


### JavaScript and Styling.

The plugin heavily rely on AdminLTE, Bootstrap for styling, so you should make some adjustments in `src/Template/Calendars/index.ctp` in order to get it running.

```php
<?php
// 'scriptBotton' is an AdminLTE typo that I kept ;(

echo $this->Html->css(
    [
        'AdminLTE./plugins/fullcalendar/fullcalendar.min.css',
        'AdminLTE./plugins/daterangepicker/daterangepicker-bs3',
        'AdminLTE./plugins/select2/select2.min',
        'Qobo/Utils.select2-bootstrap.min',
        'Qobo/Utils.select2-style',
    ]
);

echo $this->Html->script(
    [
        'AdminLTE./plugins/jQuery/jQuery-2.1.4.min', // in case you didn't include it in the layout
        'AdminLTE./bootstrap/js/bootstrap',          // should be include in your layout.
        'AdminLTE./plugins/daterangepicker/moment.min',
        'AdminLTE./plugins/fullcalendar/fullcalendar.min.js',
        'AdminLTE./plugins/daterangepicker/daterangepicker',
        'AdminLTE./plugins/select2/select2.min',
    ],
    ['block' => 'scriptBotton']
);

echo $this->Html->script(
    [
        'Qobo/Calendar.calendar.misc',
        'Qobo/Calendar.vue.min',
        'Qobo/Calendar.calendar.js',
    ],
    ['block' => 'scriptBotton']
);
?>
```
JavaScript files should go to your footer, so you can use native cake `fetch('scriptBottom')` and replace `scriptBotton` with `scriptBottom` in the index template (assuming that you have `$this->fetch('scriptBottom');` in your layout footer.
