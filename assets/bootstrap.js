import { startStimulusApp } from '@symfony/stimulus-bridge';
import Vue from "vue";
import DatePicker from "vue2-datepicker";
//https://www.npmjs.com/package/vue2-datepicker
import 'vue2-datepicker/locale/ru';

window.bootstrap = require('bootstrap/dist/js/bootstrap.bundle.js');

// Registers Stimulus controllers from controllers.json and in the controllers/ directory
export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.(j|t)sx?$/
));

Date.prototype.addHours = function(h) {
    this.setTime(this.getTime() + (h*60*60*1000));
    return this;
}
Date.prototype.addMinutes = function(m) {
    this.setTime(this.getTime() + (m*60*1000));
    return this;
}

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

// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);
