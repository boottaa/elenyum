import '/node_modules/bootstrap/dist/css/bootstrap.css';
import '/node_modules/@fortawesome/fontawesome-free/css/all.css';
import '/node_modules/@fullcalendar/common/main.css';
import './styles/workSchedulePost.css';
import {Calendar} from "@fullcalendar/core";

import './app';
import "./src/baseCalendar";
import Vue from 'vue';

import './src/vueRoleSelect';
//https://www.npmjs.com/package/vue2-datepicker
import {vueAlert} from "./src/vueAlert";
import vSelect from 'vue-select';
import {get, post} from "./src/baseQuery";
import {vueWorkShedule} from "./src/vueWorkShedule";
import DatePicker from "vue2-datepicker";
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from "@fullcalendar/interaction";

/**
 * Время для сотрудника
 * @todo https://fullcalendar.io/docs/businessHours-per-resource
 */
$(document).ready(function () {
    let elModalEvent = document.getElementById('modalEvent'),
        modalEvent = new bootstrap.Modal(elModalEvent);

    let calendarEl = document.getElementById('workScheduleCalendar');

    function getDate(date) {
        return date.getFullYear().toString() + '-' + String(date.getMonth() + 1).padStart(2, '0') + '-' + date.getDate().toString().padStart(2, '0');
    }

    function getHours(date) {
        return date.getHours().toString().padStart(2, '0') + ":" + date.getMinutes().toString().padStart(2, '0');
    }

    function addDay(date, day) {
        if (day !== 0) {
            return new Date(date.getTime() + (day * 24 * 60 * 60 * 1000));
        }

        return date;
    }

    let calendar = new Calendar(calendarEl, {
        selectable: true,
        plugins: [dayGridPlugin, interactionPlugin],
        headerToolbar: {
            right: 'today,prev,next',
        },
        initialView: 'dayGridMonth',
        locale: 'ru',
        height: 'auto',
        firstDay: 1,
        buttonText: {
            today: 'сегодня',
        },
        eventClick: function (info) {
            workSchedulePost.selected.time = [
                info.event.extendedProps.workSchedule.start,
                info.event.extendedProps.workSchedule.end
            ];

            info.event.extendedProps.workSchedule.start = new Date();

            workSchedulePost.$once('editedEvent', (data) => {
                if (data !== null) {

                    info.event.extendedProps.workSchedule.start = data[0];
                    info.event.extendedProps.workSchedule.end = data[1];
                    modalEvent.hide();

                    calendar.render();
                }
            });

            modalEvent.show();
        },
        select: function (info) {
            let counter = (info.end.getTime() - info.start.getTime()) / 86400000;
            for (let i = 0; i < counter; i++) {
                let dateStart = addDay(new Date(info.start.getTime()), i);
                let dateEnd = addDay(new Date(info.start.getTime()), i + 1);
                let getStartDate = getDate(dateStart);
                let getEndDate = getDate(dateEnd);
                let isDelete = false;

                calendar.getEvents().forEach((event) => {
                    if (event.extendedProps.workSchedule.startStr === getStartDate || event.extendedProps.workSchedule.endStr === getEndDate) {
                        isDelete = true;

                        event.remove();
                    }
                });

                if (isDelete === false) {
                    calendar.addEvent({
                        id: i,
                        start: getStartDate,
                        end: getEndDate,
                        workSchedule: {
                            startStr: getStartDate,
                            endStr: getEndDate,
                        },
                        overlap: true,
                        display: 'background',
                        color: 'rgba(0,90,255,0.4)'
                    });
                    let start = workSchedulePost.branch.start;
                    let end = workSchedulePost.branch.end;
                    let timeBranchWorkStart = (new Date(dateStart.getTime()));
                    timeBranchWorkStart.setHours(start.getHours());
                    timeBranchWorkStart.setMinutes(start.getMinutes());

                    let timeBranchWorkEnd = (new Date(dateStart.getTime()));
                    timeBranchWorkEnd.setHours(end.getHours());
                    timeBranchWorkEnd.setMinutes(end.getMinutes());

                    calendar.addEvent({
                        id: i,
                        start: getStartDate,
                        end: getEndDate,
                        workSchedule: {
                            startStr: getStartDate,
                            endStr: getEndDate,
                            start: timeBranchWorkStart,
                            end: timeBranchWorkEnd,
                        },
                        display: 'block',
                    });
                }
            }
            calendar.unselect();
        },
        eventContent: function (e) {
            let divEl = document.createElement('div');
            divEl.className = 'eventBlockWorkShedule';

            if (e.event.display !== 'background') {
                divEl.innerText = `${getHours(e.event.extendedProps.workSchedule.start)} - ${getHours(e.event.extendedProps.workSchedule.end)}`;
                let arrayOfDomNodes = [divEl];

                return {domNodes: arrayOfDomNodes};
            }
        },
    });

    calendar.render();
});

let object = {
    title: null,
    template: null,
};

let workSchedulePost = new Vue({
    components: {vueAlert, vSelect, vueWorkShedule, DatePicker},
    el: '#workSchedulePost',
    data() {
        return {
            templates: [
                {
                    id: 1,
                    title: 'Каждый день (7/0)',
                },
                {
                    id: 2,
                    title: 'Через день (1/1)',
                },
                {
                    id: 3,
                    title: 'Каждые 2 дня (2/2)',
                },
                {
                    id: 4,
                    title: 'По будням (5/2)',
                },
                {
                    id: 5,
                    title: 'Выборочно',
                },
            ],
            selected: {
                time: [],
            },

            object: {
                template: null,
            },
            branch: {
                item: null,
                start: null,
                end: null
            }
        }
    },
    created() {
        this.resetObject();

        get('/api/branch/get', (r) => {
            if (r.success === true) {
                this.branch.item = r.item;
                this.branch.start = new Date(r.item.start * 1000);
                this.branch.end = new Date(r.item.end * 1000);
            }
        });

        let array = location.href.split('/', 6);
        let id = array[5];

        if (id !== undefined) {
            // get('/api/workSchedule/get/' + id, (r) => {
            //     if (r.success === true) {
            //         this.object = r.item;
            //     }
            // });
        }
    },
    methods: {
        send() {
            return 1;
        },
        onEditedTime() {
            this.$emit('editedEvent', this.selected.time);
            return 1;
        },

        resetObject() {
            this.object = Object.assign({}, object);
        },

        onSelected() {
            this.editable = false;
        },
    },
    delimiters: ['${', '}$'],
});