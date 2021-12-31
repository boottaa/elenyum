import vSelect from 'vue-select';
import Vue from "vue";

export let menu = Vue.component('v-operation', {
    components: {vSelect},
    props: ['value'],
    data() {
        return {
            select: null,
            operations: [],
        }
    },
    mounted() {
        $.get("/operation/list", (data) => {
            this.operations = data.items;
        });
    },
    watch: {
        value() {
            this.select = this.value;
        },
    },
    template: `
      <v-select id="operations"
                @input="setSelected"
                multiple
                aria-required="true"
                v-model="select"
                :options="operations"
                label="title"
                :get-option-label="(operation) => operation.title">
          <template #no-options>
              Услуга не найдена
          </template>
          <template #option="{ title, price, duration }">
              {{ title }}
              <br/>
              <i>{{ price }} руб.</i> <i style="color: #05885d;">({{ duration }} мин.)</i>
          </template>
      </v-select>
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