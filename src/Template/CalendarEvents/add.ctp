<?php
/**
 * @var \App\View\AppView $this
 */
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
                        echo $this->Form->control('event_source_id', ['type' => 'text']);
                        echo $this->Form->control('event_source');
                    ?>
                </div>
                <div class="col-xs-12 col-md-6">
                <?php
                    echo $this->Form->control('start_date', ['empty' => true]);
                    echo $this->Form->control('end_date', ['empty' => true]);
                    echo $this->Form->control('duration', ['empty' => true]);
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
