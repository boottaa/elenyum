import './app';
import "./src/baseCalendar";
import Vue from 'vue';
import {del} from "./src/baseQuery";
import {vueList} from "./src/vueList";
import {vueAlert} from "./src/vueAlert";

let operationList = new Vue({
    components: {vueList, vueAlert},
    el: '#operationList',
    data() {
        return {
            url: '/api/operation/list',
            headers: [
                {text: 'Название', system: 'title'},
                {text: 'Цена', system: 'price'},
                {text: 'Длительность', system: 'duration'},
            ],
            actions: [
                {
                    value: 'Удалить', type: 'danger', onclick: (e) => {
                        let button = $(e.target);
                        let id = button.attr('data-id');

                        del(
                            `/api/operation/delete/${id}`,
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

                        location.href = '/operation/post/' + id;
                    }
                },
            ],
        }
    },
    methods: {
        prepare(data) {
            data.items.map((i) => {
                i.inCalendar = i.inCalendar === true ? 'Да' : 'Нет';
                let time = new Date();
                time.setHours(0);
                time.setMinutes(0);
                time.setSeconds(0);
                time = new Date(time.getTime() + i.duration * 60000);

                i.duration = time.getHours().toString().padStart(2, '0') + ':' + time.getMinutes().toString().padStart(2, '0');
            });
        }
    },
    delimiters: ['${', '}$'],
});