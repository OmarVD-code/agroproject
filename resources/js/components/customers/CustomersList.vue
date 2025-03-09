<template>
  <div>
    <h3>Lista de clientes</h3>
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
        <b-button variant="primary" class="ml-2" @click="openCustomerModal(1)">
          Registrar cliente</b-button
        >
      </template>
      <b-table
        ref="customersTable"
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
            <strong>Aún no hay clientes registrados.</strong>
          </div>
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
            @click="openCustomerModal(2, data.item)"
            v-b-tooltip.hover.top="'Editar'"
          />
        </template>
      </b-table>
    </filter-slot>

    <add-edit-customer
      v-if="showCustomerModal"
      :customer-to-edit="customerToEdit"
      @close="closeCustomerModal"
      @reload="refreshTable"
    />
  </div>
</template>

<script>
// components
import AddEditCustomer from "./components/AddEditCustomer";

// data
import fields from "./customers.fields";
import filters from "./customers.filters";

export default {
  data() {
    return {
      showCustomerModal: false,
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
      customerToEdit: null,
    };
  },
  components: {
    AddEditCustomer,
  },
  methods: {
    async myProvider(ctx) {
      const sort_by = ctx.sortBy === '' ? "created_at" : ctx.sortBy;
      try {
        const { data } = await axios.post("/customers-list", {
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
    openCustomerModal(action, item = null) {
      if (action === 2) {
        this.customerToEdit = item;
      }
      this.showCustomerModal = true;
    },
    closeCustomerModal() {
      this.showCustomerModal = false;
      this.customerToEdit = null;
    },
    refreshTable() {
      this.$refs.customersTable.refresh();
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