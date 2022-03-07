import './bootstrap';
import 'jquery.cookie';
import Vue from 'vue';
import {isValid} from "./validator/validator";
import {post} from "./src/baseQuery";

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

let vueModal = new Vue({
    el: '#modalAlert',
    data() {
        return {
            elModal: null,
            modal: null,
            message: '',
        }
    },
    mounted(){
        this.$root.$on('show', function(){
            this.show()
        });
    },
    methods: {
        show() {
            this.elModal = document.getElementById('modalAlert');
            this.modal = new bootstrap.Modal(this.elModal);
            this.modal.show();

            this.elModal.addEventListener('hidden.bs.modal', function (e) {
                this.$emit('hidden', e)
            });
        }
    },
    delimiters: ['${', '}$'],
});

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
                    validators: ['notEmpty', 'fio'],
                },
                '#position': {
                    value: this.object.position,
                    validators: ['notEmpty'],
                },
                '#email': {
                    value: this.object.email,
                    validators: ['email'],
                },
                '#companyName': {
                    value: this.object.companyName,
                    validators: ['notEmpty'],
                },
                '#password': {
                    value: this.object.password,
                    validators: ['password'],
                    repeat: this.object.repeatPassword
                },
                '#repeatPassword': {
                    value: this.object.repeatPassword,
                    validators: ['password'],
                    repeat: this.object.password
                },
                '#address': {
                    value: this.object.address,
                    validators: ['notEmpty'],
                }
            };
            return isValid(items);
        },
        send() {
            if (this.validation()) {

                let data = JSON.parse(JSON.stringify(this.object, (key, value) => {
                    return value
                }));
                post('/api/register', data, (result) => {
                    if (result.success === true) {
                        vueModal.message = result.message;
                        vueModal.$root.$emit('show');
                        vueModal.$once('hidden', () => {
                            this.resetObject();
                            window.location.href = '/login';
                        });
                    } else {
                        vueModal.message = result.message;
                        vueModal.$root.$emit('show');
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