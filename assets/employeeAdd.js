import './app';
import Vue from 'vue';
import {isValid} from "./validator/validator";
import './src/vuePositionSelect';
//https://www.npmjs.com/package/vue2-datepicker
import DatePicker from 'vue2-datepicker';

let object = {
    positionId: null,
    userName: null,
    phone: null,
    email: null,
    additionalPhone: null,
    password: null,
    dateBrith: null,
};

new Vue({
    components: { DatePicker },
    el: '#employeeAdd',
    data() {
        return {
            object: {
                positionId: null,
                userName: null,
                phone: null,
                email: null,
                additionalPhone: null,
                password: null,
                dateBrith: null,
            },
        }
    },
    created() {
        this.resetObject();
    },
    methods: {
        validation() {
            let items = {
                '#positionId': {
                    value: this.object.positionId,
                    validators: ['notEmpty'],
                },
                '#userName': {
                    value: this.object.userName,
                    validators: ['notEmpty'],
                },
                '#phone': {
                    value: this.object.phone,
                    validators: ['notEmpty'],
                },
                '#email': {
                    value: this.object.email,
                    validators: ['notEmpty'],
                },
                '#additionalPhone': {
                    value: this.object.additionalPhone,
                    validators: ['notEmpty'],
                },
                '#password': {
                    value: this.object.password,
                    validators: ['notEmpty'],
                },
                '#dateBrith': {
                    value: this.object.dateBrith,
                    validators: ['notEmpty'],
                },
            };
            return isValid(items);
        },
        send() {
            if (this.validation()) {
                post('/api/employee/post', this.object, (result) => {
                    if (result.success === true) {

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