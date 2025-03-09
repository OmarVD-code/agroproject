<template>
  <div>
    <b-modal
      title="REGISTRAR PRODUCTO"
      v-model="show"
      @hidden="onCancel"
      size="xmd"
      no-close-on-backdrop
      no-close-on-esc
    >
      <validation-observer ref="formProduct">
        <b-row>
          <b-col cols="12">
            <validation-provider
              name="product_name"
              rules="required"
              v-slot="{ errors }"
            >
              <b-form-group label="Nombre" label-for="name">
                <b-form-input
                  id="name"
                  v-model="product.name"
                  required
                  autofocus
                  :class="errors.length > 0 ? 'border-danger' : ''"
                />
              </b-form-group>
            </validation-provider>
          </b-col>
          <b-col cols="4">
            <validation-provider
              name="unit_price"
              rules="required|min_value:0.01"
              v-slot="{ errors }"
            >
              <b-form-group label="Precio unitario" label-for="unit_price">
                <money
                  v-model="product.unit_price"
                  v-bind="money"
                  class="form-control"
                  :class="
                    errors.length > 0 && hideValidator ? 'border-danger' : ''
                  "
                ></money>
              </b-form-group>
            </validation-provider>
          </b-col>
          <b-col cols="4">
            <validation-provider
              name="stock"
              rules="required|min_value:1|integer"
              v-slot="{ errors }"
            >
              <b-form-group label="Stock actual" label-for="stock">
                <b-form-input
                  type="number"
                  id="stock"
                  v-model="product.stock"
                  required
                  min="0"
                  step="1"
                  :class="
                    errors.length > 0 && hideValidator ? 'border-danger' : ''
                  "
                />
              </b-form-group>
            </validation-provider>
          </b-col>
          <b-col cols="4">
            <validation-provider
              name="supplier"
              rules="required"
              v-slot="{ errors }"
            >
              <b-form-group label="Proveedor" label-for="supplier">
                <b-form-select
                  v-model="product.supplier_id"
                  :options="suppliers"
                  required
                  :class="errors.length > 0 ? 'border-danger' : ''"
                />
              </b-form-group>
            </validation-provider>
          </b-col>
        </b-row>
      </validation-observer>
      <template #modal-footer>
        <b-button variant="primary" @click="onOk">{{
          isEdit ? "Editar" : "Registrar"
        }}</b-button>
      </template>
    </b-modal>
  </div>
</template>

<script>
import { Money } from "v-money";

export default {
  components: {
    Money,
  },
  props: {
    productEdit: {
      type: Object,
      default: null,
      required: false,
    },
  },
  data() {
    return {
      money: {
        decimal: ",",
        thousands: ".",
        prefix: "S/. ",
        suffix: "",
        precision: 2,
        masked: false,
      },
      show: false,
      product: {
        id: null,
        name: null,
        category: null,
        unit_price: 0,
        stock: 0,
        supplier_id: null,
        image: null,
      },
      suppliers: [],
      hideValidator: false,
    };
  },
  async created() {
    await this.getSuppliers();
    this.show = true;
    if (this.isEdit) {
      this.product = { ...this.productEdit };
    }
  },
  computed: {
    isEdit() {
      return this.productEdit !== null;
    },
  },
  methods: {
    async getSuppliers() {
      const { data } = await axios.get("/suppliers-list");
      this.suppliers = data;
    },
    async onOk() {
      if (this.isEdit) {
        await this.editProduct();
      } else {
        await this.addProduct();
      }
    },
    async addProduct() {
      try {
        this.hideValidator = true;
        const validation = await this.$refs.formProduct.validate();
        if (!validation) {
          return;
        }
        const data = await axios.post("/products-add", this.product);

        if (data.status == 200) {
          this.show = false;
          this.$toastr.success(data.data.message, "Éxito");
          this.$emit("reload");
        } else {
          this.$toastr.error("Ha ocurrido un error al registrar el producto");
        }
      } catch (error) {
        console.log(error);
      }
    },
    async editProduct() {
      try {
        this.hideValidator = true;
        const validation = await this.$refs.formProduct.validate();
        if (!validation) {
          return;
        }
        const data = await axios.post("/products-edit", this.product);

        if (data.status == 200) {
          this.show = false;
          this.$toastr.success(data.data.message, "Éxito");
          this.$emit("reload");
        } else {
          this.$toastr.error("Ha ocurrido un error al actualizar el producto");
        }
      } catch (error) {
        console.log(error);
      }
    },
    onCancel() {
      this.show = false;
      this.$emit("close");
    },
  },
};
</script>

<style scoped>
</style>
