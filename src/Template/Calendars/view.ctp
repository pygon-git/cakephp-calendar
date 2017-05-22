<?php
/**
  * @var \App\View\AppView $this
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Calendar'), ['action' => 'edit', $calendar->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Calendar'), ['action' => 'delete', $calendar->id], ['confirm' => __('Are you sure you want to delete # {0}?', $calendar->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Calendars'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Calendar'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Calendar Events'), ['controller' => 'CalendarEvents', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Calendar Event'), ['controller' => 'CalendarEvents', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="calendars view large-9 medium-8 columns content">
    <h3><?= h($calendar->name) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= h($calendar->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Name') ?></th>
            <td><?= h($calendar->name) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Color') ?></th>
            <td><?= h($calendar->color) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Icon') ?></th>
            <td><?= h($calendar->icon) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Calendar Source Id') ?></th>
            <td><?= h($calendar->calendar_source_id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Calendar Source') ?></th>
            <td><?= h($calendar->calendar_source) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($calendar->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($calendar->modified) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Trashed') ?></th>
            <td><?= h($calendar->trashed) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Calendar Events') ?></h4>
        <?php if (!empty($calendar->calendar_events)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Calendar Id') ?></th>
                <th scope="col"><?= __('Event Source Id') ?></th>
                <th scope="col"><?= __('Event Source') ?></th>
                <th scope="col"><?= __('Title') ?></th>
                <th scope="col"><?= __('Content') ?></th>
                <th scope="col"><?= __('Start Date') ?></th>
                <th scope="col"><?= __('End Date') ?></th>
                <th scope="col"><?= __('Duration') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($calendar->calendar_events as $calendarEvents): ?>
            <tr>
                <td><?= h($calendarEvents->id) ?></td>
                <td><?= h($calendarEvents->calendar_id) ?></td>
                <td><?= h($calendarEvents->event_source_id) ?></td>
                <td><?= h($calendarEvents->event_source) ?></td>
                <td><?= h($calendarEvents->title) ?></td>
                <td><?= h($calendarEvents->content) ?></td>
                <td><?= h($calendarEvents->start_date) ?></td>
                <td><?= h($calendarEvents->end_date) ?></td>
                <td><?= h($calendarEvents->duration) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'CalendarEvents', 'action' => 'view', $calendarEvents->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'CalendarEvents', 'action' => 'edit', $calendarEvents->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'CalendarEvents', 'action' => 'delete', $calendarEvents->id], ['confirm' => __('Are you sure you want to delete # {0}?', $calendarEvents->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
