// @codingStandardsIgnoreStart

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


Vue.component('calendar', {
    template: '<div></div>',
    props: ['ids', 'events', 'editable', 'start', 'end', 'timezone'],
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
            firstDay: 1,
            defaultDate: moment(this.start),
            editable: this.editable,
            dayClick: function(date, jsEvent, view) {
                self.dayClick(date, event, view);
            },
            eventClick: function(event) {
                self.eventClick(event);
            },
            viewRender: function(view, element) {
                self.$emit('interval-update', view.start.format('YYYY-MM-DD'), view.end.format('YYYY-MM-DD'));
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

Vue.component('v-select', VueSelect.VueSelect);

Vue.component('input-datepicker', {
    template: `
        <div class="form-group text">
            <label>{{label}}</label>
            <input type="text" :disabled="disabled" :name="name" v-model="value" :class="className" class="form-control"/>
        </div>`,
    props: ['name', 'className', 'label', 'disabled', 'lookupField'],
    mounted: function() {
        var self = this;
        self.instance = $(self.$el).find('input').daterangepicker(this.pickerOptions).data('daterangepicker');

        $(self.$el).find('input').on('apply.daterangepicker', function(ev, picker) {
            self.value = picker.startDate.format(self.pickerOptions.format);
        });
    },
    data: function() {
        return {
            instance: null,
            value: null,
            pickerOptions: {
                singleDatePicker: true,
                showDropdowns: true,
                timePicker: true,
                drops: "down",
                timePicker12Hour: false,
                timePickerIncrement: 5,
                format: "YYYY-MM-DD HH:mm",
            },
        };
    },
});

Vue.component('input-select', {
    template: `<div class="form-group">
        <label>{{label}}</label>
        <select v-model="value" class="form-control" :name="name">
            <option v-for="item in options" :value="item.value">{{item.label}}</option>
        </select>
    </div>`,
    props: ['options', 'name', 'label'],
    data: function() {
        return {
            value: null,
        };
    },
    watch: {
        value: function() {
            console.log( this.value );
        }
    }
});

Vue.component('input-checkboxes', {
    template: `
         <div class="form-group">
            <label v-for="item in options">
                <input type="checkbox" v-model="values" :value="item.value"/>{{item.label}}
            </label>
        </div>`,
    data: function() {
        return {
            options: [
                {label: 'MO', value: 'MO'},
                {label: 'TU', value: 'TU'},
                {label: 'WE', value: 'WE'},
                {label: 'TH', value: 'TH'},
                {label: 'FR', value: 'FR'},
                {label: 'SA', value: 'SA'},
                {label: 'SU', value: 'SU'}
            ],
            values: [],
        };
    },
    watch: {
        values: function() {
            console.log(this.values);
        },
    }
});

Vue.component('calendar-recurring-until', {
    template:
    `<div class="form-group">
        <span><strong>Ends:</strong></span>
        <div class='form-group radio'>
            <label><input type="radio" v-model="recurringEnd" value="infinity"/>Never</label>
        </div>
        <div class='form-group radio'>
            <label>
                <input type="radio" v-model="recurringEnd" value="occurrence"/>
                After <input type="text" v-model="recurringOccurence" :disabled="recurringEnd !== 'occurrence'"/> occurrences.
            </label>
        </div>
        <div class='form-group radio'>
            <label>
                <input type="radio" v-model="recurringEnd" value="date">
                On: <input-datepicker name="CalendarEvents[until]" :disabled="recurringEnd !== 'date'" class-name="calendar-until-datetimepicker"></input-datepicker>
            </label>
        </div>
    </div>`,
    data: function() {
        return {
            recurringEnd: 'infinity',
            recurringOccurence: null,
        };
    },
    watch: {
        recurringEnd: function() {
            console.log(this.recurringEnd);
        }
    }
});

Vue.component('calendar-modal', {
    template: `<div class="row">
                <div class="col-xs-12 col-md-12">
                    <div class="form-group">
                        <v-select v-model="calendarId" :options="calendarsList" placeholder="-- Please choose Calendar --"></v-select>
                    </div>
                </div>
                <div class="col-xs-12 col-md-12">
                    <div class="form-group">
                        <v-select v-model="eventType" :options="eventTypesList" placeholder="-- Please choose Event Type --"></v-select>
                    </div>
                </div>
                <div class="col-xs-12 col-md-12">
                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <input-datepicker name="CalendarEvents[start_date]" label="Start Date:" lookup-field="start_time" class-name="calendar-start-datetimepicker"></input-datepicker>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <input-datepicker name="CalendarEvents[end_date]" label="End Date:" lookup-field="end_time" class-name="calendar-end-datetimepicker"></input-datepicker>
                        </div>
						<div class="col-xs-12 col-md-12">
							<div class="form-group text">
								<label> Attendees: </label>
								<v-select v-model="attendeesList" :options="attendees" multiple></v-select>
							</div>
						</div>
                        <div class="col-xs-12 col-md-12">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group text">
                                        <label>Repeats:</label>
                                        <input type="checkbox" name="CalendarEvents[is_recurring]" v-model="isRecurring"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-12" v-if="isRecurring">
                            <input-select name="CalendarEvents[frequency]" :options="frequencies" label="Frequency:"></input-select>
                        </div>
                        <div class="col-xs-12 col-md-12" v-if="isWeekly || isYearly || isDaily">
                            <input-select name="CalendarEvents[intervals]" :options="frequencyIntervals" label="Interval:"></input-select>
                        </div>

                        <div class="col-xs-12 col-md-12" v-if="isWeekly">
                            <input-checkboxes></input-checkboxes>
                        </div>
                        <div class="col-xs-12 col-md-12" v-if="isRecurring">
                            <calendar-recurring-until></calendar-recurring-until>
                        </div>
                        <div class="col-xs-12 col-md-12" v-if="isRecurring">
                            Recurring Event: {{recurringRule}}
                        </div>
                    </div>
                </div>
            </div>`,
    props: ['calendarsList', 'timezone', 'eventClick'],
    data: function() {
        return {
			calendarId: null,
            attendees: [],
			attendeesList: [],
            eventType: null,
            eventTypes: [],
			eventTypesList: [],
            frequencyIntervals: [],
            frequencies: [
                { value: 3, label: 'Daily', },
                { value: 2, label: 'Weekly' },
                { value: 1, label: 'Monthly' },
                { value: 0, label: 'Yearly' },
            ],
            recurringRule: null,
            weekDays: [],
            isRecurring: 0,
        };
    },
    beforeMount: function() {
        this.frequencyIntervals = [];
        for(var i = 1; i <= 30; i++) {
            this.frequencyIntervals.push({ value: i, label: i.toString() });
        }
    },
    mounted: function() {
    /*
        $('.calendar-start-datetimepicker').daterangepicker(this.pickerOptions);
        $('.calendar-end-datetimepicker').daterangepicker(this.pickerOptions);

        this.startDatePicker = $('.calendar-start-datetimepicker').data('daterangepicker');
        this.endDatePicker = $('.calendar-end-datetimepicker').data('daterangepicker');

    */
    },
    computed: {
        isDaily: function() {
            if (this.frequency === 3 && this.isRecurring) {
                this.getRecurringRule();
                return true;
            }

			return false;
        },
        isMonthly: function() {
            if (this.frequency === 1 && this.isRecurring) {
                this.getRecurringRule();
                return true;
            }

            return false;
        },
        isWeekly: function() {
            if (this.frequency === 2 && this.isRecurring) {
                this.getRecurringRule();
                return true;
            }

            return false;
        },
        isYearly: function() {
            if (this.frequency === 0 && this.isRecurring) {
                this.getRecurringRule();
                return true;
            }

            return false;
        },
    },
    watch: {
        eventType: function() {
            this.setEventTypeDates();
        },
        frequencyInterval: function() {
            this.getRecurringRule();
        },
        weekDaysChanged: function() {
            this.getRecurringRule();
        },
        calendarId: function() {
            this.getEventTypes();
        },
    },
    methods: {
        getEventTypes: function() {
            var self = this;
			this.eventTypes = [];
			this.eventTypesList = [];

            if (this.calendarId.value) {
                $.ajax({
                    url: '/calendars/calendar-events/get-event-types',
                    data: { id: this.calendarId.value },
                    dataType: 'json',
                    method: 'post',
                }).done(function(types) {
                    if (types.length) {
                        types.forEach( (elem, key) => {
                            self.eventTypes.push(elem);
                            self.eventTypesList.push({label: elem.name, value: elem.value})
                        });
                    }
                });
            }
        },
        getRecurringRule: function () {
            if (!this.isRecurring) {
                return null;
            }

            var byweekdays = [];
            var opts = { freq: this.frequency };

            if (this.weekDays.length) {
                this.weekDays.forEach( (day, k) => {
                    byweekdays.push(RRule[day]);
                });

                opts.byweekday = byweekdays;
            }

            if (this.frequencyInterval) {
                opts.interval = this.frequencyInterval;
            }

            if (this.recurringOccurence) {
                opts.count = this.recurringOccurence;
            }

            if (this.recurringEndObject) {
                opts.until = this.recurringEndObject.toDate();
            }

            var rrule = new RRule(opts);
            this.recurringRule = rrule.toText();
        }
    },
});


var calendarApp = new Vue({
    el: '#qobo-calendar-app',
    data: {
        ids: [],
        events: [],
        calendars: [],
        calendarsList: [],
        editable: false,
        start: null,
        end: null,
        timezone: null,
        eventClick: null,
    },
    computed: {
        isIntervalChanged: function() {
            return [this.start, this.end].join('');
        },
    },
    watch: {
        calendars: function() {
            var self = this;
            this.calendarsList = [];
            if (this.calendars) {
               this.calendars.forEach((elem, key) => {
                    self.calendarsList.push( { value: elem.id, label: elem.name } );
               });
            }
        },
        isIntervalChanged: function() {
            var self = this;
            if (this.ids.length) {
                self.events = [];
                this.ids.forEach( function(calendarId, key) {
                    self.getEvents(calendarId);
                });
            }
        },
    },
    beforeMount: function() {
        this.start = this.$el.attributes.start.value;
        this.end = this.$el.attributes.end.value;
        this.timezone = this.$el.attributes.timezone.value;

        this.getCalendars();
    },
    methods: {
        updateStartEnd: function (start, end) {
            this.start = start;
            this.end = end;
        },
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
                data: {
                    'calendarId': id,
                    'period': {
                        'start_date': this.start,
                        'end_date': this.end,
                    },
                    'timezone': this.timezone,
                }
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
            this.eventClick = date;
            $('#calendar-modal-add-event').modal('toggle');
        }
    }
});
// @codingStandardsIgnoreEnd
