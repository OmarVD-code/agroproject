<template>
  <div>
    <b-modal
      v-model="show"
      ref="modalName"
      modal-class="modal-primary"
      title="DETALLE DE VENTA"
      size="xl"
      @hidden="onCancel"
      scrollable
      no-close-on-backdrop
      no-close-on-esc
    >
      <validation-observer ref="formSale">
        <b-row>
          <b-col cols="6">
            <validation-provider
              name="client"
              rules="required"
              v-slot="{ errors }"
            >
              <b-form-group label="Cliente">
                <v-select
                  :options="optionsClients"
                  label="name"
                  :reduce="(option) => option.id"
                  :clearable="false"
                  v-model="sale.client_id"
                  class="d-inline-block w-100"
                  :class="errors.length > 0 ? 'danger-selector' : ''"
                />
              </b-form-group>
            </validation-provider>
          </b-col>
          <b-col cols="6">
            <validation-provider
              name="address"
              rules="required"
              v-slot="{ errors }"
            >
              <label>Dirección</label>
              <div class="d-flex">
                <v-select
                  :options="optionsAddresses"
                  label="name"
                  :reduce="(option) => option.id"
                  :clearable="false"
                  v-model="sale.address_id"
                  class="d-inline-block w-100"
                  :class="errors.length > 0 ? 'danger-selector' : ''"
                />
                <feather-icon
                  name="plus-circle"
                  size="24"
                  class="ml-2 mt-1 text-primary"
                  :clickable="true"
                  v-b-tooltip.hover.top="'Agregar dirección'"
                  @click="openRegisterAddressModal()"
                />
              </div>
            </validation-provider>
          </b-col>
          <b-col cols="4">
            <validation-provider
              name="code"
              rules="required"
              v-slot="{ errors }"
            >
              <b-form-group label="Código">
                <b-form-input
                  v-model="sale.code"
                  required
                  :class="errors.length > 0 ? 'danger-selector' : ''"
                />
              </b-form-group>
            </validation-provider>
          </b-col>
          <b-col cols="4">
            <validation-provider
              name="date"
              rules="required"
              v-slot="{ errors }"
            >
              <b-form-group label="Fecha">
                <b-form-datepicker
                  v-model="sale.date"
                  :date-format-options="{
                    day: 'numeric',
                    month: 'numeric',
                    year: 'numeric',
                  }"
                  locale="es"
                  :class="errors.length > 0 ? 'border-danger' : ''"
                  placeholder=""
                />
              </b-form-group>
            </validation-provider>
          </b-col>
          <b-col cols="4">
            <validation-provider
              name="amount"
              rules="required|min_value:0.01"
              v-slot="{ errors }"
            >
              <b-form-group label="Monto total" label-for="amount">
                <money
                  v-model="sale.amount"
                  v-bind="money"
                  class="form-control"
                  :class="
                    errors.length > 0 && hideValidator ? 'border-danger' : ''
                  "
                  disabled
                />
              </b-form-group>
            </validation-provider>
          </b-col>
        </b-row>
      </validation-observer>
      <template #modal-footer>
        <b-button variant="primary" @click="onOk">Guardar</b-button>
      </template>

      <!-- AGREGAR PRODUCTOS -->
      <add-sale-product
        ref="addSaleProduct"
        :products="products"
        :isEdit="true"
        @updateTotalAmount="updateTotalAmount"
      />

      <!-- AGREGAR DIRECCIÓN -->
      <register-address
        v-if="showRegisterAddressModal"
        @close="closeRegisterAddressModal"
        @reload="(id) => selectAddress(id)"
      />
    </b-modal>
  </div>
</template>
<script>
import { Money } from "v-money";
import AddSaleProduct from "./AddSaleProduct.vue";
import FeatherIcon from "../../commons/FeatherIcon.vue";
import RegisterAddress from "./RegisterAddress.vue";

export default {
  props: {
    pSale: {
      type: Object,
      default: null,
      required: false,
    },
  },
  components: {
    Money,
    AddSaleProduct,
    FeatherIcon,
    RegisterAddress,
  },
  data() {
    return {
      show: false,
      sale: {
        client_id: null,
        address_id: null,
        code: null,
        date: null,
        amount: 0,
      },
      money: {
        decimal: ",",
        thousands: ".",
        prefix: "S/. ",
        suffix: "",
        precision: 2,
        masked: false,
      },
      optionsClients: [],
      optionsAddresses: [],
      hideValidator: false,
      showRegisterAddressModal: false,
      products: [],
    };
  },
  async created() {
    this.setDate();
    this.getCustomers();
    this.getAddresses();
    this.show = true;
    this.sale = { ...this.pSale };
    this.products = await this.getSaleDetails();

    console.log(this.products);
  },
  methods: {
    async onOk() {
      const validateProducts =
        await this.$refs.addSaleProduct.validateProductForm();
      const validateSale = await this.$refs.formSale.validate();
      if (!validateProducts || !validateSale) {
        return;
      }

      try {
        const payload = {
          sale_id: this.sale.id,
          client_id: this.sale.client_id,
          address_id: this.sale.address_id,
          code: this.sale.code,
          date: this.sale.date,
          amount: this.sale.amount,
          products: this.products,
        };

        const data = await axios.post("/sales-update", payload);

        if (data.status == 200) {
          this.show = false;
          this.$toastr.success(data.data.message, "Éxito");
          this.$emit("reload");
        } else {
          this.$toastr.error("Ha ocurrido un error al registrar la venta");
        }
      } catch (error) {
        console.log(error);
      }
    },
    setDate() {
      const today = new Date();
      const year = today.getFullYear();
      const month = String(today.getMonth() + 1).padStart(2, "0");
      const day = String(today.getDate()).padStart(2, "0");
      this.sale.date = `${year}-${month}-${day}`;
    },
    updateTotalAmount(value) {
      this.sale.amount = value;
    },
    getCustomers() {
      axios.get("/customers-get").then((response) => {
        this.optionsClients = response.data;
      });
    },
    async selectAddress(id) {
      await this.getAddresses();
      this.sale.address_id = id;
    },
    async getAddresses() {
      await axios.get("/address-list").then((response) => {
        this.optionsAddresses = response.data;
      });
    },
    async getSaleDetails() {
      const { data } = await axios.post("/sales-details", {
        sale_id: this.pSale.id,
      });

      return data;
    },
    closeModal() {
      this.$emit("close");
    },
    onCancel() {
      this.show = false;
      this.$emit("close");
    },
    openRegisterAddressModal() {
      this.showRegisterAddressModal = true;
    },
    closeRegisterAddressModal() {
      this.showRegisterAddressModal = false;
    },
  },
};
</script>

<style scoped>
.danger-selector {
  border: 0.5px solid red;
  border-radius: 4px;
}
</style>