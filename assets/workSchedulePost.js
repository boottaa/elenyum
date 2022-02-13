import '/node_modules/bootstrap/dist/css/bootstrap.css';
import '/node_modules/@fortawesome/fontawesome-free/css/all.css';
import '/node_modules/@fullcalendar/common/main.css';
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

document.addEventListener('DOMContentLoaded', function () {
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
        select: function (info) {
            let counter = (info.end.getTime() - info.start.getTime()) / 86400000;
            for (let i = 0; i < counter; i++) {
                let dateStart = addDay(new Date(info.start.getTime()), i);
                let dateEnd = addDay(new Date(info.start.getTime()), i + 1);
                let getStartDate = getDate(dateStart);
                let getEndDate = getDate(dateEnd);
                let isDelete = false;

                calendar.getEvents().forEach((event) => {
                    if (event.startStr === getStartDate || event.endStr === getEndDate) {
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
                            start: new Date(),
                            end: new Date(),
                        },
                        overlap: true,
                        display: 'background',
                        color: 'rgba(0,90,255,0.4)'
                    });
                }
            }
        },
        eventContent: function (e) {
            let divEl = document.createElement('div');
            divEl.className = 'eventBlock';
            console.log(e);
            divEl.innerHTML = `<div>${getHours(e.event.extendedProps.workSchedule.start)} - ${getHours(e.event.extendedProps.workSchedule.end)}</div>`;
            let arrayOfDomNodes = [divEl];

            return {domNodes: arrayOfDomNodes};
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

            object: {
                dateRange: [],

                template: null,

                monday: [],
                tuesday: [],
                wednesday: [],
                thursday: [],
                friday: [],
                saturday: [],
                sunday: []
            },
        }
    },
    created() {
        this.resetObject();

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
            console.log(this.object);
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