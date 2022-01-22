import Vue from 'vue';
import {clear} from "core-js/internals/task";

//items = [{'name': 'vasya', 'phone': '1234'}, {'name': 'vasya2', 'phone': '12346666'}];
//headers = [{name: 'Имя', 'system': 'name'}, {'name': 'Телефон', 'system':'phone'}]
//actions = [{'name': 'Удалить', 'onclick': () => {}, 'type': 'primary']
export let vueListTable = Vue.component('vue-list-table', {
    props: ['headers', 'items', 'actions'],
    template: `
      <div id="liveListTable"></div>`,
    data() {
        return {
            head: []
        }
    },
    watch: {
        items() {
            if (this.items.length > 0) {
                this.addTable();
            }
        },
    },
    methods: {
        clear() {
            let liveListTable = document.getElementById('liveListTable');
            liveListTable.innerHTML = '';
        },
        addTable() {
            this.clear();
            this.head = JSON.parse(JSON.stringify(this.headers));
            let table = document.createElement('table');
            table.className = 'table';
            let head = table.createTHead();
            let headRow = head.insertRow();
            if (this.actions !== undefined) {
                this.head.push({text: '#', system: 'vueListTableAction'});
            }
            this.head.forEach((i, key) => {
                headRow.insertCell(key).outerHTML = `<th>${i.text}</th>`;
            });

            let body = table.createTBody();
            this.items.forEach((item) => {
                let bodyRow = body.insertRow();
                this.head.forEach((i, k) => {
                    if (i.system === 'vueListTableAction') {
                        let divActionButtons = document.createElement('div');
                        this.actions.forEach((i) => {
                            let button = document.createElement('button');
                            button.addEventListener('click', i.onclick);
                            let id = item['id'] !== undefined ? item['id'] : null;
                            button.setAttribute('data-id', id);
                            button.className = `btn btn-${i.type} mr-2`;
                            button.innerText = i.value;
                            divActionButtons.appendChild(button);
                        });

                        bodyRow.insertCell(k).appendChild(divActionButtons);
                    } else {
                        bodyRow.insertCell(k).appendChild(document.createTextNode(item[i.system]));
                    }
                });

            });

            let liveListTable = document.getElementById('liveListTable');
            liveListTable.append(table);
        },
    }
});