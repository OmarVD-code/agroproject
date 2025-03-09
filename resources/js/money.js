import Vue from "vue"

Vue.filter("myMoney", (num) => {
    return num ? `$ ${num}` : '$ 0.00'
})

Vue.filter('addTwoDecimals', (num) => {
    return num ? `$ ${parseFloat(num).toFixed(2)}` : '$ 0.00'
})

Vue.filter("formatMoney", (num) => {
    if(typeof num === 'string'){
        num = Number(num.replace(/,/g, ''));
    }
     const formatter = new Intl.NumberFormat("en-US", {
         currency: "PEN",
         style: "currency",
         currencyDisplay: "symbol",
     }).format(num);
    return formatter.replace("PEN", "S/.");
})