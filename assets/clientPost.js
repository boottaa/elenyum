import './app';
import "./src/baseCalendar";
import Vue from 'vue';
import {isValid} from "./validator/validator";
import './src/vuePositionSelect';
//https://www.npmjs.com/package/vue2-datepicker
import DatePicker from 'vue2-datepicker';
import {vueAlert} from "./src/vueAlert";
import {post, get, put} from "./src/baseQuery";

let object = {
    id: null,
    name: null,
    phone: null,
    email: null,
    additionalPhone: null,
    dateBrith: null,
};

let clientPost = new Vue({
    components: {DatePicker, vueAlert},
    el: '#clientPost',
    data() {
        return {
            object: {
                id: null,
                name: null,
                phone: null,
                email: null,
                additionalPhone: null,
                dateBrith: null,
            },
        }
    },
    created() {
        this.resetObject();

        let array = location.href.split('/', 6);
        let id = array[5];

        if (id !== undefined) {
            get('/api/client/get/' + id, (r) => {
                if (r.success === true) {
                    this.object.id = r.item.id;
                    this.object.name = r.item.name;
                    this.object.phone = r.item.phone;
                    this.object.email = r.item.email;
                    this.object.additionalPhone = r.item.additionalPhone;

                    let st = r.item.dateBrith;
                    if (!!st) {
                        let pattern = /(\d{2})\.(\d{2})\.(\d{4})/;
                        this.object.dateBrith = new Date(st.replace(pattern, '$3-$2-$1'));
                    }
                }
            });
        }
    },
    methods: {
        validation() {
            let items = {
                '#userName': {
                    value: this.object.name,
                    validators: ['notEmpty', 'fio'],
                },
                '#phone': {
                    value: this.object.phone,
                    validators: ['phone'],
                },
                '#additionalPhone': {
                    value: this.object.additionalPhone,
                    validators: ['phone'],
                },
                '#email': {
                    value: this.object.email,
                    validators: ['email'],
                },
            };
            return isValid(items);
        },
        send() {
            if (this.validation()) {
                if (this.object.id === null) {
                    post('/api/client/post', this.object, (result) => {
                        if (result.success === true) {
                            this.resetObject();
                            location.href = '/client/list#added';
                        }
                    });
                } else {
                    put('/api/client/put', this.object.id, this.object, (result) => {
                        if (result.success === true) {
                            this.resetObject();
                            location.href = '/client/list#edited';
                        }
                    });
                }
            }
        },

        resetObject() {
            this.object = Object.assign({}, object);
        },
    },
    delimiters: ['${', '}$'],
});