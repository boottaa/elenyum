import vSelect from 'vue-select';
import Vue from "vue";

export let menu = Vue.component('v-position', {
    components: {vSelect},
    props: ['value'],
    data() {
        return {
            select: null,
            positions: [],
        }
    },
    mounted() {
        $.get("/api/position/list", (data) => {
            this.positions = data.items;
        });
    },
    watch: {
        value() {
            this.select = this.value;
        },
    },
    template: `
      <v-select id="selectPositions"
                @input="setSelected"
                aria-required="true"
                v-model="select"
                :options="positions"
                label="title"
                :get-option-label="(position) => position.title">
          <template #no-options>
              Должность не найдена
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