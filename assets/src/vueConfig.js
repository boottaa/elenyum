import Vue from 'vue';
import {get} from "./baseQuery";

export let vueConfig = new Vue({
    data() {
        return {
            branch: null,
            roles: null,
        }
    },

    created() {
        get('/api/config/get', (r) => {
            if (r.success === true) {
                this.branch = r.branch;
                let start = new Date(this.branch.start);
                this.branch.start = start;
                let end = new Date(this.branch.end);
                this.branch.end = end;

                this.branch.startTimeStr = start.getHours().toString().padStart(2, '0') + ':' + start.getMinutes().toString().padStart(2, '0');
                this.branch.endTimeStr = end.getHours().toString().padStart(2, '0') + ':' + end.getMinutes().toString().padStart(2, '0');

                this.roles = r.roles;
            }
            setTimeout(() => {
                this.$emit('loaded', {branch: this.branch, roles: this.roles});
            }, 500);

        });
    }
});