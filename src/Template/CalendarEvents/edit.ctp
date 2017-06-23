<?php
/**
 * @var \App\View\AppView $this
 */
echo $this->Html->css(
    [
        'AdminLTE./plugins/daterangepicker/daterangepicker-bs3',
    ]
);

echo $this->Html->script(
    [
        'AdminLTE./plugins/daterangepicker/moment.min',
        'AdminLTE./plugins/fullcalendar/fullcalendar.min.js',
        'AdminLTE./plugins/daterangepicker/daterangepicker',
        'Qobo/Calendar.calendar.misc',
    ],
    ['block' => 'scriptBotton']
);

?>
<?= $this->Form->create($calendarEvent) ?>
<section class="content-header">
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <h4><?php echo __('Add Calendar Event'); ?></h4>
        </div>
        <div class="col-xs-12 col-md-6"></div>
    </div>
</section>
<div class="content">
    <div class='box box-primary'>
        <div class="box-header with-border">
            <h3 class="box-title"><?= __('Calendar Details');?></h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <?php
                        echo $this->Form->control('title');
                        echo $this->Form->control('calendar_id', ['options' => $calendars]);
                    ?>
                </div>
                <div class="col-xs-12 col-md-6">
                <?php
                    echo $this->Form->input('start_date', ['value' => $calendarEvent->start_date->format('Y-m-d H:i'), 'type' => 'text', 'class' => 'calendar-datetimepicker calendar-start_date']);
                    echo $this->Form->input('end_date', ['type' => 'text', 'value' => $calendarEvent->end_date->format('Y-m-d H:i'), 'class' => 'calendar-datetimepicker calendar-end_date']);
                ?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-6">
                <?php echo $this->Form->control('content'); ?>
                </div>
            </div>
        </div>
    </div>
    <div>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
