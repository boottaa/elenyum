import './bootstrap';
import Vue from 'vue';
import {isValid} from "./validator/validator";
import {post} from "./src/baseQuery";

let object = {
    email: null,
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
                vueModal.$emit('hidden', e);
            });
        }
    },
    delimiters: ['${', '}$'],
});

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
                    validators: ['notEmpty', 'email'],
                },
            };
            return isValid(items);
        },
        send() {
            if (this.validation()) {

                let data = JSON.parse(JSON.stringify(this.object, (key, value) => {
                    return value
                }));
                post('/api/forgotPassword', data, (result) => {
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