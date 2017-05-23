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
                    <div class="col-xs-12">
                        <?= $this->Form->input('CalendarEvents.calendar_id', ['type' => 'select', 'options' => [], 'empty' => 'Choose Calendar']);?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <?= $this->Form->input('CalendarEvents.start_date', ['type' => 'text', 'class' => 'calendar-datetimepicker']);?>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <?= $this->Form->input('CalendarEvents.end_date', ['type' => 'text', 'class' => 'calendar-datetimepicker']);?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <?= $this->Form->input('CalendarEvents.title', ['type' => 'text']);?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <?= $this->Form->input('CalendarEvents.content', ['type' => 'textarea']);?>
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
