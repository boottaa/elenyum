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

    vueConfig.$once('loaded', (data) => {
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
                    text: 'месяц',
                    click: () => {
                        changeView('dayGridMonth')
                    },
                },
                meResourceTimeGridWeek: {
                    text: 'неделя',
                    click: () => {
                        changeView('resourceTimeGridWeek')
                    },
                },
                meResourceTimeGridDay: {
                    text: 'день',
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
                today: 'сегодня',
                month: 'месяц',
                week: 'неделя',
                day: 'день',
                list: 'список'
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
                let event = eventClickEvent.event;
                modalVue.object = {
                    id: parseInt(event.id),
                    client: event.extendedProps.client,
                    resourceId: parseInt(event.getResources()[0].id),
                    start: event.start,
                    end: event.end,

                    operations: event.extendedProps.operations,
                    //Тип оплаты
                    paymentType: event.extendedProps.paymentType ?? null,
                    //Сколько оплачено наличкой(сумма)
                    paymentCash: event.extendedProps.paymentCash ?? null,
                    //Оплачено картой (сумма)
                    paymentCard: event.extendedProps.paymentCard ?? null,
                    // Статус события
                    status: event.extendedProps.status,
                };
                modalEvent.show();

                modalVue.$once('send', (data) => {
                    if (data !== null) {
                        calendar.getEventById(data.id)?.remove();
                        postEvent(data);

                        modalEvent.hide();
                    }
                });

                modalVue.$once('remove', (data) => {
                    if (data !== null && data.id !== null) {
                        modalEvent.hide();
                        removeEvent(data.id);
                    }
                })
            },
            dateClick: function (eventClickEvent) {
                modalEvent.show();
                modalVue.object.start = eventClickEvent.date;
                modalVue.object.end = new Date(eventClickEvent.date.toString()).addMinutes(60);
                modalVue.object.resourceId = parseInt(eventClickEvent.resource.id);

                modalVue.$once('send', (data) => {
                    if (data !== null) {
                        modalEvent.hide();
                        postEvent(data);
                    }
                });
            },
            eventDrop: function (info) {
                if (!confirm("Вы уверены что хотите изменить запись?")) {
                    info.revert();
                } else {
                    console.log(info);
                    // Тут нужно внести изменние в событие
                }
            },
            eventResize: function (info) {
                if (!confirm("Вы уверены что хотите изменить время?")) {
                    info.revert();
                } else {
                    console.log(info);
                    // Тут нужно внести изменние в событие
                }
            },
            resourceLabelDidMount: function (info) {
                return info.resource.extendedProps.name;
            },
            resourceAreaColumns: [
                {
                    field: 'title',
                    headerContent: 'ФИО сотрудника'
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
                        operations += `<span class="eventOption">${operation.title} (${operation.price} руб.) x${operation.count}</span> <br>`;
                    });

                    let priceClass = 'bg-warning';
                    if (e.event.extendedProps.paymentType === 1) {
                        priceClass = 'bg-primary';
                    } else if (e.event.extendedProps.paymentType === 2) {
                        priceClass = 'bg-success';
                    } else if (e.event.extendedProps.paymentType === 3) {
                        priceClass = 'bg-info';
                    }
                    console.log();
                    operations += `<div style="position: absolute; right: 1px; bottom: 1px;" class="totalPrice badge rounded-pill ${priceClass}">${totalPrice} руб.</div>`
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
                    //Подтвердил
                    backgroundColor = 'rgb(176 192 255)';
                    break;
                case 2:
                    //Не Подтвердил
                    backgroundColor = 'rgb(236 184 184)';
                    break;
                case 3:
                    //Пришёл
                    backgroundColor = 'rgb(187 236 189)';
                    break;
                case 4:
                    //Не Пришёл
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
                        calendar.addEvent(prepareCalendarEvent(item));
                    })
                }
            });
        }

        function loadEmployee(start, end) {
            $.get(`/api/employee/listForCalendar?start=${start}&end=${end}`, (data) => {
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



                    $(document).trigger("loadedListForCalendar");
                }
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
         */
        function postEvent(event) {
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
                        calendar.addEvent(prepareCalendarEvent(data.item));
                    }
                }
            });
        }

        calendar.gotoDate(new Date($.cookie('currentDate')));
        $(document).on("loadedListForCalendar", function () {
            if (calendar.getResources().length > 0 && data.branch.startTimeStr !== data.branch.endTimeStr) {
                calendar.render();
            } else if (calendar.getResources().length < 0) {
                $('#calendar').append('<p>Нет сотрудников отображаемых в календаре, вы можете добавить <a class="text-muted" style="color: #008fff !important;" href="/position/post">должность</a> и <a class="text-muted" style="color: #008fff !important;" href="/employee/post">сотрудника</a></p>');
            } else if (data.branch.startTimeStr === data.branch.endTimeStr) {
                $('#calendar').append('<p>Время работы филиала настроено не корректно <a class="text-muted" style="color: #008fff !important;" href="/branch/setting">настроить</a>');
            }
        });


        baseCalendar.$on('dateChange', (date) => {
            calendar.gotoDate(date);
            changeView(calendar.view.type);
        });
    });
});