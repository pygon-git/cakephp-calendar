<?php
/**
  * @var \App\View\AppView $this
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Calendar Event'), ['action' => 'edit', $calendarEvent->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Calendar Event'), ['action' => 'delete', $calendarEvent->id], ['confirm' => __('Are you sure you want to delete # {0}?', $calendarEvent->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Calendar Events'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Calendar Event'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Calendars'), ['controller' => 'Calendars', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Calendar'), ['controller' => 'Calendars', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="calendarEvents view large-9 medium-8 columns content">
    <h3><?= h($calendarEvent->title) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= h($calendarEvent->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Calendar') ?></th>
            <td><?= $calendarEvent->has('calendar') ? $this->Html->link($calendarEvent->calendar->name, ['controller' => 'Calendars', 'action' => 'view', $calendarEvent->calendar->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Event Source Id') ?></th>
            <td><?= h($calendarEvent->event_source_id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Event Source') ?></th>
            <td><?= h($calendarEvent->event_source) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Title') ?></th>
            <td><?= h($calendarEvent->title) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Start Date') ?></th>
            <td><?= h($calendarEvent->start_date) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('End Date') ?></th>
            <td><?= h($calendarEvent->end_date) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Duration') ?></th>
            <td><?= h($calendarEvent->duration) ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Content') ?></h4>
        <?= $this->Text->autoParagraph(h($calendarEvent->content)); ?>
    </div>
</div>
