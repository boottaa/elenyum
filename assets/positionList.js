import './app';
import "./src/baseCalendar";
import Vue from 'vue';
import {del} from "./src/baseQuery";
import {vueList} from "./src/vueList";
import {vueAlert} from "./src/vueAlert";

new Vue({
    components: {vueList, vueAlert},
    el: '#positionList',
    data() {
        return {
            url: '/api/position/list',
            headers: [
                {text: 'Название', system: 'title'},
                {text: 'Отображать в календаре', system: 'inCalendar'},
            ],
            actions: [
                {
                    value: 'Удалить', type: 'danger', onclick: (e) => {
                        let button = $(e.target);
                        let id = button.attr('data-id');

                        del(
                            `/api/position/delete/${id}`,
                            (r) => {
                                if (r.success === true) {
                                    button.parents('tr').remove();
                                } else {
                                    this.$refs.alert.addAlert(r.message, 'danger');
                                }
                            },
                            (e) => {
                                this.$refs.alert.addAlert(e.message, 'danger');
                            }
                        );
                    }
                },
                {
                    value: 'Редактировать', type: 'info', onclick: (e) => {
                        let button = $(e.target);
                        let id = button.attr('data-id');

                        location.href = '/position/post/' + id;
                    }
                },
            ],
        }
    },
    mounted() {
        if (location.hash === '#added') {
            this.$refs.alert.addAlert('Должность добавлена', 'success');
            location.hash = ''
        }
        if (location.hash === '#edited') {
            this.$refs.alert.addAlert('Должность обновлена', 'success');
            location.hash = ''
        }
    },
    methods: {
        prepare(data) {
            data.items.map((i) => {
                i.inCalendar = i.inCalendar === true ? 'Да' : 'Нет';
            });
        }
    },
    delimiters: ['${', '}$'],
});