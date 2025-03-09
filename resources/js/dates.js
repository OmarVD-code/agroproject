import Vue from "vue";
import moment from "moment";

Vue.filter("myGlobal", (created) => {
    if (created) return moment(created).format("DD/MM/YYYY");
    return "-";
});

Vue.filter("myGlobalWithHour", (created) => {
    if (created) return moment(created).format("DD/MM/YYYY h:mm A");
    return "-";
});
