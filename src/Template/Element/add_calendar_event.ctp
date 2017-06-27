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
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="calendar-modal-label">Add Event</h4>
            </div> <!-- //modal-header -->
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <?= $this->Form->input('CalendarEvents.calendar_id', ['type' => 'select', 'class' => 'calendar-dyn-calendar-type', 'options' => $calendarOptions, 'empty' => 'Choose Calendar']);?>
                        <?= $this->Form->input('CalendarEvents.event_type', ['type' => 'select', 'class' => 'calendar-dyn-event-type', 'options' => [], 'empty' => 'Choose Event Type']);?>
                        <div class="row">
                            <div class="col-xs-12 col-md-6">
                                <?= $this->Form->input('CalendarEvents.start_date', ['type' => 'text', 'class' => 'calendar-datetimepicker calendar-start_date']);?>
                            </div>
                            <div class="col-xs-12 col-md-6">
                                <?= $this->Form->input('CalendarEvents.end_date', ['type' => 'text', 'class' => 'calendar-datetimepicker calendar-end_date']);?>
                            </div>
                        </div>
                        <?= $this->Form->input('CalendarEvents.title', ['type' => 'text']);?>
                        <?= $this->Form->input('CalendarEvents.content', ['type' => 'textarea']);?>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="#" class="btn btn-default event-add-row" data-target="attendees-container"><i class="fa fa-plus"></i> Add Attendee</a>
                        </div>
                        <hr/>
                        <div class="attendees-container">
                            <div class="row">
                                <div class="col-xs-5 col-md-4">
                                    <?= $this->Form->control('calendar_attendees.0.display_name', ['type' => 'text']);?>
                                </div>
                                <div class="col-xs-5 col-md-6">
                                    <?= $this->Form->control('calendar_attendees.0.contact_details', ['type' => 'text']);?>
                                </div>
                                <div class="col-xs-2 col-md-2">
                                    <div class="form-group">
                                        <label> &nbsp;</label>
                                        <a href="#" class="btn btn-default form-control event-remove-row" data-target="attendees-container"><i class="fa fa-minus"></i></a>
                                    </div>
                                </div>
                            </div>
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
