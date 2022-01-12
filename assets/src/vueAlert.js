import Vue from 'vue';

export let vueAlert = Vue.component('vue-alert', {
    props: ['message', 'type', 'timeout'],
    template: `
      <div style="position: relative; width: 100%; z-index: 9999">
      <div style="position: absolute; width: 100%;" id="liveAlertPlaceholder"></div>
      </div>`,
    methods: {
        addAlert(message, type) {
            let alert = document.createElement('div');
            alert.innerHTML = `<div class="alert alert-${type} alert-dismissible" role="alert">${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>`;

            let alertPlaceholder = document.getElementById('liveAlertPlaceholder');
            alertPlaceholder.append(alert);


            let timeout = this.timeout || 5000;
            setTimeout(() => {
                let al = new bootstrap.Alert(alert);
                al.close();
            }, timeout);
        },
    }
});