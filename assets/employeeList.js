import './app';
import "./src/baseCalendar";
import Vue from 'vue';
import {del, get} from "./src/baseQuery";
import {vueListTable} from "./src/vueListTable";
import {vueAlert} from "./src/vueAlert";
import {vuePaginator} from "./src/vuePaginator";

let employeeList = new Vue({
    components: {vueListTable, vueAlert, vuePaginator},
    el: '#employeeList',
    data() {
        return {
            url: '/api/employee/list',
            total: 0,
            size: 0,
            page: 1,
            headers: [
                // {text: 'Картинка', system: 'img'},
                {text: 'ФИО', system: 'name'},
                {text: 'Должность', system: 'position'},
                {text: 'Email', system: 'email'},
                {text: 'Телефон', system: 'phone'},
                {text: 'Дополнительный номер телефона', system: 'additionalPhone'},
                {text: 'Дата рождения', system: 'dateBrith'},
            ],
            items: [],
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
            ],
        }
    },
    created() {
        this.send();
    },
    methods: {
        send() {
            this.onRequest(this.page);
        },
        onChangePage(page) {
            this.page = page;
            this.onRequest(page);
        },
        onRequest(page) {
            get(this.url + '?page=' + page, (result) => {
                if (result.success === true) {
                    this.total = result.total;
                    this.size = result.size;
                    this.page = result.page;
                    result.items.map((i) => {
                        i.position = i.position.title;
                    });
                    this.items = result.items;
                }
            });
        }
    },
    delimiters: ['${', '}$'],
});