// @codingStandardsIgnoreStart
Vue.component('calendar', {
    template: '<div></div>',

    props: {
        editable: {
            type: Boolean,
            required: false,
            default: false
        }
    },

    data: function() {
        return {
            cal: null
        };
    },

    mounted: function() {
        var self = this;
        self.cal = $(self.$el);

        var args = {
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
            events: [],
            dayClick: function(date, jsEvent, view) {
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
            },
            eventClick: function(event) {
                self.getEvent(event);
            }
        }; //end of args

        this.cal.fullCalendar(args);

        // load events based on active calendars on load.
        $.each($('.calendar-id'), function (index, el) {
            if ($(el).is(':checked')) {
                self.getEvents($(el).val());
            }
        });

        // adding event handler for calendar list checkboxes.
        // @FIXME: move to calendar-list component
        $('.calendar-id').on('click', function () {
            var calendarId = $(this).val();

            if ($(this).is(':checked')) {
                self.getEvents(calendarId);
            } else {
                self.removeEvents(calendarId);
            }
        });

        $('.calendar-form-add-event').on('submit', function () {
            self.addEvent($(this).serialize());
            return false;
        });

    },
    methods: {
        addEvent: function (data) {
            let url = '/calendars/calendar-events/add';
            var self = this;
            self.cal = $(self.$el);

            $.ajax({
                method: 'POST',
                dataType: 'json',
                url: url,
                data: data
            }).then(function(resp) {
              if (resp.event.entity !== undefined) {
                  var event = self.prepareEvent(resp.event.entity);
                  self.cal.fullCalendar('addEventSource', [event]);
              }

              $('#calendar-modal-add-event').modal('toggle');
            });
        },
        getEvent: function(event) {
            let url = '/calendars/calendar-events/view';
            let eventData = {
                id: event.id,
                calendar_id: event.calendar_id,
                event_type: event.event_type
            };

            $.ajax({
                method: 'POST',
                url: url,
                data: eventData,
            }).done(function (resp) {
                console.log(resp);
                if (resp) {
                    $('#calendar-modal-view-event').find('.modal-content').empty();
                    $('#calendar-modal-view-event').find('.modal-content').append(resp);
                    $('#calendar-modal-view-event').modal('toggle');
                }
            });
        },
        prepareEvent: function(entity) {
            return {
                id: entity.id,
                title: entity.title,
                start: moment().format(entity.start_date),
                end: moment().format(entity.end_date),
                color: entity.color,
                calendar_id: entity.calendar_id,
                event_type: entity.event_type
            };
        },
        getEvents: function (calendarId) {
            var url = '/calendars/calendars/events';
            var self = this;
            self.cal = $(self.$el);

            if (!calendarId) {
                return false;
            }

            $.ajax({
                method: 'POST',
                dataType: 'json',
                url: url,
                data: { 'calendarId': calendarId}
            }).then(function(result){
                if (!result) {
                    return;
                  }

                  var events = [];

                  result.forEach(function (elem, index) {
                      events.push(self.prepareEvent(elem));
                  });

                  self.cal.fullCalendar('addEventSource', events);
            });
        },
        removeEvents: function (calendarId) {
            var url = '/calendars/calendars/events';
            var self = this;
            self.cal = $(self.$el);

            if (!calendarId) {
                return false;
            }

            $.ajax({
                method: 'POST',
                dataType: 'json',
                url: url,
                data: {'calendarId': calendarId}
            }).then(function(result){
                if (!result) {
                    return;
                  }

                  result.forEach(function (item) {
                    self.cal.fullCalendar('removeEvents', item.id);
                  });
            });
        }
    }
});

var calendarApp = new Vue({
    el: '#qobo-calendar-app'
});
// @codingStandardsIgnoreEnd
