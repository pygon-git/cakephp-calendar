$(document).ready(function () {
    var eventTypes = [];

    // date range picker (used for datetime pickers)
    $('.calendar-datetimepicker').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        timePicker: true,
        drops: "down",
        timePicker12Hour: false,
        timePickerIncrement: 5,
        format: "YYYY-MM-DD HH:mm",
    });


    $('.calendar-dyn-calendar-type').select2({
        theme: 'bootstrap',
        width: '100%',
        placeholder: '-- Please choose --'
    });

    var eventTypeSelect = $('.calendar-dyn-event-type').select2({
        theme: 'bootstrap',
        width: '100%',
        placeholder: '-- Please choose --'
    });

    $('.calendar-dyn-event-type').on('change', function (evt) {
        var eventData = null;
        var current = $(this).val();
        if(!eventTypes.length) {
            return;
        }
        $.each(eventTypes, function(key, elem) {
            if (elem.value == current) {
                eventData = elem;
            }
        });

        if (eventData) {
            startPicker = $('.calendar-start_date').data('daterangepicker');
            endPicker = $('.calendar-end_date').data('daterangepicker');

            momentStart = startPicker.startDate;
            momentEnd = endPicker.startDate;

            if (eventData.start_time) {
                hhmm = eventData.start_time.split(':');
                momentStart.set('hour', hhmm[0]);
                momentStart.set('minute', hhmm[1]);
                startPicker.setStartDate(momentStart);
                startPicker.setEndDate(momentStart);
            }

            if (eventData.end_time) {
                hhmm = eventData.end_time.split(':');
                momentEnd.set('hour', hhmm[0]);
                momentEnd.set('minute', hhmm[1]);
                endPicker.setStartDate(momentEnd);
                endPicker.setEndDate(momentEnd);
            }
        }
    });

    $('.calendar-dyn-calendar-type').on('change', function (evt) {
        calendarId = $(this).val();
        $.ajax({
            dataType: 'json',
            method: 'POST',
            data: { id : calendarId },
            url: '/calendars/calendar-events/get-event-types',
            success: function (result) {
                var opts = [];

                if (result) {
                    eventTypes = result;
                    result.forEach(function(elem){
                        opts.push({id: elem.value, text: elem.name});
                    });
                }

                eventTypeSelect.select2().empty();

                eventTypeSelect.select2({
                    theme: 'bootstrap',
                    width: '100%',
                    placeholder: '-- Please choose --',
                    data: opts
                });

            }
        });
    });
});
