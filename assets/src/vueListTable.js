import Vue from 'vue';

//items = [{'name': 'vasya', 'phone': '1234'}, {'name': 'vasya2', 'phone': '12346666'}];
//structureItems = [{name: 'Имя', 'system': 'name'}, {'name': 'Телефон', 'system':'phone'}]
export let vueListTable = Vue.component('vue-list-table', {
    props: ['headers', 'items'],
    template: `
      <div id="liveListTable"></div>`,
    watch: {
        items() {
            if (this.items.length > 0) {
                this.addTable();
            }
        },
    },
    methods: {
        addTable() {
            let table = document.createElement('table');
            table.className = 'table';
            let head = table.createTHead();
            let headRow = head.insertRow();
            this.headers.forEach((i, key) => {
                headRow.insertCell(key).outerHTML = `<th>${i.text}</th>`;
            });

            let body = table.createTBody();
            this.items.forEach((item) => {
                let bodyRow = body.insertRow();
                this.headers.forEach((i) => {
                    console.log(i.system);
                    bodyRow.insertCell().appendChild(document.createTextNode(item[i.system]))
                });
            });

            let liveListTable = document.getElementById('liveListTable');
            liveListTable.append(table);
        },
    }
});