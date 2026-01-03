<template>
  <div>
    <h3>Lista de productos</h3>
    <filter-slot
      :filters="[]"
      :filterPrincipal="filterSlot.filterSearch"
      :paginate="filterSlot.paginate"
      :totalRows="filterSlot.totalRows"
      :startPage="filterSlot.startPage"
      :toPage="filterSlot.toPage"
      @reload="refreshTable"
      @onChangeCurrentPage="onChangeCurrentPage"
    >
      <template #buttons>
        <b-button variant="primary" class="ml-2" @click="openProductModal(1)"
          >Registrar producto</b-button
        >
      </template>
      <b-table
        ref="productsTable"
        class="w-100"
        slot="table"
        striped
        hover
        :items="myProvider"
        :fields="fields"
        responsive
        :busy.sync="isBusy"
        show-empty
      >
        <template #table-busy>
          <div class="text-center text-primary my-2">
            <b-spinner class="align-middle mr-1" />
            <strong>Loading ...</strong>
          </div>
        </template>

        <template #empty>
          <div class="text-center text-primary my-2">
            <strong>Aún no hay productos registrados.</strong>
          </div>
        </template>

        <template #cell(unit_price)="data">
          <span>{{ data.item.unit_price | formatMoney }}</span>
        </template>

        <template #cell(created_at)="data">
          <span>{{ data.item.created_at | myGlobal }}</span>
        </template>

        <template #cell(actions)="data">
          <feather-icon
            class="text-primary"
            name="edit-2"
            :clickable="true"
            size="18"
            @click="openProductModal(2, data.item)"
            v-b-tooltip.hover.top="'Editar'"
          />
        </template>
      </b-table>
    </filter-slot>

    <add-edit-product
      v-if="showProductModal"
      :product-edit="productToEdit"
      @close="closeProductModal"
      @reload="refreshTable"
    />
  </div>
</template>

<script>
// components
import AddEditProduct from "./components/AddEditProduct.vue";

// data
import fields from "./products.fields";
import filters from "./products.filters";

export default {
  components: {
    AddEditProduct,
  },
  data() {
    return {
      showProductModal: false,
      filterSlot: {
        filters,
        filterSearch: {
          type: "input",
          inputType: "text",
          placeholder: "Buscar por nombre",
          model: null,
        },
        paginate: {
          currentPage: 1,
          perPage: 10,
        },
        totalRows: 0,
        startPage: 0,
        toPage: 0,
      },
      fields,
      isBusy: false,
      productToEdit: null,
    };
  },
  methods: {
    async myProvider(ctx) {
      const sort_by = ctx.sortBy === "" ? "created_at" : ctx.sortBy;
      try {
        const { data } = await axios.post("/products-list", {
          page: this.filterSlot.paginate.currentPage,
          perPage: this.filterSlot.paginate.perPage,
          search: this.filterSlot.filterSearch.model,
          orderBy: sort_by,
          orderDir: ctx.sortDesc ? "desc" : "asc",
        });
        this.filterSlot.paginate.currentPage = data.current_page;
        this.filterSlot.paginate.perPage = data.per_page;
        this.filterSlot.totalRows = data.total;
        this.filterSlot.startPage = data.from;
        this.filterSlot.toPage = data.to;
        return data.data;
      } catch (error) {
        console.log(error);
      }
    },
    openProductModal(action, item = null) {
      if (action === 2) {
        this.productToEdit = item;
      }
      this.showProductModal = true;
    },
    closeProductModal() {
      this.showProductModal = false;
      this.productToEdit = null;
    },
    deleteProduct(product) {
      console.log("delete: ", product);
    },
    refreshTable() {
      this.$refs.productsTable.refresh();
    },
    onChangeCurrentPage(e) {
      this.filterSlot.paginate.currentPage = e;
      this.refreshTable();
    },
  },
};
</script>

<style>
</style>