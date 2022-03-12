import './app';
import "./src/baseCalendar";
import Vue from 'vue';
import {del} from "./src/baseQuery";
import {vueList} from "./src/vueList";
import {vueAlert} from "./src/vueAlert";

let employeeList = new Vue({
    components: {vueAlert, vueList},
    el: '#clientList',
    data() {
        return {
            url: '/api/client/list',
            headers: [
                {text: 'ФИО', system: 'name'},
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
                            `/api/client/delete/${id}`,
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

                        location.href = '/client/post/' + id;
                    }
                },
            ],
        }
    },
    methods: {
        prepare(data) {
            data.items.map((i) => {
                let st = i.dateBrith;
                if (!!st) {
                    let pattern = /(\d{2})\.(\d{2})\.(\d{4})/;
                    let date = new Date(st.replace(pattern, '$3-$2-$1'));
                    i.dateBrith = this.getDate(date);
                } else {
                    i.dateBrith = '-';
                }

                if (!i.additionalPhone) {
                    i.additionalPhone = '-';
                }
                if (!i.email) {
                    i.email = '-';
                }
            });
        },

        getDate(date) {
            return date.getDate().toString().padStart(2, '0') + '.' + String(date.getMonth() + 1).padStart(2, '0') + '.' + date.getFullYear().toString();
        }
    },
    delimiters: ['${', '}$'],
});