import './app';
import "./src/baseCalendar";
import Vue from 'vue';

//https://www.npmjs.com/package/vue2-datepicker
import {vueAlert} from "./src/vueAlert";
import {get, post, put} from "./src/baseQuery";
import {vueWorkShedule} from "./src/vueWorkShedule";
import DatePicker from "vue2-datepicker";

let object = {
    id: null,
    name: null,
    start: null,
    end: null
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
                this.time.push(new Date(r.item.start * 1000));
                this.time.push(new Date(r.item.end * 1000));
            }
        });
    },
    methods: {
        send() {
            this.object.start = this.time[0];
            this.object.end = this.time[1];
            put('/api/branch/put', this.object.id, this.object, (result) => {
                if (result.success === true) {
                    branchSetting.$refs.alert.addAlert('Данные филиала обновлены', 'success');
                }
            });
            return 1;
        },

        resetObject() {
            this.object = Object.assign({}, object);
        },
    },
    delimiters: ['${', '}$'],
});