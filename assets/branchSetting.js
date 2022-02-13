import './app';
import "./src/baseCalendar";
import Vue from 'vue';

//https://www.npmjs.com/package/vue2-datepicker
import {vueAlert} from "./src/vueAlert";
import {get, post} from "./src/baseQuery";
import {vueWorkShedule} from "./src/vueWorkShedule";
import DatePicker from "vue2-datepicker";

let object = {
    name: null,
    time: [],
};

let branchSetting = new Vue({
    components: {vueAlert, vueWorkShedule, DatePicker},
    el: '#branchSetting',
    data() {
        return {
            object: {
                name: null,
                time: [],
            },
        }
    },
    created() {
        this.resetObject();

        let array = location.href.split('/', 6);
        let id = array[5];

        if (id !== undefined) {
            // get('/api/workSchedule/get/' + id, (r) => {
            //     if (r.success === true) {
            //         this.object = r.item;
            //     }
            // });
        }
    },
    methods: {
        send() {
            return 1;
        },

        resetObject() {
            this.object = Object.assign({}, object);
        },
    },
    delimiters: ['${', '}$'],
});