import toastr from "toastr";
import "toastr/build/toastr.min.css";
import { install } from "vee-validate";

export default {
    install(Vue) {
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-right",
            timeOut: "3000",
        };
        Vue.prototype.$toastr = toastr;
    },
}