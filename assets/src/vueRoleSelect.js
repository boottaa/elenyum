import vSelect from 'vue-select';
import Vue from "vue";

export let menu = Vue.component('v-roles', {
    components: {vSelect},
    props: ['value'],
    data() {
        return {
            select: null,
            roles: [],
        }
    },
    mounted() {
        $.get("/api/roles/list", (data) => {
            this.roles = data.items;
        });
    },
    watch: {
        value() {
            this.select = this.value;
        },
    },
    template: `
      <v-select id="selectRoles"
                @input="setSelected"
                multiple
                aria-required="true"
                v-model="select"
                :options="roles"
                label="description"
                :get-option-label="(operation) => operation.description">
      <template #no-options>
        Услуга не найдена
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