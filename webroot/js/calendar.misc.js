$(document).ready(function () {
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

    $('.calendar-dyn-calendar-type').on('change', function(evt) {
        calendarId = $(this).val();
        $.ajax({
            dataType: 'json',
            method: 'POST',
            data: { id : calendarId },
            url: '/calendars/calendar-events/get-event-types',
            success: function(resp) {
                eventTypeSelect.select2().empty();

                eventTypeSelect.select2({
                    theme: 'bootstrap',
                    width: '100%',
                    placeholder: '-- Please choose --',
                    data: resp.eventTypes
                });
            }
        });
    });
});
