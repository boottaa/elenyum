import Vue from 'vue';

export let vueAlert = Vue.component('v-alert', {
    props: ['message', 'type'],
    template: `<div id="liveAlertPlaceholder"></div>`,
    methods: {
        addAlert(message, type) {
            let alert =  document.createElement('div');
            alert.innerHTML = `<div class="alert alert-${type} alert-dismissible" role="alert">${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>`;

            let alertPlaceholder = document.getElementById('liveAlertPlaceholder');
            alertPlaceholder.append(alert);
        },
    }
});