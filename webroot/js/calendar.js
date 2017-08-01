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
                            <div class="form-group text">
                            <label>Start Date:</label>
                                <input type="text" name="CalendarEvents[start_date]" v-model="startField" class="calendar-start-datetimepicker form-control"/>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <div class="form-group text">
                            <label>End Date:</label>
                            <input type="text" name="CalendarEvents[end_date]" v-model="endField" class="calendar-end-datetimepicker form-control"/>
                            </div>
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
                            <div class="form-group">
                            <label>Frequency:</label>
                            <select v-model="frequency" class="form-control">
                                <option v-for="item in frequencies" :value="item.value">
                                    {{item.label}}
                                </option>
                            </select>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-12" v-if="isWeekly || isYearly || isDaily">
                            <div class="form-group">
                                <label>Interval:</label>
                                <select v-model="frequencyInterval" class="form-control">
                                    <option v-for="item in frequencyIntervals" :value="item.value">
                                        {{item.label}}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="col-xs-12 col-md-12" v-if="isWeekly">
                            <div class="form-group">
                                <label><input v-model="weekDays" type="checkbox" value="MO"/>MO</label>
                                <label><input v-model="weekDays" type="checkbox" value="TU"/>TU</label>
                                <label><input v-model="weekDays" type="checkbox" value="WE"/>WE</label>
                                <label><input v-model="weekDays" type="checkbox" value="TH"/>TH</label>
                                <label><input v-model="weekDays" type="checkbox" value="FR"/>FR</label>
                                <label><input v-model="weekDays" type="checkbox" value="SA"/>SA</label>
                                <label><input v-model="weekDays" type="checkbox" value="SU"/>SU</label>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-12" v-if="isRecurring">
                            <div class="form-group">
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
                                        On: <input type="text" v-model="recurringEndDate" :disabled="recurringEnd !== 'date'" class="calendar-until-datetimepicker"/>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-12" v-if="isRecurring">
                            Recurring Event: {{recurringRule}}
                        </div>
                    </div>
                </div>
            </div>`,
    props: ['calendarsList', 'timezone', 'clickedDate'],
    data: function() {
        return {
            attendees: [],
			attendeesList: [],
			calendarId: null,
            eventType: null,
            eventTypes: [],
			eventTypesList: [],
            frequency: null,
            frequencyInterval: null,
            frequencyIntervals: [],
            frequencies: [
                { value: 3, label: 'Daily', },
                { value: 2, label: 'Weekly' },
                { value: 1, label: 'Monthly' },
                { value: 0, label: 'Yearly' },
            ],
            recurringEnd: 'infinity',
            recurringOccurence: null,
            recurringEndDate: null,
            recurringEndObject: null,
            recurringRule: null,
            weekDays: [],
            isRecurring: 0,
            pickerOptions: {
                singleDatePicker: true,
                showDropdowns: true,
                timePicker: true,
                drops: "down",
                timePicker12Hour: false,
                timePickerIncrement: 5,
                format: "YYYY-MM-DD HH:mm",
            },
            startDatePicker: null,
            endDatePicker: null,
            startField: null,
            endField: null,
        };
    },
    beforeMount: function() {
        this.frequencyIntervals = [];
        for(var i = 1; i <= 30; i++) {
            this.frequencyIntervals.push({ value: i, label: i.toString() });
        }
    },
    mounted: function() {
        $('.calendar-start-datetimepicker').daterangepicker(this.pickerOptions);
        $('.calendar-end-datetimepicker').daterangepicker(this.pickerOptions);

        this.startDatePicker = $('.calendar-start-datetimepicker').data('daterangepicker');
        this.endDatePicker = $('.calendar-end-datetimepicker').data('daterangepicker');
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
        clickedDate: function() {
            if (!this.eventType) {
                console.log('empty event type');
                this.setDefaultDates();
            }
        },
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
        setDefaultDates: function() {
            if (!this.startField && this.clickedDate) {
                var start = moment(this.clickedDate);
                start.set({'hour': 9});
                this.startDatePicker.setStartDate(start);
                this.startDatePicker.setEndDate(start);

                this.startField = start.format('YYYY-MM-DD HH:mm');
            }

            if (!this.endField && this.clickedDate) {
                var end = moment(this.clickedDate);
                end.set({'hour': 9, 'minute': 30});

                this.endDatePicker.setStartDate(end);
                this.endDatePicker.setEndDate(end);

                this.endField = end.format('YYYY-MM-DD HH:mm');
            }
        },
        setEventTypeDates: function() {
            var self = this;
            var event = null;
            this.eventTypes.forEach((elem, key) => {
                if (elem.value == self.eventType.value) {
                    event = elem;
                }
            });

            if (!event) {
                return;
            }

            momentStart = this.startDatePicker.startDate;
            momentEnd = this.endDatePicker.startDate;

            if (event.start_time) {
                hhmm = event.start_time.split(':');
                momentStart.set('hour', hhmm[0]);
                momentStart.set('minute', hhmm[1]);
                this.startDatePicker.setStartDate(momentStart);
                this.startDatePicker.setEndDate(momentStart);
                this.startField = momentStart.format('YYYY-MM-DD HH:mm');
            }

            if (event.end_time) {
                hhmm = event.end_time.split(':');
                momentEnd.set('hour', hhmm[0]);
                momentEnd.set('minute', hhmm[1]);
                if (parseInt(momentEnd.format('H')) < parseInt(momentStart.format('H'))) {
                    momentEnd.add(1, 'days');
                } else {
                    momentEnd.date(momentStart.format('D'));
                    this.startDatePicker.setStartDate(momentStart);
                    this.startDatePicker.setEndDate(momentStart);
                    this.startField = momentStart.format('YYYY-MM-DD HH:mm');
                    this.endField = momentEnd.format('YYYY-MM-DD HH:mm');
                }
                this.endDatePicker.setStartDate(momentEnd);
                this.endDatePicker.setEndDate(momentEnd);
                this.endField = momentEnd.format('YYYY-MM-DD HH:mm');
            }
        },
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
        clickedDate: null,
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
            this.clickedDate = date;
            $('#calendar-modal-add-event').modal('toggle');
        }
    }
});
// @codingStandardsIgnoreEnd
