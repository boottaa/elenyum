import './bootstrap';
import Vue from 'vue';
import {isValid} from "./validator/validator";
import {post} from "./baseQuery";

let object = {
    phone: null,
    userName: null,
    position: null,
    email: null,
    companyName: null,
    password: null,
    repeatPassword: null,
    address: null
};

new Vue({
    el: '#registration',
    data() {
        return {
            object: {
                phone: null,
                userName: null,
                position: null,
                email: null,
                companyName: null,
                password: null,
                repeatPassword: null,
                address: null
            },
        }
    },
    created() {
        this.resetObject();
    },
    methods: {
        validation() {
            let items = {
                '#phone': {
                    value: this.object.phone,
                    validators: ['phone'],
                },
                '#userName': {
                    value: this.object.userName,
                    validators: ['notEmpty'],
                },
                '#position': {
                    value: this.object.position,
                    validators: ['notEmpty'],
                },
                '#email': {
                    value: this.object.email,
                    validators: ['notEmpty'],
                },
                '#companyName': {
                    value: this.object.companyName,
                    validators: ['notEmpty'],
                },
                '#password': {
                    value: this.object.password,
                    validators: ['notEmpty'],
                },
                '#address': {
                    value: this.object.address,
                    validators: ['address'],
                }
            };
            return isValid(items);
        },
        send() {
            if (this.validation()) {

                let data = JSON.parse(JSON.stringify(this.object, (key, value) => {
                    return value
                }));
                post('/api/register', data, function (r) {
                    console.log(r);
                });
                this.resetObject();
            }
        },

        resetObject() {
            this.object = Object.assign({}, object);
        },
    },
    delimiters: ['${', '}$'],
});