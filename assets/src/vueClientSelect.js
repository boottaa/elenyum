import vSelect from 'vue-select';
import Vue from "vue";

export let menu = Vue.component('v-client', {
    components: {vSelect},
    props: ['value'],
    data() {
        return {
            select: null,
            items: [],
            total: 0,
            page: 1,
            size: 0,
            query: '',
        }
    },

    mounted() {
        this.getData();
    },
    watch: {
        value() {
            this.select = this.value;
        },
        async query() {
            if (typeof this.query === 'string') {
                this.items = [];
                this.page = 1;

                if (this.isSearch) {
                    clearTimeout(this.isSearch);
                }

                this.isSearch = setTimeout(() => {
                    this.getData();
                }, 200);
            }
        },
    },
    computed: {
        hasPrevPage() {
            return Boolean(this.page > 1);
        },
        hasNextPage() {
            return Boolean(this.size * this.page < this.total);
        },
    },
    template: `
      <v-select ref="selectClientPhone"
                @input="setSelected"
                @search="(q) => {this.query = q}"
                taggable
                label="phone"
                v-model="select"
                :filterable="false"
                :options="items"
      >
          <template #option="{ phone, name }">
            {{ phone }}
            <br/>
            <i>{{ name }}</i>
          </template>
          <template #no-options="{ search, searching, loading }">
            Нечего не найдено, будет добавлен новый клиент, {{search}}
          </template>
          <li slot="list-footer" class="pagination-sm">
            <nav aria-label="Page navigation example">
              <ul class="pagination justify-content-center">
                <li class="page-item" :class="[{disabled: !hasPrevPage}]"><button class="page-link" @click="prevPage">Назад</button></li>
                <li class="page-item" :class="[{disabled: !hasNextPage}]"><button class="page-link" @click="nextPage">Далее</button></li>
              </ul>
            </nav>
          </li>
      </v-select>
    `,
    methods: {
        getData() {
            $.get(`/api/client/query?page=${this.page}&query=${this.query}`, (data) => {
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
        setSelected(value) {
            if (value === null) {
                this.select = {
                    id: null,
                    phone: null,
                    name: null,
                };
            } else if(typeof value === 'object') {
                this.select = {
                    phone: value.phone,
                    id: value.id,
                    name: value.name,
                }
            } else {
                this.select = {
                    phone: value,
                    id: null,
                    name: null,
                };
            }

            this.$emit('input', this.select);
        },
    }
});