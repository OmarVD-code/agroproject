require('./bootstrap');
// Import Vue
import Vue from 'vue';

// Import Axios
import axios from "./axios";

// Import v-money
import money from 'v-money'

// Import vee-validate rules
import "./rules";

// Import pipes
import "./dates";
import "./money";

// Importar BootstrapVue y estilos
import BootstrapVue from 'bootstrap-vue';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';

// toastr
import ToastrPlugin from "./plugins/toastr";

// Import vue-select styles
import 'vue-select/dist/vue-select.css';

// Globals
import "./global-components";

// Router
import router from "./router";

// Axios
Vue.prototype.$axios = axios;

// Registrar v-money
Vue.use(money)

// Registrar BootstrapVue
Vue.use(BootstrapVue);

// Registrar Toastr
Vue.use(ToastrPlugin);

window.Vue = require('vue').default;
// Usar Feather Icons globalmente

Vue.component('sidebar-component', require('./components/Sidebar.vue').default);

const app = new Vue({
    el: '#app',
    router
});
