import Vue from "vue";
import DatePicker from "vue2-datepicker";
import 'jquery.cookie';

if ($.cookie('currentDate') === undefined) {
    $.cookie('currentDate', new Date(), {
        path: '/',
    });
}

export let baseCalendar = new Vue({
    components: {DatePicker},
    el: '#baseCalendar',

    data() {
        return {
            pickDate: null,
        }
    },
    mounted() {
        this.pickDate = new Date($.cookie('currentDate'));
    },
    methods: {
        dateClick(date) {
            let hrefArray = location.href.split('/');

            if (hrefArray[hrefArray.length - 1] !== 'calendar') {
                window.location.href = '/calendar';
            }

            $.cookie('currentDate', date, {
                path: '/',
            });
            this.pickDate = date;
            this.$emit('dateChange', date);
        },
    }
});