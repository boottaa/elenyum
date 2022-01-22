import Vue from 'vue';
import th from "vue2-datepicker/locale/es/th";

export let vuePaginator = Vue.component('vue-paginator', {
    props: ['size', 'total', 'url', 'page'],
    template: `
      <nav id="vuePaginator" aria-label="Page navigation example"></nav>
    `,
    watch: {
        size() {
            if (this.size > 0) {
                this.addPaginator();
            }
        },
    },
    methods: {
        addPaginator() {
            let ul = document.createElement('ul');
            ul.className = 'pagination justify-content-center';
            //Текущая страница
            let current = this.page;

            let allPages = Math.ceil(this.total / this.size);
            if (allPages > 1) {
                for (let x = 1; x <= allPages; x++) {
                    let li = document.createElement('li');
                    if (x === current) {
                        // li.className = 'page-item disabled';
                        li.className = 'page-item disabled';
                    } else {
                        li.className = 'page-item';
                    }
                    let aHref = document.createElement('a');
                    aHref.className = 'page-link';
                    aHref.onclick = () => {
                        this.$emit('change', aHref.innerText);

                        let disabled = ul.getElementsByClassName('disabled');
                        if (disabled.length > 0) {
                            for (let i = 0; i <= disabled.length; i++) {
                                disabled[i].className = 'page-item';
                            }
                        }
                        li.className = 'page-item disabled';
                    };
                    aHref.innerText = x;
                    li.appendChild(aHref);
                    ul.appendChild(li);
                }
            }

            let paginator = document.getElementById('vuePaginator');
            paginator.append(ul);
        },
    }
});