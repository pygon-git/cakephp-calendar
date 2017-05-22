<?php
/**
  * @var \App\View\AppView $this
  */
?>
<section class="content-header">
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <h4>
                <?php echo $this->Html->link('Calendars', ['plugin' => 'Qobo/Calendar', 'controller' => 'calendars', 'action' => 'index']); ?>
                &raquo;
                <?php echo $calendar->name;?>
            </h4>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="pull-right">
                <div class="btn-group btn-group-sm" role="group">
                <?php
					$url = [
                        'plugin' => $this->request->plugin,
                        'controller' => $this->request->controller,
                        'action' => 'edit',
                        $calendar->id
                    ];
                    $menu[] = [
                        'html' => $this->Html->link('<i class="fa fa-pencil"></i> ' . __('Edit'), $url, [
                        'title' => __('Edit'), 'escape' => false, 'class' => 'btn btn-default'
                    ]),
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
            <h3 class="box-title"><?= h($calendar->name);?></h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-xs-4 col-md-2 text-right">
                    <strong>ID:</strong>
                </div>
                <div class="col-xs-8 col-md-4">
                    <?= $calendar->id;?>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    <strong>Color:</strong>
                </div>
                <div class="col-xs-8 col-md-4">
                    <?= $calendar->color; ?>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    <strong>Calendar Source ID:</strong>
                </div>
                <div class="col-xs-8 col-md-4">
                    <?= $calendar->calendar_source_id; ?>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    <strong>Created:</strong>
                </div>
                <div class="col-xs-8 col-md-4">
                    <?= $calendar->created; ?>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    <strong>Name:</strong>
                </div>
                <div class="col-xs-8 col-md-4">
                    <?= $calendar->name; ?>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    <strong>Icon:</strong>
                </div>
                <div class="col-xs-8 col-md-4">
                    <?= $calendar->icon; ?>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    <strong>Calendar Source:</strong>
                </div>
                <div class="col-xs-8 col-md-4">
                    <?= $calendar->calendar_source; ?>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    <strong>Modified:</strong>
                </div>
                <div class="col-xs-8 col-md-4">
                    <?= $calendar->modified; ?>
                </div>
            </div>
        </div>
    </div>
</section>
