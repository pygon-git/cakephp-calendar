<?php
echo $this->Html->css(
    [
        'AdminLTE./plugins/fullcalendar/fullcalendar.min.css',
        'AdminLTE./plugins/daterangepicker/daterangepicker-bs3',
    ]
);

echo $this->Html->script(
    [
        'AdminLTE./plugins/daterangepicker/moment.min',
        'AdminLTE./plugins/fullcalendar/fullcalendar.min.js',
        'AdminLTE./plugins/daterangepicker/daterangepicker',
        'Qobo/Calendar.calendar',
    ],
    ['block' => 'scriptBotton']
);

$options = [
    '123' => 'Calendar 1',
    '456' => 'Calendar 2',
];
?>
<section class="content-header">
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <h4><?php echo __('Calendars'); ?></h4>
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

    <div class="row">
       <div class="col-md-4">
            <div class='box'>
                <div class='box-header with-border'>
                    <h3 class='box-title'><?= __('Calendars');?></h3>
                </div>
                <div class='box-body'>
                    <div class="row">
                        <div class="col-md-12">
                            <?php foreach ($calendars as $calendar) : ?>
                                <?php echo $this->Form->input('Calendar._ids', [
                                    'id' => false,
                                    'type' => 'checkbox',
                                    'multiple' => true,
                                    'value' => $calendar->id,
                                    'label' => $calendar->name,
                                    'class' => 'calendar-id',
                                    'hiddenField' => false,
                                ]);?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="box">
                <div class='box-body'>
                    <div id="qobrix-calendar"></div>
                </div>
            </div>
        </div>

        <?php
            //add event modal form
            echo $this->element('Qobo/Calendar.add_calendar_event');
            //view details dialog
            echo $this->element('Qobo/Calendar.view_calendar_event');
        ?>
    </div> <!-- //end first row -->

    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <table class="table table-hover table-condensed table-vertical-align table-datatable" width="100%">
                        <thead>
                            <tr>
                                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                                <th scope="col"><?= $this->Paginator->sort('name') ?></th>
                                <th scope="col"><?= $this->Paginator->sort('color') ?></th>
                                <th scope="col"><?= $this->Paginator->sort('icon') ?></th>
                                <th scope="col"><?= $this->Paginator->sort('calendar_source_id') ?></th>
                                <th scope="col"><?= $this->Paginator->sort('calendar_source') ?></th>
                                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                                <th scope="col" class="actions"><?= __('Actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($calendars as $calendar) : ?>
                            <tr>
                                <td><?= h($calendar->id) ?></td>
                                <td><?= h($calendar->name) ?></td>
                                <td><?= h($calendar->color) ?></td>
                                <td><?= h($calendar->icon) ?></td>
                                <td><?= h($calendar->calendar_source_id) ?></td>
                                <td><?= h($calendar->calendar_source) ?></td>
                                <td><?= h($calendar->created) ?></td>
                                <td class="actions">
                                    <?= $this->Html->link(__('View'), ['action' => 'view', $calendar->id]) ?>
                                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $calendar->id]) ?>
                                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $calendar->id], ['confirm' => __('Are you sure you want to delete # {0}?', $calendar->id)]) ?>
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
    </div>
</section>
