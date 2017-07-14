// @codingStandardsIgnoreStart
//{{{
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
//}}}

Vue.component('calendar', {
    template: '<div></div>',
    props: ['ids', 'events', 'editable'],
    data: function() {
        return {
            calendarInstance: null,
            // prepared FullCalendar events based on the db entities.
            calendarEvents: []
        };
    },
    watch: {
        events: function() {
            var self = this;
            // @FIXME: remove only the ones that deserve it.
            this.calendarEvents = [];

            if (!this.events.length) {
                return;
            }

            this.events.forEach( (event, index) => {
                self.calendarEvents.push({
                    id: event.id,
                    title: event.title,
                    color: event.color,
                    start: moment().format(event.start_date),
                    end: moment().format(event.end_date),
                    calendar_id: event.calendar_id,
                    event_type: event.event_type
                });
            });
        },
        calendarEvents: function() {
            this.calendarInstance.fullCalendar('removeEvents');
            this.calendarInstance.fullCalendar('addEventSource', this.calendarEvents);
        }
    },
    mounted: function() {
        var self = this;
        self.calendarInstance = $(self.$el);

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
            editable: this.editable,
            dayClick: function(date, jsEvent, view) {
                self.dayClick(date, event, view);
            },
            eventClick: function(event) {
                self.eventClick(event);
            }
        };

        this.calendarInstance.fullCalendar(args);

        $('.calendar-form-add-event').on('submit', function () {
            self.addEvent($(this).serialize());
            return false;
        });
    },
    methods: {
        eventClick: function(calendarEvent) {
            this.$emit('event-info', calendarEvent);
        },
        dayClick: function(date, event, view) {
            this.$emit('add-event', date, event, view);
        },
        addEvent: function(data) {
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
                    self.events.push(resp.event.entity);
                }
                $('#calendar-modal-add-event').modal('toggle');
            });
        }
    }
});

var calendarApp = new Vue({
    el: '#qobo-calendar-app',
    data: {
        ids: [],
        events: [],
        calendars: [],
        editable: false,
    },
    beforeMount: function() {
        this.getCalendars();
    },
    methods: {
        getCalendars: function() {
            var self = this;
            $.ajax({
                dataType: 'json',
                url: '/calendars/calendars/index',
            }).done( function(resp) {
                self.calendars = resp;
                self.calendars.forEach( function(elem, key) {
                    if (elem.active == true) {
                        self.ids.push(elem.id);
                        self.getEvents(elem.id);
                    }
                });
            });
        },
        getEvents: function(id) {
            var self = this;
            var url = '/calendars/calendars/events'; //: string

            $.ajax({
                method: 'POST',
                dataType: 'json',
                url: url,
                data: { 'calendarId': id}
            }).then(function(resp){
                if (!resp) {
                    return;
                }

                var event_ids = self.events.map( (element) => {
                    return element.id;
                });

                resp.forEach(function (elem, index) {
                    if ( !event_ids.includes(elem.id) ) {
                        self.events.push(elem);
                    }
                });
            });
        },
        removeEvents: function(id) {
            this.events = this.events.filter( function (item) {
                if (item.calendar_id !== id) {
                    return item;
                }
            });
        },
        updateCalendarIds: function(state, id) {
            var self = this;
            var found = false;

            this.ids.forEach( function (elem, key) {
                if (elem == id) {
                    if (state === false ) {
                        self.ids.splice(key, 1);
                        self.removeEvents(id);
                    } else {
                        found = true;
                    }
                }
            });

            if (state === true && !found) {
                this.ids.push(id);
                this.getEvents(id);
            }
        },
        getEventInfo: function(calendarEvent) {
            var url = '/calendars/calendar-events/view';
            var post = {
                id: calendarEvent.id,
                calendar_id: calendarEvent.calendar_id,
                event_type: calendarEvent.event_type
            };

            $.ajax({
                method: 'POST',
                url: url,
                data: post,
            }).done(function (resp) {
                if (resp) {
                    $('#calendar-modal-view-event').find('.modal-content').empty();
                    $('#calendar-modal-view-event').find('.modal-content').append(resp);
                    $('#calendar-modal-view-event').modal('toggle');
                }
            });
        },
        addCalendarEvent: function(date, event, view) {
            $('#calendar-modal-add-event').modal('toggle');
        }
    }
});
// @codingStandardsIgnoreEnd
