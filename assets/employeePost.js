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
    userName: null,
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
                    this.object = r.item;
                    let st = r.item.dateBrith;
                    let pattern = /(\d{2})\.(\d{2})\.(\d{4})/;
                    this.object.dateBrith = new Date(st.replace(pattern, '$3-$2-$1'));
                }
            });
        }
    },
    methods: {
        validation() {
            let items = {
                '#positionId': {
                    value: this.object.position,
                    validators: ['notEmpty'],
                },
                '#userName': {
                    value: this.object.name,
                    validators: ['notEmpty'],
                },
                '#phone': {
                    value: this.object.phone,
                    validators: ['notEmpty'],
                },
                '#email': {
                    value: this.object.email,
                    validators: ['notEmpty'],
                },
                '#dateBrith': {
                    value: this.object.dateBrith,
                    validators: ['notEmpty'],
                },
            };
            return isValid(items);
        },
        send() {
            if (this.validation()) {
                if (this.object.id === null) {
                    post('/api/employee/post', this.object, (result) => {
                        if (result.success === true) {
                            employeePost.$refs.alert.addAlert('Сотрудник добавлен', 'success');
                            setTimeout(() => {
                                document.location = '/employee/list';
                            }, 1000);
                        }
                    });
                } else {
                    put('/api/employee/put', this.object.id, this.object, (result) => {
                        if (result.success === true) {
                            employeePost.$refs.alert.addAlert('Данные сотрудника обновлены', 'success');
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