import './bootstrap';
import Vue from 'vue';
import {isValid} from "./validator/validator";

let object = {
    email: null,
};

new Vue({
    el: '#forgotPassword',
    data() {
        return {
            object: {
                email: null,
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