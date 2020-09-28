import Vue from "vue";
import VueRouter from "vue-router";
import routes from "./routes";
import { BootstrapVue, IconsPlugin } from "bootstrap-vue";
import "bootstrap/dist/css/bootstrap.css";
import "bootstrap-vue/dist/bootstrap-vue.css";
import home from "./components/Home.vue";

Vue.component("home", home);

Vue.use(BootstrapVue);
Vue.use(IconsPlugin);
Vue.use(VueRouter);

require("./bootstrap");

let app = new Vue({
    el: "#app",
    
    router: new VueRouter(routes)
});
