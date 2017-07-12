// @codingStandardsIgnoreStart
Vue.component('calendar', {
    template: '<div></div>',
    data: function() {
        return {
            cal: null
        };
    },
    mounted: function() {
        var events = [];
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
            'editable': false,
            'events': events,
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
            var url = '/calendars/calendar-events/add';
            var self = this;
            self.cal = $(self.$el);

            $.ajax({
                method: 'POST',
                dataType: 'json',
                url: url,
                data: data
            }).then(function (resp) {
              if (resp.event.entity !== undefined) {
                  var event = self.prepareEvent(resp.event.entity);
                  self.cal.fullCalendar('addEventSource', [event]);
              }

              $('#calendar-modal-add-event').modal('toggle');
            });
        },
        getEvent: function(event) {
            var url = '/calendars/calendar-events/view';
            var eventData = {
                id: event.id,
                calendar_id: event.calendar_id,
                event_type: event.event_type
            };

            $.ajax({
                method: 'POST',
                url: url,
                data: eventData,
            }).done(function (resp) {
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
            var url = '/calendars/calendars/events'; //: string
            var self = this;
            self.cal = $(self.$el);

            if (!calendarId) {
                return false;
            }

            $.ajax({
                method: 'POST',
                dataType: 'json',
                url: url,
                data: { 'calendarId': calendarId }
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
                data: { 'calendarId': calendarId }
            }).then(function (result) {
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

Vue.component('icon-component', {
    template: `<i class="fa" v-bind:class="getIcon(name)">&nbsp;</i>`,
    props: ['name'],
    methods: {
        getIcon: function (name) {
            return (name) ? 'fa-' + name : '';
        }
    }
});

Vue.component('calendar-link', {
    template: `<a :href="getUrl(itemUrl, itemValue)" :class="itemClass">
                    <icon-component v-if="itemIcon" :name="itemIcon"></icon-component>
                </a>`,
    props: ['itemIcon', 'itemValue', 'itemUrl', 'itemClass', 'itemDelete', 'itemConfirmMsg'],
    methods: {
        getUrl: function (url, id) {
            return (id) ? url + '/' + id : url;
        },
        confirmAlert: function() {
            console.log('confirm');
        }
    }
});

Vue.component('calendar-item', {
    template: `<div class="form-group checkbox">
                <label>
                    <input type="checkbox" @click="toggleCalendar(value)" v-model="toggle" :value="value" :name="name" :multiple="{ multiple: itemIsMultiple }" :class="[itemClass]"/>
                        <icon-component v-if="icon" :name="icon"></icon-component>
                        {{label}}
                </label>
                </div>`,
    props: ['label', 'value', 'icon','itemActive', 'name'],
    components: ['icon-component'],
    beforeMount: function() {
        this.toggle = this.itemActive;
    },
    data: function() {
        return {
            toggle: null,
            itemClass: 'calendar-id',
            itemIsMultiple: true,
        };
    },
    methods: {
        toggleCalendar(calendarId) {
            this.$emit('toggle-calendar', this.toggle, calendarId );
        }
    }
});

var calendarApp = new Vue({
    el: '#qobo-calendar-app',
    data: {
        calendars: [],
        calendarIds: [],
        events: []
    },
    mounted: function() {
        var self = this;
        $.ajax({
            dataType: 'json',
            url: '/calendars/calendars/index',
        }).done( function(resp) {
            self.calendars = resp;
            self.calendars.forEach( function(elem, key) {
                if (elem.active == true) {
                    self.calendarIds.push(elem.id);
                }
            });
        });
    },
    methods: {
        updateCalendarIds: function(state, id) {
            var self = this;
            var found = false;

            this.calendarIds.forEach( function (elem, key) {
                if (elem == id) {
                    if (state === false ) {
                        self.calendarIds.splice(key, 1);
                    } else {
                        found = true;
                    }
                }
            });

            if (state === true && !found) {
                this.calendarIds.push(id);
            }
        }
    }
});
// @codingStandardsIgnoreEnd
