import './app';
import "./src/baseCalendar";
import Vue from 'vue';
import {del, get} from "./src/baseQuery";
import {vueListTable} from "./src/vueListTable";
import {vueAlert} from "./src/vueAlert";

let employeeList = new Vue({
    components: {vueListTable, vueAlert},
    el: '#positionList',
    data() {
        return {
            headers: [
                {text: 'Название', system: 'title'},
                {text: 'Отображать в календаре', system: 'inCalendar'},
            ],
            items: [],
            actions: [
                {
                    value: 'Удалить', type: 'danger', onclick: (e) => {
                        let button = $(e.target);
                        let id = button.attr('data-id');

                        del(
                            `/api/position/delete/${id}`,
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

                        location.href = '/position/post/' + id;
                    }
                },
            ],
        }
    },
    created() {
        this.send();
    },
    methods: {
        send() {
            get('/api/position/list', (result) => {
                if (result.success === true) {
                    result.items.map((i) => {
                        i.inCalendar = i.inCalendar === true ? 'Да' : 'Нет';
                    });

                    this.items = result.items;
                }
            });
        },
    },
    delimiters: ['${', '}$'],
});