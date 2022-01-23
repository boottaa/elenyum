import vSelect from 'vue-select';
import Vue from "vue";

export let menu = Vue.component('v-operation', {
    components: {vSelect},
    props: ['value'],
    data() {
        return {
            select: null,
            items: [],
            total: 0,
            page: 1,
            size: 0,
        }
    },
    mounted() {
        this.getData();
    },
    watch: {
        value() {
            this.select = this.value;
        },
    },
    template: `
      <v-select id="selectOperations"
                @input="setSelected"
                multiple
                aria-required="true"
                v-model="select"
                :options="items"
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
          <li slot="list-footer" class="pagination">
            <nav aria-label="Page navigation example">
              <ul class="pagination justify-content-center">
                <li class="page-item"><button class="page-link" @click="prevPage">Назад</button></li>
                <li class="page-item"><button class="page-link" @click="nextPage">Далее</button></li>
              </ul>
            </nav>
          </li>
      </v-select>
    `,
    methods: {
        getData() {
            $.get("/api/operation/list?page=" + this.page , (data) => {
                if (data.success === true) {
                    this.items = data.items;
                    this.total = data.total;
                    this.page = data.page;
                    this.size = data.size;
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
        hasPrevPage() {
            return this.page > 1;
        },
        hasNextPage() {
            return this.items.length * this.page < this.total
        },
        setSelected(value) {
            if (value === null) {
                this.select = null;
            }

            this.$emit('input', this.select);
        },
    }
});