import './app';
import "./src/baseCalendar";
import Vue from 'vue';
import {get} from "./src/baseQuery";
import {vueListTable} from "./src/vueListTable";

let employeeList = new Vue({
    components: {vueListTable},
    el: '#employeeList',
    data() {
        return {
            headers: [
                {text: '№', system: 'id'},
                // {text: 'Картинка', system: 'img'},
                {text: 'Имя', system: 'name'},
                {text: 'Должность', system: 'position'},
                {text: 'Email', system: 'email'},
                {text: 'Телефон', system: 'phone'},
                {text: 'Дополнительный номер телефона', system: 'additionalPhone'},
                {text: 'Дата рождения', system: 'dateBrith'},
            ],
            items: [],
        }
    },
    created() {
        this.send();
    },
    methods: {
        send() {
            get('/api/employee/list', (result) => {
                if (result.success === true) {
                    this.items = result.items;
                }
            });
        },
    },
    delimiters: ['${', '}$'],
});