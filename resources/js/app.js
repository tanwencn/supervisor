import Vue from 'vue'
import Antd from 'ant-design-vue/';
import '../../node_modules/ant-design-vue/dist/antd.css';
import App from './App.vue'
import router from "./router.js"
import axios from 'axios';
import moment from 'moment';
import hljs from 'highlight.js';
import 'highlight.js/styles/vs2015.css';

Vue.use(hljs.vuePlugin);

Vue.use(Antd);

Vue.config.productionTip = false

let token = document.head.querySelector('meta[name="csrf-token"]');

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

axios.defaults.baseURL = window.basePath
Vue.prototype.$http = axios.create();

Vue.prototype.$moment = moment;

new Vue({
  render: h => h(App),
  router
}).$mount('#app')
