<template>
  <div>
    <div class="d-flex justify-content-start" style="gap: 10px">
      <span class="text-header">{{ isEdit ? 'Productos' : 'Agregar productos' }}</span>
      <feather-icon
        name="plus-circle"
        size="24"
        class="text-primary"
        :clickable="true"
        v-b-tooltip.hover.top="'Agregar producto'"
        @click="addProduct()"
      ></feather-icon>
    </div>
    <div class="products-form">
      <validation-observer ref="formProduct" class="products-list">
        <div v-for="(product, index) in products" :key="index">
          <b-row :style="{ backgroundColor: product.deleted ? '#f8d7da' : '' }">
            <b-col cols="5">
              <validation-provider
                name="product_name"
                rules="required"
                v-slot="{ errors }"
              >
                <b-form-group label="Nombre" label-for="name">
                  <v-select
                    :options="optionsProducts"
                    label="name"
                    :reduce="(option) => option.id"
                    :clearable="false"
                    v-model="product.name"
                    class="d-inline-block w-100"
                    :class="errors.length > 0 ? 'danger-selector' : ''"
                    @input="onChangeProduct(index, $event)"
                  />
                </b-form-group>
              </validation-provider>
            </b-col>
            <b-col cols="2">
              <b-form-group label="Precio unitario" label-for="unit_price">
                <money
                  v-model="product.unit_price"
                  id="unit_price"
                  v-bind="money"
                  class="form-control"
                  disabled
                ></money>
              </b-form-group>
            </b-col>
            <b-col cols="2">
              <validation-provider
                name="quantity"
                rules="required|min_value:1|integer"
                v-slot="{ errors }"
              >
                <b-form-group label="Cantidad" label-for="quantity">
                  <b-form-input
                    type="number"
                    id="quantity"
                    v-model="product.quantity"
                    min="1"
                    step="1"
                    :class="errors.length > 0 ? 'border-danger' : ''"
                    @input="onChangeQuantity(index)"
                  />
                </b-form-group>
              </validation-provider>
            </b-col>
            <b-col cols="2">
              <b-form-group label="Total" label-for="total">
                <money
                  v-model="product.total"
                  id="total"
                  v-bind="money"
                  class="form-control"
                  disabled
                ></money>
              </b-form-group>
            </b-col>
            <b-col cols="1">
              <feather-icon
                v-if="products.length > 1 || isEdit"
                :name="isEdit && product.deleted ? 'x' : 'trash'"
                size="24"
                class="text-danger"
                style="position: absolute; top: 40px"
                :clickable="true"
                v-b-tooltip.hover.top="
                  isEdit && product.deleted ? 'Restaurar' : 'Remover'
                "
                @click="removeProduct(index)"
              />
            </b-col>
          </b-row>
        </div>
      </validation-observer>
    </div>
  </div>
</template>

<script>
import { Money } from "v-money";

export default {
  props: {
    products: {
      type: Array,
      default: () => [],
    },
    isEdit: {
      type: Boolean,
      default: false,
    },
  },
  components: {
    Money,
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
      optionsProducts: [],
    };
  },
  computed: {
    totalAmount() {
      return this.products.reduce((a, b) => a + b.total, 0);
    },
  },
  created() {
    this.getProducts();
  },
  methods: {
    async addProduct() {
      this.products.unshift({
        id: null,
        name: null,
        unit_price: 0,
        quantity: 0,
        total: 0,
      });
    },
    async removeProduct(idx) {
      if (this.isEdit) {
        this.products[idx].deleted = !this.products[idx].deleted;
      } else {
        this.products.splice(idx, 1);
      }
    },
    async getProducts() {
      const { data } = await axios.get("/products-get");
      this.optionsProducts = data;
    },
    onChangeProduct(idx, event) {
      const product = this.optionsProducts.find((p) => p.id === event);
      this.products[idx].id = product.id;
      this.products[idx].name = product.name;
      this.products[idx].unit_price = product.unit_price;
      this.products[idx].quantity = 1;
      this.products[idx].total =
        this.products[idx].unit_price * this.products[idx].quantity;

      if (this.isEdit) {
        this.products[idx].modified = 1;
      }

      this.$emit("updateTotalAmount", this.totalAmount);
    },
    onChangeQuantity(idx) {
      this.products[idx].total =
        this.products[idx].unit_price * this.products[idx].quantity;

      if (this.isEdit) {
        this.products[idx].modified = 1;
      }

      this.$emit("updateTotalAmount", this.totalAmount);
    },
    async validateProductForm() {
      const validation = await this.$refs.formProduct.validate();
      return validation;
    },
  },
};
</script>

<style scoped>
.text-header {
  font-size: 1.25rem;
}

.danger-selector {
  border: 0.5px solid red;
  border-radius: 4px;
}

.products-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}
</style>