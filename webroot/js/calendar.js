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
                            <input type="text" name="CalendarEvents[start_date]" v-model="start" class="calendar-datetimepicker form-control"/>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <div class="form-group text">
                            <label>End Date:</label>
                            <input type="text" name="CalendarEvents[end_date]" v-model="end" class="calendar-datetimepicker form-control"/>
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
                                        <label>All Day</label>
                                        <input type="checkbox" name="CalendarEvents[all_day]" v-model="isAllDay"/>
                                    </div>
                                </div>
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
                            <v-select v-model="frequency" :options="frequencies" class=""></v-select>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-12" v-if="isDaily">
                            <div class="form-group">
                                <label>Interval: </label>
                                <v-select v-model="frequencyInterval" :options="frequencyIntervals" class=""></v-select>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-12" v-if="isWeekly">
                            <div class="form-group">
                                <label><input type="checkbox" value="MO"/>MO</label>
                                <label><input type="checkbox" value="TU"/>TU</label>
                                <label><input type="checkbox" value="WE"/>WE</label>
                                <label><input type="checkbox" value="TH"/>TH</label>
                                <label><input type="checkbox" value="FR"/>FR</label>
                                <label><input type="checkbox" value="SA"/>SA</label>
                                <label><input type="checkbox" value="SU"/>SU</label>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-12" v-if="isWeekly">
                            <div class="form-group">
                                <label>Intervals: </label>
                                <v-select v-model="frequencyInterval" :options="frequencyIntervals"></v-select>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-12" v-if="isYearly">
                            <div class="form-group">
                                <label>Intervals:</label>
                                <v-select v-model="frequencyInterval" :options="frequencyIntervals"></v-select>
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
                                        On: <input type="text" v-model="recurringEndDate" :disabled="recurringEnd !== 'date'" class="calendar-datetimepicker"/>
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
    props: ['calendarsList', 'timezone', 'start', 'end'],
    data: function() {
        return {
            attendees: [],
			attendeesList: [],
			calendarId: null,
            eventType: null,
            frequency: null,
            frequencyInterval: null,
            frequencyIntervals: [],
            recurringRule: null,
            recurringEnd: 'infinity',
            recurringOccurence: null,
            recurringEndDate: null,
            eventTypes: [],
			eventTypesList: [],
            isRecurring: 0,
            isAllDay: 0,
            eventTypes: [],
			eventTypesList: [],
            frequency: null,
            frequencyInterval: null,
            frequencyIntervals: [],
            frequencies: [
                { value: 'DAILY', label: 'Daily', },
                { value: 'WEEKLY', label: 'Weekly' },
                { value: 'MONTHLY', label: 'Monthly' },
                { value: 'YEARLY', label: 'Yearly' },
            ],
            recurringEnd: 'infinity',
            recurringOccurence: null,
            recurringEndDate: null,
            //recurringRule: null,
            isAllDay: 0,
            isRecurring: 0,
        };
    },
    beforeMount: function() {
        this.frequencyIntervals = [];
        for(var i = 1; i <= 30; i++) {
            this.frequencyIntervals.push({ value: i, label: i.toString() });
        }
    },
    computed: {
        isDaily: function() {
            if (this.frequency) {
				return (this.frequency.value == 'DAILY' && this.isRecurring) ? true : false;
			}

			return false;
        },
        isMonthly: function() {
            if (this.frequency) {
				return (this.frequency.value == 'MONTHLY' && this.isRecurring) ? true : false;
            }

            return false;
        },
        isWeekly: function() {
            if (this.frequency) {
				return (this.frequency.value == 'WEEKLY' && this.isRecurring) ? true : false;
            }

            return false;
        },
        isYearly: function() {
            if (this.frequency) {
            	return (this.frequency.value == 'YEARLY' && this.isRecurring) ? true : false;
            }

            return false;
        },
        recurringRule: function() {
            var rrule = new RRule({
                freq: RRule.WEEKLY
            });
            console.log(rrule);
            console.log(rrule.toString());
            return 'foobar';
        },
    },
    watch: {
        recurringEnd: function() {
            if (this.recurringEnd === 'date' ) {
				$('.calendar-datetimepicker').daterangepicker({
					singleDatePicker: true,
					showDropdowns: true,
					timePicker: true,
					drops: "down",
					timePicker12Hour: false,
					timePickerIncrement: 5,
					format: "YYYY-MM-DD HH:mm",
				});
            }
        },
        calendarId: function() {
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
                        self.eventTypes = [];
                        types.forEach( (elem, key) => {
                            self.eventTypes.push(elem);
                        });
                    }
                });
            }
        },
		eventTypes: function() {
			var self = this;
			self.eventTypesList = [];
			self.eventType = null;

			if (this.eventTypes.length) {
				this.eventTypes.forEach( (item, key) => {
					self.eventTypesList.push({value: item.value, label: item.name});
				});

				this.eventType = self.eventTypesList[0];
			}
		},
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
    },
    computed: {
        isIntervalChanged: function() {
            return [this.start, this.end].join('');
        }
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
            // @NOTE: used for default values, if no config/calendar.php
            // calendar/event types specified.
            /*
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

            $('#calendar-modal-add-event').find('.calendar-dyn-attendees').select2('val', '');
            $('#calendar-modal-add-event').find('.calendar-dyn-event-type').select2('val','');
            */
            $('#calendar-modal-add-event').modal('toggle');
        }
    }
});
// @codingStandardsIgnoreEnd
