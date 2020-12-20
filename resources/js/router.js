import Vue from "vue";
import VueRouter from "vue-router";

// 要告诉 vue 使用 vueRouter
Vue.use(VueRouter);

const routes = [
    {
        name: 'content',
        path: "/content",
        component: require('./screens/content.vue').default
    },
    {
        name: 'blank',
        path: "/blank",
        component: require('./screens/blank.vue').default
    }
]

var router = new VueRouter({
    routes,
    mode: 'history',
    base: window.basePath
})
export default router;
