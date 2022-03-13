import './app';
import "./src/baseCalendar";
import Vue from 'vue';
import {isValid} from "./validator/validator";
import './src/vuePositionSelect';
//https://www.npmjs.com/package/vue2-datepicker
import DatePicker from 'vue2-datepicker';
import {vueAlert} from "./src/vueAlert";
import {post, get, put} from "./src/baseQuery";

let object = {
    id: null,
    position: null,
    name: null,
    phone: null,
    email: null,
    additionalPhone: null,
    dateBrith: null,
};

let employeePost = new Vue({
    components: {DatePicker, vueAlert},
    el: '#employeePost',
    data() {
        return {
            object: {
                id: null,
                position: null,
                name: null,
                phone: null,
                email: null,
                additionalPhone: null,
                dateBrith: null,
            },
        }
    },
    created() {
        this.resetObject();

        let array = location.href.split('/', 6);
        let id = array[5];

        if (id !== undefined) {
            get('/api/employee/get/' + id, (r) => {
                if (r.success === true) {
                    this.object.id = r.item.id;
                    this.object.position = r.item.position;
                    this.object.name = r.item.name;
                    this.object.phone = r.item.phone;
                    this.object.email = r.item.email;
                    this.object.additionalPhone = r.item.additionalPhone;

                    let st = r.item.dateBrith;
                    if (!!st) {
                        let pattern = /(\d{2})\.(\d{2})\.(\d{4})/;
                        this.object.dateBrith = new Date(st.replace(pattern, '$3-$2-$1'));
                    }
                }
            });
        }
    },
    methods: {
        validation() {
            let items = {
                '#position': {
                    value: this.object.position,
                    validators: ['notEmpty'],
                },
                '#userName': {
                    value: this.object.name,
                    validators: ['notEmpty', 'fio'],
                },
                '#phone': {
                    value: this.object.phone,
                    validators: ['phone'],
                },
                '#additionalPhone': {
                    value: this.object.additionalPhone,
                    validators: ['phone'],
                },
                '#email': {
                    value: this.object.email,
                    validators: ['email'],
                },
            };
            return isValid(items);
        },
        send() {
            if (this.validation()) {
                if (this.object.id === null) {
                    let result = this.object;
                    result.position = {
                        id: this.object.position.id,
                        title: this.object.position.title
                    }
                    post('/api/employee/post', result, (result) => {
                        if (result.success === true) {
                            this.resetObject();
                            location.href = '/employee/list#added';
                        }
                    });
                } else {
                    let result = this.object;
                    result.position = {
                        id: this.object.position.id,
                        title: this.object.position.title
                    }
                    put('/api/employee/put', result.id, result, (result) => {
                        if (result.success === true) {
                            this.resetObject();
                            location.href = '/employee/list#edited';
                        }
                    });
                }
            }
        },

        resetObject() {
            this.object = Object.assign({}, object);
        },
    },
    delimiters: ['${', '}$'],
});