import Vue from 'vue';
import {vueTable} from "./vueTable";
import {vuePaginator} from "./vuePaginator";

//headers = [{name: 'Имя', 'system': 'name'}, {'name': 'Телефон', 'system':'phone'}]
//actions = [{'name': 'Удалить', 'onclick': () => {}, 'type': 'primary']
//url = '/api/employee/list'
//onprepare = функция которая выполняется перед выводом
// onprepare(data) {
//      data.items.map((i) => {
//         i.position = i.position.title;
//      });
//}
export let vueList = Vue.component('vue-list', {
    props: ['url', 'headers', 'actions', 'onprepare'],
    components: {vueTable, vuePaginator},
    template: `
      <div>
        <vue-table :headers="headers" :items="items" :actions="actions"></vue-table>
        <vue-paginator :size="size" :total="total" :url="url" :page="page" @change="onChangePage"></vue-paginator>
      </div>
    `,
    data() {
        return {
            //(получаем с backend)
            items: [],
            //Количество элементов на одной странице (получаем с backend)
            size: 0,
            //Всего элементов (получаем с backend)
            total: 0,
            //Текущая страница (получаем с backend)
            page: 1,
        }
    },
    created() {
        this.send();
    },
    methods: {
        send() {
            this.onRequest(this.page);
        },
        onChangePage(page) {
            this.page = page;
            this.onRequest(page);
        },
        onRequest(page) {
            $.ajax({
                type: "GET",
                url: this.url + '?page=' + page,
                contentType: "application/json",
                dataType: 'json',
                success: (result) => {
                    if (result.success === true) {
                        this.onprepare(result);

                        this.total = result.total;
                        this.size = result.size;
                        this.page = result.page;
                        this.items = result.items;
                    }
                }
            });
        }
    },
});