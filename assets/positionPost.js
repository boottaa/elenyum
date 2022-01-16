import './app';
import "./src/baseCalendar";
import Vue from 'vue';
import {isValid} from "./validator/validator";
import './src/vueRoleSelect';
//https://www.npmjs.com/package/vue2-datepicker
import {vueAlert} from "./src/vueAlert";
import {get, post} from "./src/baseQuery";

let object = {
    inCalendar: null,
    title: null,
    roles: null,
};

let positionPost = new Vue({
    components: { vueAlert },
    el: '#positionPost',
    data() {
        return {
            roles: null,
            object: {
                inCalendar: null,
                title: null,
                roles: null,
            },
        }
    },
    created() {
        this.resetObject();

        let array = location.href.split('/', 6);
        let id = array[5];

        if (id !== undefined) {
            get('/api/position/get/' + id, (r) => {
                if(r.success === true) {
                    this.object = r.item;
                }
            });
        }
    },
    methods: {
        validation() {
            let items = {
                '#inCalendar': {
                    value: this.object.inCalendar,
                    validators: ['notEmpty'],
                },
                '#title': {
                    value: this.object.title,
                    validators: ['notEmpty'],
                },
                '#roles': {
                    value: this.object.roles,
                    validators: ['notEmpty'],
                },
            };
            return isValid(items);
        },
        send() {
            if (this.validation()) {
                post('/api/position/post', this.object, (result) => {
                    if (result.success === true) {
                        positionPost.$refs.alert.addAlert('Должность добавлена', 'success');
                        this.resetObject();
                    }
                });
            }
        },
        loadedRoles(roles) {
            this.roles = roles

            let r = [];
            this.object.roles.map((item, index) => {
                this.roles.forEach(i => {
                    if (i.id?.toString() === item) {
                        r.push({
                            id: i.id,
                            title: i.title,
                            description: i.description
                        });
                    }
                });
            });

            this.object.roles = r;
        },

        resetObject() {
            this.object = Object.assign({}, object);
        },
    },
    delimiters: ['${', '}$'],
});