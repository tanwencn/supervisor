import Vue from 'vue'
import Antd from 'ant-design-vue/';
import 'ant-design-vue/dist/antd.css';
import App from './App.vue'
import router from "./router.js"
import axios from 'axios';
import moment from 'moment'

Vue.use(Antd);

Vue.config.productionTip = false

let token = document.head.querySelector('meta[name="csrf-token"]');

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

Vue.prototype.$http = axios.create();

Vue.prototype.$moment = moment;

new Vue({
  render: h => h(App),
  router
}).$mount('#app')
