import Vue from 'vue';
import vSelect from 'vue-select';
import '../src/vueMenu';
import '../src/vueClientSelect';
import '../src/vueOperationSelect';
//https://www.npmjs.com/package/vue2-datepicker
import DatePicker from 'vue2-datepicker';
import {isValid} from "../Validator/Validator";
import {menuItems, paymentTypes, sheduleStatus, sheduleObject} from "./objects";

//https://vue-select.org/guide/values.html#getting-and-setting
Vue.component('v-select', vSelect);

export let modalVue = new Vue({
    components: {DatePicker},
    el: '#modalEvent',
    data() {
        return {
            showId: 1,
            sheduleStatus: sheduleStatus,
            paymentTypes: paymentTypes,
            object: Object.assign({}, sheduleObject),
            menuItems: menuItems,
        }
    },

    computed: {
        totalPrice() {
            let total = 0;
            this.object.operations.forEach(operation => {
                total += operation.price * operation.count;
            });
            return total;
        },
    },
    methods: {
        menuChange(item) {
            this.menuItems.map(
                (i) => {
                    if (i.label !== item.label && i.isActive) {
                        i.isActive = false;
                    }
                }
            );

            item.isActive = true;
        },
        //Срабатывает при выборе клиента
        setSelected(value) {
            if (value === null) {
                this.object.client = {
                    id: null,
                    phone: null,
                    name: null,
                };
            }
        },
        changeOperation(value) {
            let totalDuration = this.recountTotalTime(this.object.operations);
            this.object.end = new Date(this.object.start.toString()).addMinutes(totalDuration);

        },
        recountTotalTime(value) {
            let totalDuration = 0;
            if (value !== null) {
                value.forEach((item) => {
                    item.count = (item.count ? item.count : 1);
                    totalDuration += item.duration * item.count;
                });
            }
            return totalDuration;
        },
        validation() {
            let items = {
                '#clientPhone': {
                    value: this.object.client.phone,
                    validators: ['phone'],
                },
                '#clientName': {
                    value: this.object.client.name,
                    validators: ['notEmpty'],
                },
                '#operations': {
                    value: this.object.operations,
                    validators: ['notEmpty'],
                },
                '#eventStart': {
                    value: this.object.start,
                    validators: ['notEmpty'],
                },
                '#eventEnd': {
                    value: this.object.end,
                    validators: ['notEmpty'],
                }
            };
            return isValid(items);
        },
        send() {
            if (this.validation()) {
                this.$emit('send', JSON.parse(JSON.stringify(this.object, (key, value) => {
                    return value
                })));
                this.resetObject();
            }
        },
        remove() {
            this.$emit('remove', JSON.parse(JSON.stringify(this.object, (key, value) => {
                return value
            })));
        },

        resetObject() {
            this.object = Object.assign({}, sheduleObject);
            this.$emit('send', null);
            this.$emit('remove', null);
        },

        itemSelected(item) {
            this.showId = item.id;
        },

        clickEventStatus(value) {
            if (this.object.status === value) {
                this.object.status = false;
            }
        },

        clickPaymentType(value) {
            if (this.object.paymentType === value) {
                this.object.paymentType = false;
            }
        },
    },
    delimiters: ['${', '}$'],
});