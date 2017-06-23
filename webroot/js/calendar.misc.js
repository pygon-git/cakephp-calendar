$(document).ready(function () {
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
