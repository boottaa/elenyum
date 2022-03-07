import './app';
import "./src/baseCalendar";
import Vue from 'vue';
import {isValid} from "./validator/validator";
import './src/vueRoleSelect';
import './src/vueOperationSelect';
//https://www.npmjs.com/package/vue2-datepicker
import {vueAlert} from "./src/vueAlert";
import {get, post, put} from "./src/baseQuery";

let object = {
    id: null,
    inCalendar: null,
    title: null,
    roles: null,
    operations: [],
};

let positionPost = new Vue({
    components: { vueAlert },
    el: '#positionPost',
    data() {
        return {
            roles: null,
            operations: null,
            object: {
                id: null,
                inCalendar: null,
                title: null,
                roles: null,
                operations: [],
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

                let result = Object.assign({}, this.object);
                result.operations = this.object.operations.map(i => {return i.id});
                if (this.object.id === null) {
                    post('/api/position/post', result, (result) => {
                        if (result.success === true) {
                            positionPost.$refs.alert.addAlert('Должность добавлена', 'success');
                            this.resetObject();
                        }
                    });
                } else {
                    put('/api/position/put', result.id, result, (result) => {
                        if (result.success === true) {
                            positionPost.$refs.alert.addAlert('Должность обновлена', 'success');
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
                    if (i.id === item) {
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


        loadedOperations(data) {
            this.operations = data.items

            let r = [];
            this.object.operations.map((item, index) => {
                this.operations.forEach(i => {
                    if (i.id === item) {
                        r.push({
                            id: i.id,
                            title: i.title
                        });
                    }
                });
            });

            this.object.operations = r;
        },

        resetObject() {
            this.object = Object.assign({}, object);
        },
    },
    delimiters: ['${', '}$'],
});