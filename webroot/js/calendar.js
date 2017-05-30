var calendar = calendar || {};

( function ($) {

    function QoboCalendar(options)
    {
        this.calendarContainer = options.container;
        this.calendarIdContainer = options.calendarIdContainer;
    }

    QoboCalendar.prototype.init = function () {
        var that = this;

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

        $(this.calendarContainer).fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            buttonText: {
                today: 'today',
                month: 'month',
                week: 'week',
                day: 'day'
            },
            editable: false,
            eventClick: function (event) {
                that.loadSelectedCalendarEvent(event);
            },

            dayClick: function (date, jsEvent, view) {
                // FIXME : refactor to generic method.
                drp = $('.calendar-start_date').data('daterangepicker');
                drp2 = $('.calendar-end_date').data('daterangepicker');

                if ('month' === view.intervalUnit) {
                    date.add(9, 'hours');
                }

                drp.setStartDate(date);
                drp.setEndDate(date);

                var end = moment(date);
                end.add(30, 'minutes');

                drp2.setStartDate(end);
                drp2.setEndDate(end);

                $('#calendar-modal-add-event').modal('toggle');
            }
        });

        //checkbox options chosen and load is clicked.
        this.attachCalendarEvents();
    };

    QoboCalendar.prototype.loadSelectedCalendarEvent = function (event) {
        $.ajax({
            method: 'POST',
            url: '/calendars/calendar-events/view',
            data: { 'id' : event.id },
        }).done(function (resp) {
            if (resp) {
                $('#calendar-modal-view-event').find('.modal-content').empty();
                $('#calendar-modal-view-event').find('.modal-content').append(resp);
                $('#calendar-modal-view-event').modal('toggle');
            }
        });
    };

    QoboCalendar.prototype.attachCalendarEvents = function () {
        var that = this;

        // load events based on active calendars on load.
        $.each( $(this.calendarIdContainer), function (index, el) {
            if ($(el).is(':checked')) {
                that.loadSelectedCalendarEvents($(el).val());
            }
        });

        // adding event handler for calendar list checkboxes.
        $(this.calendarIdContainer).on('click', function () {
            var calendarId = $(this).val();

            if ($(this).is(':checked')) {
                that.loadSelectedCalendarEvents(calendarId);
            } else {
                that.unloadSelectedCalendarEvents(calendarId);
            }
        });

        $('.calendar-form-add-event').on('submit', function () {
            that.createEvent($(this).serialize());

            return false;
        });
    };

    QoboCalendar.prototype.createEvent = function (data) {
        var that = this;

        $.ajax({
            method: 'POST',
            dataType: 'json',
            url: '/calendars/calendar-events/add',
            data: data,
            success: function (resp) {
                if (resp.event.entity !== undefined) {
                    var event = {
                        id: resp.event.entity.id,
                        title: resp.event.entity.title,
                        start: moment().format(resp.event.entity.start),
                        color: resp.event.entity.color
                    };

                    $(that.calendarContainer).fullCalendar('addEventSource', [event]);
                }

                $('#calendar-modal-add-event').modal('toggle');
            }
        });
    };

    QoboCalendar.prototype.unloadSelectedCalendarEvents = function (calendarId) {
        var that = this;

        if (!calendarId) {
            return false;
        }

        $.ajax({
            method: 'POST',
            dataType: 'json',
            url: "/calendars/calendars/events",
            data: { 'calendarId': calendarId },
            success: function (resp) {
                if (resp.events.length) {
                    resp.events.forEach(function (item) {
                        $(that.calendarContainer).fullCalendar('removeEvents', item.id);
                    });
                }
            }
        });
    };

    QoboCalendar.prototype.loadSelectedCalendarEvents = function (calendarId) {
        var that = this;

        if (!calendarId) {
            return false;
        }

        $.ajax({
            method: 'POST',
            dataType: 'json',
            url: "/calendars/calendars/events",
            data: { 'calendarId': calendarId },
            success: function (resp) {
                if (resp.events.length) {
                    $(that.calendarContainer).fullCalendar('addEventSource', resp.events);
                }
            }
        });
    };

    calendar = new QoboCalendar({
        container: '#qobrix-calendar',
        calendarIdContainer: '.calendar-id',
    });

    calendar.init();
})(jQuery);
