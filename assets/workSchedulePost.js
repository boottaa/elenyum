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
import {vueConfig} from "./src/vueConfig";

/**
 * Время для сотрудника
 * @todo https://fullcalendar.io/docs/businessHours-per-resource
 */
// Для заполнения графика сотрудников по шаблону (Пока этого нет)
function lastday(y, m) {
    return new Date(y, m + 1, 0);
}


$(document).ready(function () {
    let elModalEvent = document.getElementById('modalEvent'),
        modalEvent = new bootstrap.Modal(elModalEvent);

    let calendarEl = document.getElementById('workScheduleCalendar');

    elModalEvent.addEventListener('hidden.bs.modal', function () {
        workSchedulePost.$emit('editedEvent', null);
    });
    workSchedulePost.$once('configLoaded', (data) => {

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
            selectable: workSchedulePost.canEdit,
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
            viewDidMount: function () {
                $('.fc-prev-button, .fc-next-button, .fc-today-button').on('click', () => {
                    workSchedulePost.start = calendar.view.currentStart;
                    workSchedulePost.end = calendar.view.currentEnd;
                });
            },
            eventClick: function (info) {
                if (workSchedulePost.canEdit) {
                    workSchedulePost.selected.time = [
                        info.event.extendedProps.workSchedule.start,
                        info.event.extendedProps.workSchedule.end
                    ];

                    workSchedulePost.$once('editedEvent', (data) => {
                        if (data !== null) {
                            info.event.extendedProps.workSchedule.start = data[0];
                            info.event.extendedProps.workSchedule.end = data[1];
                            modalEvent.hide();

                            calendar.render();
                        }
                    });

                    modalEvent.show();
                }
            },
            select: function (info) {
                // Может быть выбрано сразу несколько дней, по этому разбиваем на дни и проходимся по каждому дню
                let counter = (info.end.getTime() - info.start.getTime()) / 86400000;
                for (let i = 0; i < counter; i++) {
                    let dateStart = addDay(new Date(info.start.getTime()), i);
                    let dateEnd = addDay(new Date(info.start.getTime()), i + 1);
                    let getStartDate = getDate(dateStart);
                    let getEndDate = getDate(dateEnd);

                    let isDelete = false;

                    calendar.getEvents().forEach((event) => {
                        if (event.extendedProps.workSchedule.startStr === getStartDate && event.extendedProps.workSchedule.endStr === getEndDate) {
                            event.remove();
                            isDelete = true;
                        }
                    });
                    if (isDelete) {
                        workSchedulePost.object.workSchedules = workSchedulePost.object.workSchedules.filter((event) => {
                            return !(event.startStr === getStartDate && event.endStr === getEndDate);
                        });
                    } else {
                        let start = workSchedulePost.branch.start;
                        let end = workSchedulePost.branch.end;
                        let timeBranchWorkStart = (new Date(dateStart.getTime()));
                        timeBranchWorkStart.setHours(start.getHours());
                        timeBranchWorkStart.setMinutes(start.getMinutes());

                        let timeBranchWorkEnd = (new Date(dateStart.getTime()));
                        timeBranchWorkEnd.setHours(end.getHours());
                        timeBranchWorkEnd.setMinutes(end.getMinutes());

                        let workSchedule = {
                            id: i,
                            startStr: getStartDate,
                            endStr: getEndDate,
                            start: timeBranchWorkStart,
                            end: timeBranchWorkEnd,
                        };

                        addWorkSchedule(calendar, workSchedule);
                        workSchedulePost.object.workSchedules.push(workSchedule);
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

        function addWorkSchedule(calendar, workSchedule) {
            calendar.addEvent({
                id: workSchedule.id,
                start: workSchedule.startStr,
                end: workSchedule.endStr,
                workSchedule: workSchedule,
                overlap: true,
                display: 'background',
                color: 'rgba(0,90,255,0.4)'
            });

            calendar.addEvent({
                id: workSchedule.id,
                start: workSchedule.start,
                end: workSchedule.end,
                workSchedule: workSchedule,
                display: 'block',
            });
        }

        //Если выбран шаблон то выводим календарь
        workSchedulePost.$on('workSchedulesLoaded', (data) => {
            calendar.removeAllEvents();
            workSchedulePost.object.workSchedules = [];
            data.forEach(i => {
                let start = new Date(i.start);
                let end = new Date(i.end);
                let endStr = getDate(addDay(new Date(i.end), 1));
                let workSchedule = {
                    id: i.id,
                    startStr: getDate(start),
                    endStr: endStr,
                    start: start,
                    end: end,
                };

                addWorkSchedule(calendar, workSchedule);
                workSchedulePost.object.workSchedules.push(workSchedule);
            });

            calendar.render();
        });
    })
});

let object = {
    employeeId: null,
    workSchedules: [],
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
                employeeId: null,
                workSchedules: [],
            },
            branch: {
                item: null,
                start: null,
                end: null,
                startTimeStr: null,
                endTimeStr: null,
            },

            start: null,
            end: null,
            canEdit: false,
        }
    },
    created() {
        this.resetObject();
        vueConfig.$once('loaded', (data) => {
            this.branch = data.branch;
            this.canEdit = Boolean(data.roles.includes("ROLE_WORK_SCHEDULE_EDIT"));

            let array = location.href.split('/', 6);
            let id = array[5];

            if (id !== undefined) {
                this.object.employeeId = id;
                this.start = new Date();
                this.start.setDate(1);
                this.start.setMinutes(0);
                this.start.setHours(0);
                this.start.setSeconds(0);
                this.end = lastday(this.start.getFullYear(), this.start.getMonth());
            }
            this.$emit('configLoaded', data);
        });
    },
    watch: {
        end() {
            this.loadWorkSchedules(this.start, this.end)
        },
    },
    methods: {
        loadWorkSchedules(start, end) {
            get(`/api/workSchedule/list/${this.object.employeeId}?start=${start.getTime()}&end=${end.getTime()}`, (r) => {
                if (r.success === true) {
                    this.$emit('workSchedulesLoaded', r.items);
                }
            });
        },
        send() {
            let data = this.object;
            data.range = {
                start: this.start,
                end: this.end
            };

            post('/api/workSchedule/post/collection', data, (result) => {
                if (result.success === true) {
                    workSchedulePost.$refs.alert.addAlert('График сотрудника обновлён', 'success');
                }
            });
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
            this.$emit('selectedTemplate', this.object.template);
        },
    },
    delimiters: ['${', '}$'],
});