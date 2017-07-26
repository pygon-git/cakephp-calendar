<?php
$calendarOptions = [];
if (!empty($calendars)) {
    foreach ($calendars as $calendar) {
        $calendarOptions[$calendar->id] = $calendar->name;
    }
}
?>
<div class="modal fade" id="calendar-modal-add-event" tabindex="-1" role="dialog" aria-labelledby="calendar-modal-label">
    <?= $this->Form->create('CalendarEvents', ['url' => false, 'class' => 'calendar-form-add-event']);?>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="calendar-modal-label">Add Event</h4>
            </div> <!-- //modal-header -->
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <?= $this->Form->control('CalendarEvents.calendar_id', ['type' => 'select', 'class' => 'calendar-dyn-calendar-type', 'options' => $calendarOptions, 'empty' => 'Choose Calendar']);?>
                        <?= $this->Form->control('CalendarEvents.event_type', ['type' => 'select', 'class' => 'calendar-dyn-event-type', 'options' => [], 'empty' => 'Choose Event Type']);?>
                        <div class="row">
                            <div class="col-xs-12 col-md-12">
                                <?= $this->Form->control('CalendarEvents.start_date', ['type' => 'text', 'class' => 'calendar-datetimepicker calendar-start_date']);?>
                            </div>
                            <div class="col-xs-12 col-md-12">
                                <?= $this->Form->control('CalendarEvents.end_date', ['type' => 'text', 'class' => 'calendar-datetimepicker calendar-end_date']);?>
                            </div>
                            <div class="col-xs-12 col-md-12">
                                <?= $this->Form->control('CalendarAttendees.contact_details', ['type' => 'select', 'multiple' => 'multiple', 'class' => 'calendar-dyn-attendees']);?>
                            </div>
                        </div>
                        <div class="calendar-title">
                            <?= $this->Form->control('CalendarEvents.title', ['type' => 'text']);?>
                        </div>
                        <div class="calendar-content">
                            <?= $this->Form->control('CalendarEvents.content', ['type' => 'textarea']);?>
                        </div>
                    </div>
                </div>
            </div> <!-- //modal-body -->
            <div class="modal-footer">
                <?= $this->Form->button(__('Submit'), ['type' => 'submit', 'class' => 'btn btn-default']);?>
                <?= $this->Form->button(__('Close'), ['data-dismiss' => 'modal', 'class' => 'btn btn-default']);?>
            </div> <!-- //modal-footer -->
        </div> <!-- //modal-content -->
    </div> <!-- // modal-dialog -->
    <?= $this->Form->end();?>
</div>
