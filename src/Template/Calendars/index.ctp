<?php
/**
  * @var \App\View\AppView $this
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Calendar'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Calendar Events'), ['controller' => 'CalendarEvents', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Calendar Event'), ['controller' => 'CalendarEvents', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="calendars index large-9 medium-8 columns content">
    <h3><?= __('Calendars') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('name') ?></th>
                <th scope="col"><?= $this->Paginator->sort('color') ?></th>
                <th scope="col"><?= $this->Paginator->sort('icon') ?></th>
                <th scope="col"><?= $this->Paginator->sort('calendar_source_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('calendar_source') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                <th scope="col"><?= $this->Paginator->sort('trashed') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($calendars as $calendar): ?>
            <tr>
                <td><?= h($calendar->id) ?></td>
                <td><?= h($calendar->name) ?></td>
                <td><?= h($calendar->color) ?></td>
                <td><?= h($calendar->icon) ?></td>
                <td><?= h($calendar->calendar_source_id) ?></td>
                <td><?= h($calendar->calendar_source) ?></td>
                <td><?= h($calendar->created) ?></td>
                <td><?= h($calendar->modified) ?></td>
                <td><?= h($calendar->trashed) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $calendar->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $calendar->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $calendar->id], ['confirm' => __('Are you sure you want to delete # {0}?', $calendar->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
    </div>
</div>
