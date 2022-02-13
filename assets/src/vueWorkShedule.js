import Vue from 'vue';
import DatePicker from "vue2-datepicker";

export let vueWorkShedule = Vue.component('vue-work-shedule', {
    components: {DatePicker},
    props: ['value', 'text', 'start', 'end'],
    data() {
        return {
            select: []
        }
    },
    watch: {
        value() {
            this.select = this.value;
        },
    },
    template: `
      <div id="workSchedulePostDatePicker" class="mt-1">
        <label for="title" v-if="text" class="form-label">{{ text }}</label> <br>
        <div class="workSchedulePostDatePickerInner">
          <date-picker :time-picker-options="{start: start, step: '01:00', end: end}" aria-required="true" v-model="select" id="eventStart" @input="setSelected" style="width: 100%" type="time" :show-second="false" range></date-picker>
        </div>
      </div>
    `,
    methods: {
        setSelected(value) {
            if (value === null) {
                this.select = null;
            }

            this.$emit('input', this.select);
        },
    }
});