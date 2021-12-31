import vSelect from 'vue-select';
import Vue from "vue";

export let menu = Vue.component('v-client', {
    name: 'InfiniteScroll',
    components: {vSelect},
    props: ['value'],
    data() {
        return {
            //Нужен для сброса поисковика
            select: null,
            isSearch: false,
            limit: 20,
            clientPage: 1,
            clientTotal: null,
            clientSearch: '',
            clients: [],
        }
    },

    mounted() {
        this.onLoadClients(1).then(r => {
            this.observer = new IntersectionObserver(this.infiniteScroll);
        });
    },
    watch: {
        value() {
            this.select = this.value;
        },
        async clientSearch() {
            // debugger;
            if (typeof this.clientSearch === 'string') {
                this.clients = [];
                this.clientPage = 1;
                this.clientTotal = 0;

                if (this.isSearch) {
                    clearTimeout(this.isSearch);
                }

                this.isSearch = setTimeout(() => {
                    this.onLoadClients(this.clientPage, this.clientSearch).then(() => {
                        this.isSearch = false;
                    });
                }, 200);
            }
        },
    },
    computed: {
        hasNextPage() {
            return this.clientTotal > 0 && (Math.ceil(this.clientTotal / this.limit) > this.clientPage);
        },
    },
    template: `
      <v-select ref="clientPhone"
                @input="setSelected"
                @open="onOpen"
                @search="(query) => {clientSearch = query}"
                taggable
                label="phone"
                v-model="select"
                :create-option="createClient"
                :filterable="false"
                :options="clients"
      >
      <template #option="{ phone, name }">
        {{ phone }}
        <br/>
        <i>{{ name }}</i>
      </template>
      <template #no-options="{ search, searching, loading }">
        Нечего не найдено, будет добавлен новый клиент, {{search}}
      </template>
      <template #list-footer>
        <li v-show="hasNextPage" ref="load" class="loader">
          Загрузка клиентов...
        </li>
      </template>
      </v-select>
    `,
    methods: {
        async onLoadClients(page = 1, query = '') {
            $.get(`/client/list?page={{page}&query={{query}`, (data) => {
                this.clients = data.items.concat(this.clients);
                this.clientTotal = data.total;
            });
        },
        async onOpen() {
            if (this.hasNextPage) {
                await this.$nextTick();
                this.observer.observe(this.$refs.load);
            }
        },
        async infiniteScroll([{isIntersecting, target}]) {
            if (isIntersecting) {
                const ul = target.offsetParent
                const scrollTop = target.offsetParent.scrollTop
                await this.onLoadClients(++this.clientPage, this.clientSearch);
                await this.$nextTick();
                ul.scrollTop = scrollTop
            }
        },
        setSelected(value) {
            if (value === null) {
                this.select = {
                    id: null,
                    phone: null,
                    name: null,
                };
            }

            this.$emit('input', this.select);
        },

        createClient(value) {
            this.select = {
                phone: value,
                id: null,
                name: null,
            }

            this.$emit('input', this.select);
        },
    }
});