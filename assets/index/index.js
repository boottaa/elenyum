import '../app';
import '../styles/index.css';
import Vue from "vue";
import {post} from "../src/baseQuery";
import {isValid} from "../validator/validator";

new Vue({
    el: '#contactForm',
    data() {
        return {
            url: '/api/system/newClient',
            object: {
                phone: null,
            }
        }
    },
    watch: {
        'object.phone': function () {
            if (this.validation()) {
                $('#submitButton').removeClass('disabled');
            } else {
                $('#submitButton').addClass('disabled');
            }
        }
    },
    methods: {
        validation() {
            let items = {
                '#phone': {
                    value: this.object.phone,
                    validators: ['phone'],
                },
            };
            return isValid(items);
        },

        send() {
            if (this.validation()) {
                post(this.url, this.object, (result) => {
                    if (result.success === true) {
                        let elModal = document.getElementById('modalAlertAddPhone');
                        let modal = new bootstrap.Modal(elModal);
                        modal.show();
                    }
                }, (result) => {
                    console.log(result);
                });
            }
        },
    }
});


new Vue({
    el: '#contactFormFooter',
    data() {
        return {
            url: '/api/employee/list',
            object: {
                phone: null,
            }
        }
    },
    watch: {
        'object.phone': function () {
            if (this.validation()) {
                $('#submitButtonFooter').removeClass('disabled');
            } else {
                $('#submitButtonFooter').addClass('disabled');
            }
        }
    },
    methods: {
        validation() {
            let items = {
                '#phoneFooter': {
                    value: this.object.phone,
                    validators: ['phone'],
                },
            };
            return isValid(items);
        },

        send() {
            if (this.validation()) {

                post(this.url, this.object, (result) => {
                    if (result.success === true) {
                        let elModal = document.getElementById('modalAlertAddPhone');
                        let modal = new bootstrap.Modal(elModal);
                        modal.show();
                    }
                }, (result) => {
                    console.log(result);
                });
            }
        },
    }
});