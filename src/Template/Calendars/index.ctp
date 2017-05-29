<?php
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
        'AdminLTE./plugins/daterangepicker/moment.min',
        'AdminLTE./plugins/fullcalendar/fullcalendar.min.js',
        'AdminLTE./plugins/daterangepicker/daterangepicker',
        'AdminLTE./plugins/select2/select2.min',
        'Qobo/Calendar.calendar',
        'Qobo/Calendar.calendar.misc',
    ],
    ['block' => 'scriptBotton']
);
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
                                <div class="row">
                                    <div class="col-xs-8">
                                    <?php
                                        $label = (!empty($calendar->icon) ? "<i class='fa fa-{$calendar->icon}'></i>&nbsp;&nbsp;" . $calendar->name : $calendar->name);
                                        echo $this->Form->input('Calendar._ids', [
                                            'id' => false,
                                            'type' => 'checkbox',
                                            'multiple' => true,
                                            'value' => $calendar->id,
                                            'class' => 'calendar-id',
                                            'hiddenField' => false,
                                            'label' => $label,
                                            'escape' => false,
                                            'checked' => $calendar->active,
                                        ]);?>
                                    </div>
                                    <div class="col-xs-4">
                                        <div class="btn-group btn-group-xs pull-right">
                                            <?php
                                                echo $this->Html->link(
                                                    '<i class="fa fa-eye"></i>',
                                                    [
                                                        'plugin' => 'Qobo/Calendar',
                                                        'controller' => 'Calendars',
                                                        'action' => 'view',
                                                        $calendar->id,
                                                    ],
                                                    [
                                                        'class' => 'btn btn-default',
                                                        'escape' => false
                                                    ]
                                                );
                                                echo $this->Html->link(
                                                    '<i class="fa fa-pencil"></i>',
                                                    [
                                                        'plugin' => 'Qobo/Calendar',
                                                        'controller' => 'Calendars',
                                                        'action' => 'edit',
                                                        $calendar->id,
                                                    ],
                                                    [
                                                        'class' => 'btn btn-default',
                                                        'escape' => false
                                                    ]
                                                );
                                                echo $this->Form->postLink(
                                                    '<i class="fa fa-trash"></i>',
                                                    [
                                                        'plugin' => 'Qobo/Calendar',
                                                        'controller' => 'Calendars',
                                                        'action' => 'delete',
                                                        $calendar->id,
                                                    ],
                                                    [
                                                        'class' => 'btn btn-default',
                                                        'escape' => false,
                                                        'confirm' => __('Are you sure you want to delete calendar "{0}"?', $calendar->name),
                                                    ]
                                                );?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
<!--
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Organizer</h3>
                </div>
                <div class='box-body'>

                </div>
            </div>
-->
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
            echo $this->element('Qobo/Calendar.add_calendar_event', ['calendars' => $calendars]);
            //view details dialog
            echo $this->element('Qobo/Calendar.view_calendar_event');
        ?>
    </div> <!-- //end first row -->
</section>
