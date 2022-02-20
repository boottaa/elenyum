import './app';
import "./src/baseCalendar";
import Vue from 'vue';
import {isValid} from "./validator/validator";
import './src/vueRoleSelect';
//https://www.npmjs.com/package/vue2-datepicker
import {vueAlert} from "./src/vueAlert";
import {get, post} from "./src/baseQuery";

let object = {
    title: null,
    price: null,
    duration: null,
};

let operationPost = new Vue({
    components: { vueAlert },
    el: '#operationPost',
    data() {
        return {
            roles: null,
            object: {
                title: null,
                price: null,
                duration: null,
            },
        }
    },
    created() {
        this.resetObject();

        let array = location.href.split('/', 6);
        let id = array[5];

        if (id !== undefined) {
            get('/api/operation/get/' + id, (r) => {
                if(r.success === true) {
                    this.object = r.item;
                }
            });
        }
    },
    methods: {
        validation() {
            let items = {
                '#title': {
                    value: this.object.title,
                    validators: ['notEmpty'],
                },
                '#price': {
                    value: this.object.price,
                    validators: ['notEmpty'],
                },
                '#duration': {
                    value: this.object.duration,
                    validators: ['notEmpty'],
                },
            };
            return isValid(items);
        },
        send() {
            if (this.validation()) {
                post('/api/operation/post', this.object, (result) => {
                    if (result.success === true) {
                        operationPost.$refs.alert.addAlert('Услуга сохранена', 'success');
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