<?php
$color = (!empty($calEvent->calendar->color) ? $calEvent->calendar->color : null);
$icon = (!empty($calEvent->calendar->icon) ? $calEvent->calendar->icon : null);

$title = (!empty($icon) ? "<i class='fa fa-$icon'></i>&nbsp;": '');
$title .= $calEvent->title;

if (!empty($color)) {
    $title = '<div style="color: ' . $color . '">' . $title . '</div>';
}

$attendeesList = [];

if (!empty($calEvent->calendar_attendees)) {
    foreach ($calEvent->calendar_attendees as $attendee) {
        $attendeesList[] = $attendee->display_name;
    }
}

?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="calendar-modal-label"><?= $title;?></h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-xs-12">
            <?= $calEvent->content;?>
        </div>
    </div>
    <hr/>
    <div class="row">
        <div class="col-xs-12">
            <strong>When:</strong>
        </div>
        <div class="col-xs-12">
            <?= $calEvent->start_date->format('Y-m-d H:i'); ?> &#8212; <?= $calEvent->end_date->format('Y-m-d H:i'); ?>
        </div>
    </div>
    <?php if (!empty($calEvent->calendar_attendees)) : ?>
        <div class="row">
            <div class="col-xs-12">
                <strong>Attendees:</strong> <?= implode(', ', $attendeesList);?>
            </div>
        </div>
    <?php endif; ?>
</div>
    <div class="modal-footer">
        <?= $this->Form->button(__('Close'), ['data-dismiss' => 'modal', 'class' => 'btn btn-success']);?>
    </div> <!-- //modal-footer -->
</div>

