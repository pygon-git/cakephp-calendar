<?php
/**
 * @var \App\View\AppView $this
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $calendarEvent->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $calendarEvent->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Calendar Events'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Calendars'), ['controller' => 'Calendars', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Calendar'), ['controller' => 'Calendars', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="calendarEvents form large-9 medium-8 columns content">
    <?= $this->Form->create($calendarEvent) ?>
    <fieldset>
        <legend><?= __('Edit Calendar Event') ?></legend>
        <?php
            echo $this->Form->control('calendar_id', ['options' => $calendars]);
            echo $this->Form->control('event_source_id');
            echo $this->Form->control('event_source');
            echo $this->Form->control('title');
            echo $this->Form->control('content');
            echo $this->Form->control('start_date', ['empty' => true]);
            echo $this->Form->control('end_date', ['empty' => true]);
            echo $this->Form->control('duration', ['empty' => true]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
