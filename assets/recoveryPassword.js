import './bootstrap';
import Vue from 'vue';
import {isValid} from "./validator/validator";
import {post} from "./src/baseQuery";

let object = {
    password: null,
    repeatPassword: null,
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
    mounted() {
        this.$root.$on('show', function () {
            this.show()
        });
    },
    methods: {
        show() {
            this.elModal = document.getElementById('modalAlert');
            this.modal = new bootstrap.Modal(this.elModal);
            this.modal.show();

            this.elModal.addEventListener('hidden.bs.modal', function (e) {
                location.href = '/login';
            });
        }
    },
    delimiters: ['${', '}$'],
});

new Vue({
    el: '#recoveryPassword',
    data() {
        return {
            object: {
                password: null,
                repeatPassword: null,
            },
        }
    },
    created() {
        this.resetObject();
    },
    methods: {
        validation() {
            let items = {
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
            };
            return isValid(items);
        },
        send() {
            if (this.validation()) {

                let data = JSON.parse(JSON.stringify(this.object, (key, value) => {
                    return value
                }));
                let getParams = document.location.toString().split('?')[1];
                post('/api/recoveryPassword?' + getParams, data, (result) => {
                    if (result.success === true) {
                        vueModal.message = result.message;
                        vueModal.$root.$emit('show');
                        vueModal.$once('hidden', () => {
                            this.resetObject();
                            window.location.href = '/login#recoveryd';
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