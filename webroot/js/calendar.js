var calendar = calendar || {};

( function ($) {

    function QoboCalendar(options)
    {
        this.calendarContainer = options.container;
        this.calendarIdContainer = options.calendarIdContainer;
    }

    QoboCalendar.prototype.init = function () {
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
            eventClick: function (calEvent, jsEvent, view) {
                console.log('modal pops up with Event Details');
                console.log(calEvent);
                $('#calendar-modal-view-event').modal('toggle');
            },
            dayClick: function (date, jsEvent, view) {
                console.log('day click clack');
                console.log('adding event pops up');
                console.log(date);
                $('#calendar-modal-add-event').modal('toggle');
            }
        });

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

        //checkbox options chosen and load is clicked.
        this.attachCalendarEvents();
    };

    QoboCalendar.prototype.attachCalendarEvents = function () {
        var that = this;

        // adding event handler for calendar list checkboxes.
        $(this.calendarIdContainer).on('click', function () {
            var calendarId = $(this).val();

            if ($(this).is(':checked')) {
                that.loadSelectedCalendarEvents(calendarId);
            } else {
                that.unloadSelectedCalendarEvents(calendarId);
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
            url: "/calendars/calendars/get-events",
            data: { 'calendarId': calendarId },
            success: function (resp) {
                if (resp.events.length) {
                    resp.events.forEach(function (item) {
                        $(that.calendarContainer).fullCalendar('removeEvents', item.id);
                    });
                }
            }
        });

    }

    QoboCalendar.prototype.loadSelectedCalendarEvents = function (calendarId) {
        var that = this;

        if (!calendarId) {
            return false;
        }

        $.ajax({
            method: 'POST',
            dataType: 'json',
            url: "/calendars/calendars/get-events",
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

