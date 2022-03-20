import './app';
import "./src/baseCalendar";
import Vue from 'vue';

//https://www.npmjs.com/package/vue2-datepicker
import {vueAlert} from "./src/vueAlert";
import {get, post, put} from "./src/baseQuery";
import {vueWorkShedule} from "./src/vueWorkShedule";
import DatePicker from "vue2-datepicker";
import {vueConfig} from "./src/vueConfig";
import {isValid} from "./validator/validator";

let object = {
    id: null,
    name: null,
    start: null,
    end: null,
    address: null,
};

let branchSetting = new Vue({
    components: {vueAlert, vueWorkShedule, DatePicker},
    el: '#branchSetting',
    data() {
        return {
            time: [],
            object: {
                id: null,
                name: null,
                address: null,
                start: null,
                end: null
            },
        }
    },
    created() {
        this.resetObject();

        get('/api/branch/get', (r) => {
            if (r.success === true) {
                this.object = r.item;
                this.time.push(r.item.start !== null ? new Date(r.item.start) : null);
                this.time.push(r.item.end !== null ? new Date(r.item.end) : null);
            }
        });
    },
    methods: {
        validation() {

            let items = {
                '#name': {
                    value: this.object.name,
                    validators: ['notEmpty'],
                },
                '#address': {
                    value: this.object.address,
                    validators: ['notEmpty'],
                },
                '#time': {
                    value: this.time[0],
                    validators: ['notEmpty'],
                }
            };
            return isValid(items);
        },

        send() {
            if (this.validation()) {
                this.object.start = this.time[0];
                this.object.end = this.time[1];
                put('/api/branch/put', this.object.id, this.object, (result) => {
                    if (result.success === true) {
                        branchSetting.$refs.alert.addAlert('Данные филиала обновлены', 'success');
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