<?php
/**
 * @var \App\View\AppView $this
 */
?>
<section class="content-header">
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <h4>
                <?php echo $this->Html->link('Calendar Evens', ['plugin' => 'Qobo/Calendar', 'controller' => 'CalendarEvents', 'action' => 'index']); ?>
                &raquo;
                <?php echo $calendarEvent->title;?>
            </h4>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="pull-right">
                <div class="btn-group btn-group-sm" role="group">
                <?php $url = [
                        'plugin' => $this->request->plugin,
                        'controller' => $this->request->controller,
                        'action' => 'edit',
                        $calendarEvent->id
                    ];
                    $menu[] = [
                        'html' => $this->Html->link(
                            '<i class="fa fa-pencil"></i> ' . __('Edit'),
                            $url,
                            [
                                'title' => __('Edit'), 'escape' => false, 'class' => 'btn btn-default'
                            ]
                        ),
                        'url' => $url
                    ];

                    foreach ($menu as $item) {
                        echo $item['html'];
                    }
                ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title"><?= h($calendarEvent->title); ?></h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-xs-4 col-md-2 text-right">
                    <strong>ID: </strong>
                </div>
                <div class="col-xs-8 col-md-4">
                    <?= $calendarEvent->id;?>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    <strong>Calendar:</strong>
                </div>
                <div class="col-xs-8 col-md-4">
                    <?= $calendarEvent->has('calendar') ? $this->Html->link($calendarEvent->calendar->name, ['controller' => 'Calendars', 'action' => 'view', $calendarEvent->calendar->id]) : '' ?>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    <strong>Event Source ID:</strong>
                </div>
                <div class="col-xs-8 col-md-4">
                    <?= $calendarEvent->event_source_id;?>
                </div>

                <div class="col-xs-4 col-md-2 text-right">
                    <strong>Calendar Event Source:</strong>
                </div>
                <div class="col-xs-8 col-md-4">
                    <?= $calendarEvent->event_source;?>
                </div>
                <div class="col-xs-4 col-md-2 text-right"><strong>Start Date:</strong></div>
                <div class="col-xs-8 col-md-4"><?= $calendarEvent->start_date;?></div>

                <div class="col-xs-4 col-md-2 text-right"><strong>End Date:</strong></div>
                <div class="col-xs-8 col-md-4"><?= $calendarEvent->end_date;?></div>

                <div class="col-xs-4 col-md-2 text-right"><strong>Duration:</strong></div>
                <div class="col-xs-8 col-md-4"><?= $calendarEvent->duration;?></div>
            </div>
            <div class="row">
                <div class="col-xs-4 col-md-2 text-right"><strong>Title: </strong></div>
                <div class="col-xs-8 col-md-4"><?= $calendarEvent->title;?></div>

                <div class="col-xs-4 col-md-2 text-right"><strong>Content:</strong></div>
                <div class="col-xs-8 col-md-4">
                    <?= $this->Text->autoParagraph(h($calendarEvent->content)); ?>
                </div>
            </div>
        </div>
    </div>
</section>
