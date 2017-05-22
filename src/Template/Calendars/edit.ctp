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
                ['action' => 'delete', $calendar->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $calendar->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Calendars'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Calendar Events'), ['controller' => 'CalendarEvents', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Calendar Event'), ['controller' => 'CalendarEvents', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="calendars form large-9 medium-8 columns content">
    <?= $this->Form->create($calendar) ?>
    <fieldset>
        <legend><?= __('Edit Calendar') ?></legend>
        <?php
            echo $this->Form->control('name');
            echo $this->Form->control('color');
            echo $this->Form->control('icon');
            echo $this->Form->control('calendar_source_id');
            echo $this->Form->control('calendar_source');
            echo $this->Form->control('trashed');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
