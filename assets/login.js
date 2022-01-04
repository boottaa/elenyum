import './bootstrap';
import Vue from 'vue';
import {isValid} from "./validator/validator";

let object = {
    email: null,
    password: null
};

new Vue({
    el: '#login',
    data() {
        return {
            object: {
                email: null,
                password: null
            },
        }
    },
    created() {
        this.resetObject();
    },
    methods: {
        validation() {
            let items = {
                '#email': {
                    value: this.object.email,
                    validators: ['notEmpty'],
                },
                '#password': {
                    value: this.object.password,
                    validators: ['notEmpty'],
                },
            };
            return isValid(items);
        },
        send() {
            if (this.validation()) {

                console.log(this.object)
                this.resetObject();
            }
        },

        resetObject() {
            this.object = Object.assign({}, object);
        },
    },
    delimiters: ['${', '}$'],
});