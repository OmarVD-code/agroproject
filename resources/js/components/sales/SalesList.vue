<template>
  <div>
    <h3>Ventas realizadas</h3>
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
        <label class="ml-4" for="year">Año</label>
        <v-select
          :options="optionsYears"
          label="name"
          :reduce="(option) => option.id"
          :clearable="false"
          v-model="year"
          class="d-inline-block"
          style="min-width: 100px"
        />

        <label class="ml-4" for="year">Mes</label>
        <v-select
          :options="optionsMonths"
          label="name"
          :reduce="(option) => option.id"
          :clearable="false"
          v-model="month"
          class="d-inline-block"
          style="min-width: 100px"
        />

        <label class="ml-2" for="status">Estado</label>
        <v-select
          :options="filterStatusOptions"
          label="name"
          :reduce="(option) => option.id"
          :clearable="false"
          v-model="status"
          class="d-inline-block"
          style="min-width: 150px"
        />
        <feather-icon
          name="plus"
          size="24"
          class="ml-2 text-primary"
          :clickable="true"
          v-b-tooltip.hover.top="'Agregar venta'"
          @click="openRegisterSaleModal()"
        />
        <feather-icon
          name="file-text"
          size="24"
          class="ml-2 text-success"
          :clickable="true"
          v-b-tooltip.hover.top="'Generar reporte mensual'"
          @click="generateMonthlyReport()"
        />
      </template>
      <b-table
        ref="salesTable"
        class="w-100"
        slot="table"
        striped
        hover
        :items="myProvider"
        :fields="fields"
        responsive
        :busy.sync="isBusy"
        show-empty
        :sticky-header="'70vh'"
      >
        <template #table-busy>
          <div class="text-center text-primary my-2">
            <b-spinner class="align-middle mr-1" />
            <strong>Loading ...</strong>
          </div>
        </template>

        <template #empty>
          <div class="text-center text-primary my-2">
            <strong>Aún no hay ventas registradas.</strong>
          </div>
        </template>

        <template #cell(date)="data">
          <span>{{ data.item.date | myGlobal }}</span>
        </template>

        <template #cell(amount)="data">
          <span>{{ data.item.amount | formatMoney }}</span>
        </template>

        <template #cell(status)="{ item }">
          <div v-if="!item.is_edit">
            <b-badge :variant="getVariant(item.status)" class="status-badge">
              {{ item.status }}
            </b-badge>
            <feather-icon
              class="text-info"
              name="edit-2"
              :clickable="true"
              size="18"
              @click="item.is_edit = true"
            />
          </div>
          <div v-else>
            <change-sale-status
              :status="item.status_int"
              @change="(new_status) => changeStatus(new_status, item.id)"
            />
          </div>
        </template>

        <template #cell(actions)="data">
          <feather-icon
            class="text-primary"
            name="eye"
            :clickable="true"
            size="18"
            @click="openSaleDetail(data.item)"
            v-b-tooltip.hover.right="'Ver detalles'"
          />
        </template>

        <template #bottom-row>
          <b-th colspan="4" class="text-right py-0" v-if="status !== null">
            <div class="foot-totales" :style="{ color: '#000' }">
              <p class="m-0" style="font-weight: 600">SUBTOTAL</p>
              <p class="m-0" style="font-weight: 600">TOTAL</p>
            </div>
          </b-th>
          <b-th colspan="1" class="text-right py-0" v-if="status !== null">
            <div class="foot-totales" :style="{ color: '#000' }">
              {{ totals.total | formatMoney }}
              <br />
              {{ totals.subtotal | formatMoney }}
            </div>
          </b-th>
          <b-th colspan="2" v-if="status !== null" />
        </template>
      </b-table>
    </filter-slot>

    <register-sale
      v-if="showRegisterSaleModal"
      @close="showRegisterSaleModal = false"
      @reload="refreshTable"
    />

    <sale-detail
      v-if="showSaleDetailModal"
      :pSale="saleDetail"
      @close="showSaleDetailModal = false"
      @reload="refreshTable"
    />
  </div>
</template>

<script>
import fields from "./sales.fields";
import filters from "./sales.filters";

import RegisterSale from "./components/RegisterSale.vue";
import ChangeSaleStatus from "./components/ChangeSaleStatus.vue";
import SaleDetail from "./components/SaleDetail.vue";

export default {
  components: {
    RegisterSale,
    ChangeSaleStatus,
    SaleDetail,
  },
  data() {
    return {
      filterSlot: {
        filters,
        filterSearch: {
          type: "input",
          inputType: "text",
          placeholder: "Buscar por nombre de cliente",
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
      optionsYears: [],
      optionsMonths: [
        { id: 1, name: "Enero" },
        { id: 2, name: "Febrero" },
        { id: 3, name: "Marzo" },
        { id: 4, name: "Abril" },
        { id: 5, name: "Mayo" },
        { id: 6, name: "Junio" },
        { id: 7, name: "Julio" },
        { id: 8, name: "Agosto" },
        { id: 9, name: "Septiembre" },
        { id: 10, name: "Octubre" },
        { id: 11, name: "Noviembre" },
        { id: 12, name: "Diciembre" },
      ],
      year: new Date().getFullYear(),
      month: new Date().getMonth() + 1,
      filterStatusOptions: [
        { id: null, name: "VER TODO" },
        { id: 1, name: "PENDIENTE" },
        { id: 2, name: "CANCELADO" },
        { id: 3, name: "ANULADO" },
      ],
      status: null,
      items: [],
      showRegisterSaleModal: false,
      showSaleDetailModal: false,
      saleDetail: null,
    };
  },
  mounted() {
    this.getYears();
  },
  computed: {
    totals() {
      return {
        total: this.items.length > 0 ? Number(this.items[0].totalAmount) : 0,
        subtotal: this.items.reduce((a, b) => a + Number(b.amount), 0),
      };
    },
  },
  methods: {
    async getYears() {
      const today = new Date();
      const currentYear = today.getFullYear();
      const yearPrevious = [];

      for (let i = 0; i < 20; i++) {
        const lastYear = currentYear - i;
        if (lastYear == 2005) break;
        yearPrevious.push(lastYear);
      }

      this.optionsYears = yearPrevious.map((element) => ({
        id: element,
        name: "" + element,
      }));
    },
    async myProvider(ctx) {
      const sort_by = ctx.sortBy === "" ? "date" : ctx.sortBy;
      try {
        const { data } = await axios.post("/sales-list", {
          page: this.filterSlot.paginate.currentPage,
          perPage: this.filterSlot.paginate.perPage,
          search: this.filterSlot.filterSearch.model,
          status: this.status,
          year: this.year,
          month: this.month,
          orderBy: sort_by,
          orderDir: ctx.sortDesc ? "desc" : "asc",
        });
        this.filterSlot.paginate.currentPage = data.current_page;
        this.filterSlot.paginate.perPage = data.per_page;
        this.filterSlot.totalRows = data.total;
        this.filterSlot.startPage = data.from;
        this.filterSlot.toPage = data.to;
        this.items = data.data;
        return data.data;
      } catch (error) {
        console.log(error);
      }
    },
    getVariant(status) {
      const statusVariant = {
        CANCELADO: "success",
        ANULADO: "danger",
        PENDIENTE: "warning",
      };

      return statusVariant[status];
    },
    openRegisterSaleModal() {
      this.showRegisterSaleModal = true;
    },
    openSaleDetail(item) {
      this.saleDetail = item;
      this.showSaleDetailModal = true;
    },
    refreshTable() {
      this.$refs.salesTable.refresh();
    },
    async changeStatus(new_status, sale_id) {
      try {
        const data = await axios.post("/sales-update-status", {
          id: sale_id,
          new_status,
        });

        if (data.status == 200) {
          this.$toastr.success(data.data.message, "Éxito");
          this.refreshTable();
        } else {
          this.$toastr.error("Ha ocurrido un error al actualizar la venta");
        }
      } catch (error) {
        console.log(error);
      }
    },
    onChangeCurrentPage(e) {
      this.filterSlot.paginate.currentPage = e;
      this.refreshTable();
    },
    async generateMonthlyReport() {
      try {
        const response = await axios({
          url: "/sales-monthly-report",
          method: "POST",
          responseType: "blob",
          data: {
            year: this.year,
            month: this.month,
          },
        });

        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement("a");
        link.href = url;

        const month_text = this.getMonths(this.month);
        const fileName = `INVENTARIO_VILLARREAL_${month_text}_${this.year}.xlsx`;
        link.setAttribute("download", fileName);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        this.$toastr.success("Reporte descargado con éxito", "Éxito");
      } catch (error) {
        console.error(error);
        this.$toastr.error("Ha ocurrido un error al descargar el reporte");
      }
    },
    getMonths(month) {
      const names = {
        1: "Enero",
        2: "Febrero",
        3: "Marzo",
        4: "Abril",
        5: "Mayo",
        6: "Junio",
        7: "Julio",
        8: "Agosto",
        9: "Septiembre",
        10: "Octubre",
        11: "Noviembre",
        12: "Diciembre",
      };
      return names[month];
    }
  },
  watch: {
    year() {
      this.refreshTable();
    },
    month() {
      this.refreshTable();
    },
    status() {
      this.refreshTable();
    },
  },
};
</script>

<style>
.status-badge {
  min-width: 100px;
}
</style>