import Vue from 'vue';
import vSelect from 'vue-select';
import '../src/vueMenu';
import '../src/vueClientSelect';
import '../src/vueOperationSelect';
//https://www.npmjs.com/package/vue2-datepicker
import DatePicker from 'vue2-datepicker';
import {addErrorMessage, isValid} from "../validator/validator";
import {menuItems, paymentTypes, sheduleStatus, sheduleObject} from "./objects";
import {vueConfig} from "../src/vueConfig";
import {vueAlert} from "../src/vueAlert";

//https://vue-select.org/guide/values.html#getting-and-setting
Vue.component('v-select', vSelect);

export let modalVue = new Vue({
    components: {DatePicker, vueAlert},
    el: '#modalEvent',
    data() {
        return {
            showId: 1,
            sheduleStatus: sheduleStatus,
            paymentTypes: paymentTypes,
            object: Object.assign({}, sheduleObject),
            menuItems: menuItems,
            todayResourceWork: undefined,
            branch: {
                item: null,
                start: null,
                end: null,
                startTimeStr: null,
                endTimeStr: null,
            },

            startTimeStr: null,
            endTimeStr: null,
        }
    },
    created() {
        vueConfig.$on('loaded', (data) => {
            let b = data.branch;
            this.startTimeStr = b.startTimeStr;
            this.endTimeStr = b.endTimeStr;
        });
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
            $('.invalid-feedback').remove();
            if (this.object.paymentType === 1 && this.totalPrice !== this.object.paymentCard) {
                addErrorMessage('#paymentCard', 'Сумма не соответствует итоговой сумме');
                return;
            }
            if (this.object.paymentType === 2 && this.totalPrice !== this.object.paymentCash) {
                addErrorMessage('#paymentCash', 'Сумма не соответствует итоговой сумме');
                return;
            }
            if (this.object.paymentType === 3 && this.totalPrice !== parseInt(this.object.paymentCard) + parseInt(this.object.paymentCash)) {
                addErrorMessage('#paymentCard', 'Суммарная сумма по карте и наличным не соответствует итоговой сумме');
                addErrorMessage('#paymentCash', 'Суммарная сумма по карте и наличным не соответствует итоговой сумме');
                return;
            }
            if (this.validation()) {
                if (this.todayResourceWork === undefined) {
                    this.$refs.alert.addAlert('Указано не верное время, в данный день специалист не работает', 'danger');
                    return;
                }

                if (!(this.object.start >= new Date(this.todayResourceWork.start) && modalVue.object.end <= new Date(this.todayResourceWork.end))) {
                    this.$refs.alert.addAlert('Указано не верное время, время начала или окончания записи не входит в рабочее время специалиста', 'danger');
                    return;
                }
                this.$emit('send', this.object);
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
            $('.invalid-feedback').remove();
            if (this.object.paymentType === value) {
                this.object.paymentType = null;

                return;
            }

            if (value === 1) {
                this.object.paymentCard = this.totalPrice;
            } else if (value === 2) {
                this.object.paymentCash = this.totalPrice;
            } else {
                this.object.paymentCard = null;
                this.object.paymentCash = null;
            }
        },
    },
    delimiters: ['${', '}$'],
});