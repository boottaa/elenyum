import './app';
import "./src/baseCalendar";
import Vue from 'vue';
import {del, get} from "./src/baseQuery";
import {vueList} from "./src/vueList";
import {vueAlert} from "./src/vueAlert";

let employeeList = new Vue({
    components: {vueAlert, vueList},
    el: '#employeeList',
    data() {
        return {
            url: '/api/employee/list',
            headers: [
                // {text: 'Картинка', system: 'img'},
                {text: 'ФИО', system: 'name'},
                {text: 'Должность', system: 'position'},
                {text: 'Email', system: 'email'},
                {text: 'Телефон', system: 'phone'},
                {text: 'Дополнительный номер телефона', system: 'additionalPhone'},
                {text: 'Дата рождения', system: 'dateBrith'},
            ],
            actions: [
                {
                    value: 'Удалить', type: 'danger', onclick: (e) => {
                        let button = $(e.target);
                        let id = button.attr('data-id');

                        del(
                            `/api/employee/delete/${id}`,
                            (r) => {
                                if (r.success === true) {
                                    button.parents('tr').remove();
                                } else {
                                    employeeList.$refs.alert.addAlert(r.message, 'danger');
                                }
                            },
                            (e) => {
                                employeeList.$refs.alert.addAlert(e.message, 'danger');
                            }
                        );
                    }
                },
                {
                    value: 'Редактировать', type: 'info', onclick: (e) => {
                        let button = $(e.target);
                        let id = button.attr('data-id');

                        location.href = '/employee/post/' + id;
                    }
                },
                {
                    value: 'График работы', type: 'success', onclick: (e) => {
                        let button = $(e.target);
                        let id = button.attr('data-id');

                        location.href = '/workSchedule/post/' + id;
                    }
                },
            ],
        }
    },
    methods: {
        prepare(data) {
            data.items.map((i) => {
                i.position = i.position.title;
                if (!!i.dateBrith) {
                    let st = i.dateBrith;
                    if (!!st) {
                        let pattern = /(\d{2})\.(\d{2})\.(\d{4})/;
                        let date = new Date(st.replace(pattern, '$3-$2-$1'));
                        i.dateBrith = this.getDate(date);
                    }
                    // console.log(i.dateBrith.replace(pattern, '$3-$2-$1'));
                    // i.dateBrith = i.dateBrith.replace(pattern, '$3-$2-$1')
                }
            });
        },

        getDate(date) {
            return date.getDate().toString().padStart(2, '0') + '.' + String(date.getMonth() + 1).padStart(2, '0') + '.' + date.getFullYear().toString();
        }
    },
    delimiters: ['${', '}$'],
});