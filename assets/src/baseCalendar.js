import Vue from "vue";
import DatePicker from "vue2-datepicker";

export let baseCalendar = new Vue({
    components: {DatePicker},
    el: '#baseCalendar',

    data() {
        return {
            pickDate: new Date(),
        }
    },
    methods: {
        dateClick(date) {
            // console.log(date);
            this.pickDate = date;
            this.$emit('dateChange', date);
        },
    }
});