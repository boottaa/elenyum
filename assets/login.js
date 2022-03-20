import './bootstrap';
import Vue from 'vue';
import { isValid } from "./validator/validator";
import { post, setToken } from "./src/baseQuery";
import {vueAlert} from "./src/vueAlert";

let object = {
    username: null,
    password: null
};

new Vue({
    el: '#login',
    components: { vueAlert },
    data() {
        return {
            object: {
                username: null,
                password: null
            },
        }
    },
    created() {
        this.resetObject();
    },
    mounted() {
        if (location.hash === '#confirmed') {
            this.$refs.alert.addAlert('Учётная запись подтверждена', 'success');
            location.hash = ''
        }
    },
    methods: {
        validation() {
            let items = {
                '#email': {
                    value: this.object.username,
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
                post('/api/login', this.object, (result) => {
                    if (result.success === true) {
                        setToken(result.token);

                        window.location.href = '/';
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