import vSelect from 'vue-select';
import Vue from "vue";

export let menu = Vue.component('v-operation', {
    components: {vSelect},
    props: ['value', 'employee', 'disabled'],
    data() {
        return {
            select: null,
            items: [],
            total: 0,
            page: 1,
            size: 0
        }
    },
    mounted() {
        this.getData();
    },
    watch: {
        value() {
            this.select = this.value;
        },
        employee() {
            this.getData();
        }
    },
    template: `
      <v-select id="selectOperations"
                @input="setSelected"
                multiple
                aria-required="true"
                v-model="select"
                :options="items"
                label="title"
                :disabled="this.disabled"
                :get-option-label="(operation) => operation.title">
      <template #no-options>
        Услуга не найдена
      </template>
      <template #option="{ title, price, duration }">
        {{ title }}
        <br/>
        <i>{{ price }} руб.</i> <i style="color: #05885d;">({{ duration }} мин.)</i>
      </template>
      <li slot="list-footer" class="pagination-sm">
        <nav aria-label="Page navigation example">
          <ul class="pagination justify-content-center">
            <li class="page-item" :class="[{disabled: !hasPrevPage}]">
              <button class="page-link" @click="prevPage">Назад</button>
            </li>
            <li class="page-item" :class="[{disabled: !hasNextPage}]">
              <button class="page-link" @click="nextPage">Далее</button>
            </li>
          </ul>
        </nav>
      </li>
      </v-select>
    `,
    computed: {
        hasPrevPage() {
            return Boolean(this.page > 1);
        },
        hasNextPage() {
            return Boolean(this.size * this.page < this.total);
        },
    },
    methods: {
        getData() {
            if (this.employee === null) return;
            $.get("/api/operation/list?page=" + this.page + "&employee=" + this.employee, (data) => {
                if (data.success === true) {
                    this.items = data.items;
                    this.total = data.total;
                    this.page = data.page;
                    this.size = data.size;

                    this.$emit('loaded', data);
                }
            });
        },
        prevPage() {
            this.page -= 1;
            this.getData();
        },
        nextPage() {
            this.page += 1;
            this.getData();
        },
        setSelected(value) {
            if (value === null) {
                this.select = null;
            }

            this.$emit('input', this.select);
        },
    }
});