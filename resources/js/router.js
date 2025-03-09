import Vue from "vue";
import VueRouter from "vue-router";

Vue.use(VueRouter);

import ProductsList from "./components/products/ProductsList.vue";
import CustomersList from "./components/customers/CustomersList.vue";
import SuppliersList from "./components/suppliers/SuppliersList.vue";
import SalesList from "./components/sales/SalesList.vue";
import ReportsList from "./components/story/ReportsList.vue";

const routes = [
  {
    path: "/products",
    name: "Products",
    component: ProductsList,
  },
  {
    path: "/customers",
    name: "Customers",
    component: CustomersList,
  },
  {
    path: "/suppliers",
    name: "Suppliers",
    component: SuppliersList,
  },
  {
    path: "/sales",
    name: "Sales",
    component: SalesList,
  },
  {
    path: "/reports",
    name: "Reports",
    component: ReportsList,
  },
];

const router = new VueRouter({
  mode: "history",
//   base: process.env.BASE_URL,
  routes,
});

export default router;