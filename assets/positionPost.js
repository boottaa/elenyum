import './app';
import "./src/baseCalendar";
import Vue from 'vue';
import {isValid} from "./validator/validator";
import './src/vueRoleSelect';
//https://www.npmjs.com/package/vue2-datepicker
import {vueAlert} from "./src/vueAlert";
import {get, post, put} from "./src/baseQuery";

let object = {
    id: null,
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
                id: null,
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
                console.log(this.object.id);
                if (this.object.id === null) {
                    post('/api/position/post', this.object, (result) => {
                        if (result.success === true) {
                            positionPost.$refs.alert.addAlert('Должность добавлена', 'success');
                            this.resetObject();
                        }
                    });
                } else {
                    put('/api/position/put', this.object.id, this.object, (result) => {
                        if (result.success === true) {
                            positionPost.$refs.alert.addAlert('Должность обновлена', 'success');
                            this.resetObject();
                        }
                    });
                }
            }
        },
        loadedRoles(data) {
            this.roles = data.items

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

            console.log(r);

            this.object.roles = r;
        },

        resetObject() {
            this.object = Object.assign({}, object);
        },
    },
    delimiters: ['${', '}$'],
});