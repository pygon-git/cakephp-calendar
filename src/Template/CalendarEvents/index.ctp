<?php
/**
 * @var \App\View\AppView $this
 */
?>
<section class="content-header">
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <h4><?php echo __('Calendar Events'); ?></h4>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="pull-right">
                <div class="btn-group btn-group-sm" role="group">
                    <?php echo $this->element('CsvMigrations.Menu/index_top', ['user' => $user]);?>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="box">
        <div class="box-body">
            <table class="table table-hover table-condensed table-vertical-align table-datatable" width="100%">
                <thead>
                    <tr>
                        <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                        <th scope="col"><?= $this->Paginator->sort('calendar_id') ?></th>
                        <th scope="col"><?= $this->Paginator->sort('event_source_id') ?></th>
                        <th scope="col"><?= $this->Paginator->sort('event_source') ?></th>
                        <th scope="col"><?= $this->Paginator->sort('title') ?></th>
                        <th scope="col"><?= $this->Paginator->sort('start_date') ?></th>
                        <th scope="col"><?= $this->Paginator->sort('end_date') ?></th>
                        <th scope="col"><?= $this->Paginator->sort('duration') ?></th>
                        <th scope="col" class="actions"><?= __('Actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($calendarEvents as $calendarEvent) : ?>
                    <tr>
                        <td><?= h($calendarEvent->id) ?></td>
                        <td><?= $calendarEvent->has('calendar') ? $this->Html->link($calendarEvent->calendar->name, ['controller' => 'Calendars', 'action' => 'view', $calendarEvent->calendar->id]) : '' ?></td>
                        <td><?= h($calendarEvent->event_source_id) ?></td>
                        <td><?= h($calendarEvent->event_source) ?></td>
                        <td><?= h($calendarEvent->title) ?></td>
                        <td><?= h($calendarEvent->start_date) ?></td>
                        <td><?= h($calendarEvent->end_date) ?></td>
                        <td><?= h($calendarEvent->duration) ?></td>
                        <td class="actions">
                            <?= $this->Html->link(__('View'), ['action' => 'view', $calendarEvent->id]) ?>
                            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $calendarEvent->id]) ?>
                            <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $calendarEvent->id], ['confirm' => __('Are you sure you want to delete # {0}?', $calendarEvent->id)]) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
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
</section>
