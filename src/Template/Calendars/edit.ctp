<?php
/**
 * @var \App\View\AppView $this
 */
?>
<?= $this->Form->create($calendar) ?>
<section class="content-header">
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <h4><?php echo __('Add Calendar'); ?></h4>
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
                    <?= $this->Form->control('name'); ?>
                    <?= $this->Form->control('calendar_source_id', ['type' => 'text']);?>
                    <?= $this->Form->control('calendar_source'); ?>
                </div>
                <div class="col-xs-12 col-md-6">
                    <?= $this->Form->control('color', ['type' => 'select', 'options' => $calendarColors]);?>
                    <?= $this->Form->control('icon');?>
                </div>
            </div>
        </div>
    </div>
    <div>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
