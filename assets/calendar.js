import './bootstrap';
import './app';
import './styles/app.css';
import './styles/calendar.css';

import {Calendar} from '@fullcalendar/core';
import interactionPlugin from "@fullcalendar/interaction";
import bootstrapPlugin from '@fullcalendar/bootstrap';
import resourceTimeGridPlugin from '@fullcalendar/resource-timegrid';
import scrollGridPlugin from '@fullcalendar/scrollgrid';
import dayGridPlugin from '@fullcalendar/daygrid';
import {modalVue} from "./calendar/modalEvent";
import {baseCalendar} from "./src/baseCalendar";
import {vueConfig} from "./src/vueConfig";
import {get} from "./src/baseQuery";

$(function () {
    let elModalEvent = document.getElementById('modalEvent'),
        modalEvent = new bootstrap.Modal(elModalEvent);

    let calendarEl = document.getElementById('calendar');

    elModalEvent.addEventListener('hidden.bs.modal', function () {
        modalVue.resetObject();
    });

    let showEvent = {
        show: function (event) {
            let e = $(event.el),
                eventOperations = e.find('.eventOperations'),
                eh = e.height() + 2,
                eventHoverShowOperations = e.find('.eventHoverShowOperations'),
                h = 21 + (21 * eventOperations.children('span').length);

            if (eventHoverShowOperations.length > 0 && eventOperations.css('display') === 'none') {
                eventHoverShowOperations.one('click', (event) => {
                    e.parent().css("z-index", 9999);
                    eventOperations.show();
                    e.animate({height: h}, 200);
                    event.stopPropagation();
                });
                e.one('mouseleave', () => {
                    setTimeout(() => {
                        e.parent().css("z-index", 1);
                        eventOperations.hide();
                        e.animate({height: eh}, 100);
                    }, 300);
                });
            }
        },
    };

    vueConfig.$on('loaded', (data) => {
        function getDate(date) {
            return date.getDate().toString().padStart(2, '0') + '.' + String(date.getMonth() + 1).padStart(2, '0') + '.' + date.getFullYear().toString();
        }

        let calendar = new Calendar(calendarEl, {
            plugins: [resourceTimeGridPlugin, bootstrapPlugin, scrollGridPlugin, dayGridPlugin, interactionPlugin],
            locale: 'ru',
            height: 'auto',
            dayMinWidth: 220,
            slotDuration: '00:15:00',
            headerToolbar: {
                left: 'meDayGridMonth,meResourceTimeGridWeek,meResourceTimeGridDay',
                center: 'title',
                right: 'today,prev,next'
            },
            viewDidMount: function () {
                $('.fc-prev-button, .fc-next-button, .fc-today-button').on('click', () => {
                    changeView(calendar.view.type);
                });
            },
            customButtons: {
                meDayGridMonth: {
                    text: '??????????',
                    click: () => {
                        changeView('dayGridMonth')
                    },
                },
                meResourceTimeGridWeek: {
                    text: '????????????',
                    click: () => {
                        changeView('resourceTimeGridWeek')
                    },
                },
                meResourceTimeGridDay: {
                    text: '????????',
                    click: () => {
                        changeView('resourceTimeGridDay')
                    },
                },
            },
            editable: true,
            droppable: true,
            allDaySlot: false,
            eventTextColor: '#555555',
            stickyFooterScrollbar: true,
            buttonText: {
                today: '??????????????',
                month: '??????????',
                week: '????????????',
                day: '????????',
                list: '????????????'
            },
            drop: function (info) {
                console.log(info);
            },
            initialView: 'resourceTimeGridDay',//'dayGridMonth',//'resourceTimeGridDay',

            resourceLabelContent: function (renderInfo) {
                let divEl = document.createElement('div'),
                    resourceImg = renderInfo.resource.extendedProps.img === null ? '/img/defaultEmployee.png' : renderInfo.resource.extendedProps.img;

                let name = renderInfo.resource.extendedProps.name,
                    fio = name.split(' ');
                if (fio.length > 1) {
                    name = fio[0] + ' ' + fio[1].substr(0, 1) + '.';
                }
                divEl.innerHTML += `<div style="font-size: 20px" class="resourceName mr-2 d-lg-inline text-gray-600"><span title="${renderInfo.resource.extendedProps.name}">${name}<span> &nbsp;
                                    <img style="width: 30px; vertical-align: -3px;" src="${resourceImg}" class="img-profile rounded-circle" alt="${renderInfo.resource.extendedProps.name}">
                                </div>`;
                let arrayOfDomNodes = [divEl];
                return {domNodes: arrayOfDomNodes};
            },

            eventClick: (eventClickEvent) => {
                if ($(eventClickEvent.el).hasClass('loadEvent')) {
                    return;
                }
                let event = eventClickEvent.event,
                    currentDate = getDate(event.start);

                //?????? ?????????? ??????-???? ???????????????? workSchedules ???????????????? ??????????????
                modalVue.todayResourceWork = event.extendedProps.employee.workSchedules.find(i => {
                    return getDate(new Date(i.start)) === currentDate
                });

                if (typeof modalVue.todayResourceWork !== "object") {
                    return;
                }

                modalVue.calendarEvents = calendar.getEvents();
                modalVue.object = {
                    id: parseInt(event.id),
                    client: event.extendedProps.client,
                    resourceId: parseInt(event.getResources()[0].id),
                    start: event.start,
                    end: event.end,
                    employee: event.extendedProps.employee,
                    operations: event.extendedProps.operations,
                    //?????? ????????????
                    paymentType: event.extendedProps.paymentType ?? null,
                    //?????????????? ???????????????? ????????????????(??????????)
                    paymentCash: event.extendedProps.paymentCash ?? null,
                    //???????????????? ???????????? (??????????)
                    paymentCard: event.extendedProps.paymentCard ?? null,
                    // ???????????? ??????????????
                    status: event.extendedProps.status,
                };
                modalEvent.show();

                modalVue.$once('send', (data) => {
                    if (data !== null) {
                        postEvent(data, eventClickEvent.el);

                        modalEvent.hide();
                    }
                });

                //???????? ???????????? ???????????? ??????????????
                modalVue.$once('remove', (data) => {
                    if (data !== null && data.id !== null) {
                        modalEvent.hide();
                        removeEvent(data.id);
                    }
                })
            },
            dateClick: function (eventClickEvent) {
                let currentDate = getDate(eventClickEvent.date);

                modalVue.todayResourceWork = eventClickEvent.resource.extendedProps.workSchedules.find(i => {
                    return getDate(new Date(i.start)) === currentDate
                });

                if (typeof modalVue.todayResourceWork !== "object") {
                    return;
                }

                if (!(eventClickEvent.date >= new Date(modalVue.todayResourceWork.start) && eventClickEvent.date < new Date(modalVue.todayResourceWork.end))) {
                    return;
                }

                modalEvent.show();
                modalVue.object.start = eventClickEvent.date;
                modalVue.object.end = new Date(eventClickEvent.date.toString()).addMinutes(60);
                modalVue.object.resourceId = parseInt(eventClickEvent.resource.id);
                modalVue.calendarEvents = calendar.getEvents();

                modalVue.$once('send', (data) => {
                    if (data !== null) {
                        modalEvent.hide();
                        postEvent(data);
                    }
                });
            },
            eventDrop: function (info) {
                if (!confirm("???? ?????????????? ?????? ???????????? ???????????????? ?????????????")) {
                    info.revert();
                } else {
                    let event = info.event,
                        currentDate = getDate(event.start),
                        workSchedules = info.newResource === null ?
                            event.extendedProps.employee.workSchedules : info.newResource.extendedProps.workSchedules;

                    if (info.newResource !== null &&
                        info.newResource.extendedProps.position.id !== info.oldResource.extendedProps.position.id)
                    {
                        info.revert();
                        alert('?????????????????? ???????????? ????????????????????, ???????????? ??????????????????');
                        return;
                    }
                    //???????? ?????????????????? ?????? ?????????? ???????????? ?????????? ?????????????????? ????-???? ???????????????? ?????? ?? ????????????
                    modalVue.todayResourceWork = workSchedules.find(i => {
                        return getDate(new Date(i.start)) === currentDate
                    });

                    modalVue.object = {
                        id: parseInt(event.id),
                        client: event.extendedProps.client,
                        resourceId: parseInt(event.getResources()[0].id),
                        start: event.start,
                        end: event.end,
                        employee: event.extendedProps.employee,
                        operations: event.extendedProps.operations,
                        //?????? ????????????
                        paymentType: event.extendedProps.paymentType ?? null,
                        //?????????????? ???????????????? ????????????????(??????????)
                        paymentCash: event.extendedProps.paymentCash ?? null,
                        //???????????????? ???????????? (??????????)
                        paymentCard: event.extendedProps.paymentCard ?? null,
                        // ???????????? ??????????????
                        status: event.extendedProps.status,
                    };
                    modalVue.calendarEvents = calendar.getEvents();
                    if (modalVue.checkResourceWork()) {
                        info.revert();
                        alert('?????????????? ???? ???????????? ??????????, ?????????? ???????????? ?????? ?????????????????? ???????????? ???? ???????????? ?? ?????????????? ?????????? ??????????????????????');
                        return;
                    }
                    if (modalVue.checkSheduleIntersections()) {
                        info.revert();
                        alert('?????????????? ???? ???????????? ??????????, ???????????? ?????????????????????? ???????????? ????????????');
                        return;
                    }

                    let object = JSON.parse(JSON.stringify(modalVue.object));
                    modalVue.resetObject();
                    postEvent(object, info.el);
                }
            },
            eventResize: function (info) {
                if (!confirm("???? ?????????????? ?????? ???????????? ???????????????? ???????????")) {
                    info.revert();
                } else {
                    let event = info.event,
                        currentDate = getDate(event.start);
                    modalVue.todayResourceWork = event.extendedProps.employee.workSchedules.find(i => {
                        return getDate(new Date(i.start)) === currentDate
                    });
                    modalVue.object = {
                        id: parseInt(event.id),
                        client: event.extendedProps.client,
                        resourceId: parseInt(event.getResources()[0].id),
                        start: event.start,
                        end: event.end,
                        employee: event.extendedProps.employee,
                        operations: event.extendedProps.operations,
                        //?????? ????????????
                        paymentType: event.extendedProps.paymentType ?? null,
                        //?????????????? ???????????????? ????????????????(??????????)
                        paymentCash: event.extendedProps.paymentCash ?? null,
                        //???????????????? ???????????? (??????????)
                        paymentCard: event.extendedProps.paymentCard ?? null,
                        // ???????????? ??????????????
                        status: event.extendedProps.status,
                    };
                    modalVue.calendarEvents = calendar.getEvents();
                    if (modalVue.checkResourceWork()) {
                        info.revert();
                        alert('?????????????? ???? ???????????? ??????????, ?????????? ???????????? ?????? ?????????????????? ???????????? ???? ???????????? ?? ?????????????? ?????????? ??????????????????????');
                        return;
                    }

                    if (modalVue.checkSheduleIntersections()) {
                        info.revert();
                        alert('?????????????? ???? ???????????? ??????????, ???????????? ?????????????????????? ???????????? ????????????');
                        return;
                    }

                    let object = JSON.parse(JSON.stringify(modalVue.object));
                    modalVue.resetObject();
                    postEvent(object, info.el);
                }
            },
            resourceLabelDidMount: function (info) {
                return info.resource.extendedProps.name;
            },
            resourceAreaColumns: [
                {
                    field: 'title',
                    headerContent: '?????? ????????????????????'
                }
            ],
            views: {
                resourceTimeGridDay: {
                    slotLabelFormat: [
                        {hour12: false, hour: '2-digit', minute: '2-digit'},
                    ],
                    slotMinTime: data.branch.startTimeStr,
                    slotMaxTime: data.branch.endTimeStr,
                },
                resourceTimeGridWeek: {
                    slotLabelFormat: [
                        {hour12: false, hour: '2-digit', minute: '2-digit'},
                    ],
                    slotMinTime: data.branch.startTimeStr,
                    slotMaxTime: data.branch.endTimeStr,
                },
            },
            eventContent: function (e) {
                let divEl = document.createElement('div');
                divEl.className = 'eventBlock';

                let userFullName = e.event.extendedProps.client.name;

                let name = userFullName;
                if (userFullName.length > 10) {
                    name = userFullName.substring(0, 10) + "..."
                }

                if (e.event.extendedProps.client) {
                    divEl.innerHTML += `
                <div class="eventUserInfo">
                    <span title="${userFullName}">${name}</span>
                    <span style="color: #2a84e1">${e.event.extendedProps.client.phone}</span>
                </div>
                `;
                }

                let diffMins = parseInt((e.event.end.getTime() - e.event.start.getTime()) / 60000); // minutes
                if (e.event.extendedProps.operations) {
                    let invisible = '';
                    let slotDurationMinutes = 15;
                    if (diffMins < (slotDurationMinutes * e.event.extendedProps.operations.length) + slotDurationMinutes) {
                        invisible = 'display: none;';
                        divEl.innerHTML += `<span class="eventHoverShowOperations"
                                        style="padding: 2px 10px; position: absolute; top:0; right: 0;">
                                            <i class="fas fa-caret-down"></i>
                                        </span>`;
                    }

                    let totalPrice = 0,
                        operations = `<div class="eventOperations mt-1" style="${invisible}">`;

                    e.event.extendedProps.operations.forEach((operation) => {
                        totalPrice += operation.price * operation.count;
                        operations += `<span class="eventOption">${operation.title} (${operation.price} ??????.) x${operation.count}</span> <br>`;
                    });

                    let priceClass = 'bg-warning';
                    if (e.event.extendedProps.paymentType === 1) {
                        priceClass = 'bg-primary';
                    } else if (e.event.extendedProps.paymentType === 2) {
                        priceClass = 'bg-success';
                    } else if (e.event.extendedProps.paymentType === 3) {
                        priceClass = 'bg-info';
                    }

                    operations += `<div style="position: absolute; right: 1px; bottom: 1px;" class="totalPrice badge rounded-pill ${priceClass}">${totalPrice} ??????.</div>`
                    operations += '</div>';

                    divEl.innerHTML += operations;
                }

                let arrayOfDomNodes = [divEl];
                return {domNodes: arrayOfDomNodes};
            },
            eventMouseEnter: (event) => {
                showEvent.show(event)
            },
        });

        calendar.gotoDate(new Date($.cookie('currentDate')));

        let start = calendar.view.currentStart.getTime();
        let end = calendar.view.currentEnd.getTime();

        /**
         * @param item
         * @returns {{paymentCard: (null|any), resourceId, operations: [], start, client: {phone: (null|*), name: (null|*), id: (null|*), status: (boolean|*)}, end, id, paymentCash: (null|any), paymentType: (boolean|*), status}}
         */
        function prepareCalendarEvent(item) {
            let operations = [],
                backgroundColor = null;

            switch (item.status) {
                case 1:
                    //????????????????????
                    backgroundColor = 'rgb(176 192 255)';
                    break;
                case 2:
                    //???? ????????????????????
                    backgroundColor = 'rgb(236 184 184)';
                    break;
                case 3:
                    //????????????
                    backgroundColor = 'rgb(187 236 189)';
                    break;
                case 4:
                    //???? ????????????
                    backgroundColor = 'rgb(240 230 191)';
                    break;
                default:
                    backgroundColor = 'rgb(209 215 241)';
            }
            item.sheduleOperations.forEach((sheduleOperation) => {
                operations.push({
                    id: sheduleOperation.operation.id,
                    duration: sheduleOperation.operation.duration,
                    price: sheduleOperation.operation.price,
                    title: sheduleOperation.operation.title,
                    count: sheduleOperation.count,
                });
            });

            return {
                id: item.id,
                start: item.start,
                end: item.end,
                operations: operations,
                client: {
                    id: item.client.id,
                    name: item.client.name,
                    phone: item.client.phone,
                    status: item.client.status
                },
                employee: {
                    id: item.employee.id,
                    workSchedules: item.employee.workSchedules
                },
                resourceId: item.employee.id,
                paymentType: item.paymentType,
                paymentCash: item.paymentCash,
                paymentCard: item.paymentCard,
                status: item.status,
                backgroundColor: backgroundColor,
            };
        }

        function loadEvents(start, end) {
            $.get(`/api/shedule/list?start=${start}&end=${end}`, (data) => {
                if (data.total > 0) {
                    data.items.forEach(function (item) {
                        calendar.getEventById(item.id)?.remove();
                        calendar.addEvent(prepareCalendarEvent(item));
                    })
                }
            });
        }

        function loadEmployee(start, end) {
            calendar.getResources().map(r => r.remove());
            calendar.destroy();

            get(`/api/employee/listForCalendar?start=${start}&end=${end}`, (data) => {
                if (data.total > 0) {
                    data.items.forEach(function (item) {
                        item.businessHours = [];
                        item.workSchedules.forEach(function (ws) {
                            let start = new Date(ws.start),
                                end = new Date(ws.end),
                                startTime = start.getHours().toString().padStart(2, '0') + ':' + start.getMinutes().toString().padStart(2, '0'),
                                endTime = end.getHours().toString().padStart(2, '0') + ':' + end.getMinutes().toString().padStart(2, '0');

                            item.businessHours.push({
                                daysOfWeek: [start.getDay()],
                                startTime: startTime,
                                endTime: endTime,
                            });
                        });
                        calendar.addResource(item);
                    });
                }

                $(document).trigger("loadedListForCalendar");
            });
        }

        function removeEvent(id) {
            calendar.getEventById(id).remove();
            $.get(`/api/shedule/remove/${id}`);
        }

        loadEmployee(start, end);
        loadEvents(start, end);

        function changeView(view) {
            calendar.changeView(view);
            calendar.removeAllEvents();

            baseCalendar.pickDate = calendar.view.currentStart;
            if (view !== 'dayGridMonth') {
                let start = calendar.view.currentStart.getTime();
                let end = calendar.view.currentEnd.getTime();
                loadEmployee(start, end);
                loadEvents(start, end);
            }
        }

        /**
         * @param event
         * @param eventEl
         */
        function postEvent(event, eventEl) {
            if (eventEl) {
                //?????????????????? ?????????? ???????????????????? ???????? ??????????????
                $(eventEl).addClass('loadEvent');
                $(eventEl).append(`<div class="loaderMask" id="loadEvent${data.id}">
                            <div class="loader" style="margin: 0 auto; top: 30%; font-size: 0.2em; position: relative; display: block;">Loading...</div>
                        </div>`);
            }
            $.ajax({
                type: "POST",
                url: '/api/shedule/post',
                data: JSON.stringify(event, (key, value) => {
                    return value
                }),
                contentType: "application/json",
                dataType: 'json',
                success: function (data) {
                    if (data.success === true) {
                        let start = calendar.view.currentStart.getTime();
                        let end = calendar.view.currentEnd.getTime();
                        loadEvents(start, end);
                    }
                }
            });
        }

        $(document).on("loadedListForCalendar", function () {
            $('.noticeCalendarEmpty').remove();
            if (calendar.getResources().length > 0 && data.branch.startTimeStr !== data.branch.endTimeStr) {
                calendar.render();
            } else if (calendar.getResources().length <= 0) {
                $('#calendar').append('<p class="noticeCalendarEmpty">?????? ?????????????????????? ???????????????????????? ?? ??????????????????, ?? ???????? ???????? ?????????? ???? ???????????????? ?????? ?????? ???????????????????? ?????? ????????????. <a class="text-muted" style="color: #008fff !important;" href="/employee/list">???? ???????????? ?????????????????? ???????????? ????????????</a></p>');
            } else if (data.branch.startTimeStr === data.branch.endTimeStr) {
                $('#calendar').append('<p class="noticeCalendarEmpty">?????????? ???????????? ?????????????? ?????????????????? ???? ?????????????????? <a class="text-muted" style="color: #008fff !important;" href="/branch/setting">??????????????????</a>');
            }
        });


        baseCalendar.$on('dateChange', (date) => {
            calendar.gotoDate(date);
            changeView(calendar.view.type);
        });
    });
});