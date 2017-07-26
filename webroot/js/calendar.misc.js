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

    $('.calendar-dyn-attendees').select2({
        theme: 'bootstrap',
        width: '100%',
        multiple: true,
        placeholder: '-- Please choose --',
        allowClear: true,
        minimumInputLength: 3,
        ajax: {
            url: '/calendars/calendar-attendees/lookup',
            dataType: 'json',
            method: 'get',
            cache: false, // @TODO: change to true
            contentType: 'application/json',
            accepts: {
                json: 'application/json',
            },
            delay: 300,
            data: function (params) {
                return {
                    term: params.term,
                    calendar_id: $('.calendar-dyn-calendar-type').val(),
                    event_type: $('.calendar-dyn-event-type').val()
                };
            },
            processResults: function (data, params) {
                return {
                    results: data
                };
            }
        }
    });

    var eventTypeSelect = $('.calendar-dyn-event-type').select2({
        theme: 'bootstrap',
        width: '100%',
        placeholder: '-- Please choose --',
    });

    $('.calendar-dyn-event-type').on('change', function (evt) {
        var eventData = null;
        var current = $(this).val();

        if (!eventTypes.length) {
            return;
        }

        $.each(eventTypes, function (key, elem) {
            if (elem.value == current) {
                eventData = elem;
            }
        });

        if (eventData) {
            if (eventData.exclude_fields.length) {
                eventData.exclude_fields.forEach(function (field_class, key) {
                    $('#calendar-modal-add-event').find('.' + field_class).hide();
                });
            }
        } else {
            $('#calendar-modal-add-event').find('div:hidden').show();
        }

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
                if (parseInt(momentEnd.format('H')) < parseInt(momentStart.format('H'))) {
                    momentEnd.add(1, 'days');
                } else {
                    momentEnd.date(momentStart.format('D'));
                    startPicker.setStartDate(momentStart);
                    startPicker.setEndDate(momentStart);
                }
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
                    result.forEach(function (elem) {
                        opts.push({id: elem.value, text: elem.name});
                    });
                }

                $('#calendar-modal-add-event').find('div:hidden').show();
                eventTypeSelect.select2().empty();

                eventTypeSelect.select2({
                    theme: 'bootstrap',
                    width: '100%',
                    placeholder: '-- Please choose --',
                    data: opts
                });

                //@NOTE: triggering change to update start/end intervals
                eventTypeSelect.trigger('change');
            }
        });
    });

    $('.event-add-row').on('click', function () {
        var rowContainer = '.' + $(this).data('target');

        var row = $(rowContainer).find('.row:first');
        var clone = row.clone();

        $(rowContainer).append(clone);

        $(rowContainer).find('.row').each(function (key, row) {
             $(row).find('input').each(function (k, elem) {
                var fieldName = $(elem).attr('name');
                var newFieldName = fieldName.replace(/^(\w+\[)\d+(\]\[\w+\])$/, "$1" + key + "$2");
                $(elem).attr('name', newFieldName);
             });
        });
    });

    $('#rows-collection').on('click', '.event-remove-row', function () {
        var rowContainer = '.' + $(this).data('target');
        var row = $(this).parent().parent().parent();

        if ($(rowContainer).find('.row').length > 1) {
            $(row).remove();
        }

        $(rowContainer).find('.row').each(function (key, row) {
             $(row).find('input').each(function (k, elem) {
                var fieldName = $(elem).attr('name');
                var newFieldName = fieldName.replace(/^(\w+\[)\d+(\]\[\w+\])$/, "$1" + key + "$2");
                $(elem).attr('name', newFieldName);
             });
        });

    });
});
