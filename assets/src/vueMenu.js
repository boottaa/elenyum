import Vue from 'vue';

export let menu = Vue.component('v-menu', {
    props: ['item', 'items'],
    template: `
        <button type="button" v-bind:class="(item.isActive) ? 'active' : ''"
                class="list-group-item list-group-item-action" @click="menuChange(item)">
            {{ item.label }}
        </button>
    `,
    methods: {
        menuChange(item) {
            this.items.map(
                (i) => {
                    if (i.label !== item.label && i.isActive) {
                        i.isActive = false;
                    }
                }
            );

            this.$emit('select', item);
            item.isActive = true;
        },
    }
});