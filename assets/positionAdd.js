import './app';
import "./src/baseCalendar";
import Vue from 'vue';
import {isValid} from "./validator/validator";
import './src/vueRoleSelect';
//https://www.npmjs.com/package/vue2-datepicker
import {vueAlert} from "./src/vueAlert";
import {post} from "./src/baseQuery";

let object = {
    inCalendar: null,
    title: null,
    roles: null,
};

let positionAdd = new Vue({
    components: { vueAlert },
    el: '#employeeAdd',
    data() {
        return {
            object: {
                inCalendar: null,
                title: null,
                roles: null,
            },
        }
    },
    created() {
        this.resetObject();
    },
    methods: {
        validation() {
            let items = {
                '#inCalendar': {
                    value: this.object.inCalendar,
                    validators: ['notEmpty'],
                },
                '#title': {
                    value: this.object.title,
                    validators: ['notEmpty'],
                },
                '#roles': {
                    value: this.object.roles,
                    validators: ['notEmpty'],
                },
            };
            return isValid(items);
        },
        send() {
            if (this.validation()) {
                post('/api/position/post', this.object, (result) => {
                    if (result.success === true) {
                        positionAdd.$refs.alert.addAlert('Должность добавлена', 'success');
                        this.resetObject();
                    }
                });
            }
        },

        resetObject() {
            this.object = Object.assign({}, object);
        },
    },
    delimiters: ['${', '}$'],
});