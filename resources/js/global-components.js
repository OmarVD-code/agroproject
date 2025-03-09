import Vue from 'vue';
import FeatherIcon from "./components/commons/FeatherIcon.vue";
import FilterSlot from "./components/commons/FilterSlot.vue";
import { ValidationObserver, ValidationProvider } from 'vee-validate'
import vSelect from "vue-select";

Vue.component("feather-icon", FeatherIcon);
Vue.component("filter-slot", FilterSlot);
Vue.component('validation-observer', ValidationObserver)
Vue.component('validation-provider', ValidationProvider)
Vue.component('vSelect', vSelect)
