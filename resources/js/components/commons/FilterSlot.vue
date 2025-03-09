<template>
  <div>
    <b-card no-body :class="cardClass">
      <b-sidebar
        right
        backdrop
        bg-variant="white"
        sidebar-class="sidebar-lg"
        header-class="pt-1"
        :no-close-on-backdrop="backdropClose"
        :no-close-on-esc="backdropClose"
        lazy
        v-model="showSidebar"
        @hidden="closeSidebar"
      >
        <template #header>
          <div class="d-flex justify-content-between align-items-center w-100">
            <h5 style="margin: 0.25rem 0">Búsqueda avanzada</h5>
            <feather-icon
              style="cursor: pointer"
              name="x"
              size="18"
              @click="closeSidebar"
            />
          </div>
        </template>
        <b-container>
          <filters-component :filters="filters"></filters-component>
        </b-container>
        <template #footer>
          <b-container>
            <div class="d-flex justify-content-end">
              <b-button variant="secondary" @click="resetFilters"
                >Limpiar filtros</b-button
              >
              <b-button variant="primary" class="ml-2" @click="sideBarSearch"
                >Buscar
              </b-button>
            </div>
          </b-container>
        </template>
      </b-sidebar>
      <div class="d-flex justify-content-between align-items-center">
        <span class="text-muted">
          Mostrando {{ startPage }} a {{ toPage }} de {{ totalRows }} entradas
        </span>
        <b-pagination
          v-model="currentPage"
          :total-rows="totalRows"
          :per-page="perPage"
          first-number
          last-number
          @input="$emit('onChangeCurrentPage', $event)"
        ></b-pagination>
      </div>
      <div
        class="d-flex justify-content-between align-items-center"
        style="gap: 1rem"
      >
        <div>
          <label>Mostrando</label>
          <v-select
            v-model="paginate.perPage"
            :options="[10, 25, 50, 100]"
            :clearable="false"
            class="per-page-selector d-inline-block mx-50"
          />
          <label class="mr-2">registros</label>
          <feather-icon
            name="refresh-ccw"
            size="20"
            :clickable="true"
            @click="$emit('reload')"
          />
          <slot name="buttons"></slot>
        </div>
        <div class="d-flex justify-content-between align-items-center">
          <b-input-group style="min-width: 400px">
            <b-form-input
              :type="filterPrincipal.inputType"
              :placeholder="filterPrincipal.placeholder"
              v-model="filterPrincipal.model"
              @keyup.enter="$emit('reload')"
            />
            <b-input-group-append>
              <b-button variant="primary" @click="$emit('reload')">
                <feather-icon name="search" :clickable="true" />
              </b-button>
            </b-input-group-append>
          </b-input-group>
          <b-button
            v-if="filters.length > 0"
            class="ml-2"
            @click="openSidebar"
            v-b-tooltip.hover.top="'Mostrar filtros'"
            ><i class="fa-solid fa-filter"
          /></b-button>
        </div>
      </div>
      <div class="table-responsive mt-1">
        <slot name="table" />
      </div>
    </b-card>
  </div>
</template>

<script>
import FiltersComponent from "./FiltersComponent.vue";
import vSelect from "vue-select";

export default {
  name: "FilterSlot",
  props: {
    filters: { required: true, type: Array, default: () => [] },
    backdropClose: { required: false, default: false },
    cardClass: { required: false, type: String, default: "mb-1 border-0 p-0" },
    paginate: { required: false, type: Object },
    currentPage: { required: false, type: Number, default: 1 },
    perPage: { required: false, type: Number, default: 10 },
    totalRows: { required: false, type: Number },
    startPage: { required: false, type: Number },
    toPage: { required: false, type: Number },
    filterPrincipal: { required: false, type: Object },
  },
  components: {
    FiltersComponent,
    vSelect,
  },
  data() {
    return {
      showSidebar: false,
    };
  },
  created() {
    this.filterPrincipal.model = "";
  },
  methods: {
    openSidebar() {
      this.showSidebar = true;
    },
    closeSidebar() {
      this.showSidebar = false;
    },
    resetFilters() {
      console.log("reset filters");
    },
    sideBarSearch() {
      this.showSidebar = false;
    },
  },
};
</script>
