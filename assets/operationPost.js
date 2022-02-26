import './app';
import "./src/baseCalendar";
import Vue from 'vue';
import {isValid} from "./validator/validator";
import './src/vueRoleSelect';
//https://www.npmjs.com/package/vue2-datepicker
import {vueAlert} from "./src/vueAlert";
import {get, post, put} from "./src/baseQuery";
import DatePicker from 'vue2-datepicker';

let object = {
    id: null,
    title: null,
    price: null,
    duration: null,
};

let operationPost = new Vue({
    components: { vueAlert, DatePicker },
    el: '#operationPost',
    data() {
        return {
            roles: null,
            object: {
                id: null,
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
                    let time = new Date();
                    time.setHours(0);
                    time.setMinutes(0);
                    time.setSeconds(0);

                    this.object = r.item;
                    this.object.duration = new Date(time.getTime() + r.item.duration * 60000);
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
                let time = new Date(this.object.duration.getTime()),
                    result = Object.assign({}, this.object);

                time.setHours(0);
                time.setMinutes(0);
                time.setSeconds(0);

                result.duration = (this.object.duration.getTime() - time.getTime()) / 60000;

                if (this.object.id === null) {
                    post('/api/operation/post', result, (result) => {
                        if (result.success === true) {
                            operationPost.$refs.alert.addAlert('Услуга сохранена', 'success');
                            this.resetObject();
                        }
                    });
                } else {
                    put('/api/operation/put', this.object.id, result, (result) => {
                        if (result.success === true) {
                            operationPost.$refs.alert.addAlert('Услуга сохранена', 'success');
                        }
                    });
                }
            }
        },

        resetObject() {
            this.object = Object.assign({}, object);
        },
    },
    delimiters: ['${', '}$'],
});